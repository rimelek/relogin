<?php
/**************************************************************************
* R.E. login (1.8.1) - login_class.php                                    *
* ======================================================================= *
* A loginrendszer fõ fájlja                                               *
* ======================================================================= *
* Ezt a programot a PHP Studio fejlesztette, a szerzõk: / This program    *
* was developed by the PHP Studio, the authors:                           *
* Rimelek                                                                 *
* ----------------------------------------------------------------------- *
* Weboldalunk / Our webpage: http://www.phpstudio.hu                      *
* Segítségnyújtás / HelpDesk: http://forum.phpstudio.hu                   *
* Kapcsolat / Contact: php.prog@hotmail.com                               *
* ======================================================================= *
* Ez a program license alatt áll, amit itt tekinthetsz meg: / This        *
* program is under a license, which you can see here:                     *
* http://license.phpstudio.hu                                             *
* ----------------------------------------------------------------------- *
* A license-szel kapcsolatos észrevételeid, megjegyzéseid, kérdéseid  a   *
* license@phpstudio.hu e-mail címen várjuk.                               *
* ----------------------------------------------------------------------- *
* You can send your remarks, opinions, questions to the following e-mail  *
* address: license@phpstudio.hu                                           *
* ======================================================================= *
* Dátum / Date:   2010.02.07.                                             *
**************************************************************************/
ob_start();
session_start();


//Beállítjuk a bejelentkezési ûrlapból érkezõ adatokat
if(isset($_POST['login']))
{
	$_SESSION['usr'] = $_POST['user'];
	$_SESSION['psw'] = md5($_POST['pass']);
}

if (!isset($_SESSION['usr']))
{
	$_SESSION['usr'] = '';
}

if (!isset($_SESSION['psw']))
{
	$_SESSION['psw'] = '';
}


class Re_login
{
    //Adatbázis változók
    var $dbhost;
    var $dbuser;
    var $dbpass;
    var $dbname;

    //Jelenlegi idõ
    var $time;
    
    //Felhasználói változók
    var $userid;
    var $jog;
    var $mh_szam;
    var $mail_aktiv_true;

    //Admin változók
    var $reg_block;
    var $login_block;

    //adatbázis kapcsolat létrehozása
    function re_sql($dbhost, $dbuser, $dbpass, $dbname)
    {
        $_SESSION['belep'] = false;

        $kapcsolat = @mysql_connect($dbhost, $dbuser, $dbpass)
            or die("Nem tudok kapcsolódni a mysql-hez");
        $db = @mysql_select_db($dbname)
            or die("Nem tudok kapcsolódni az adatbázishoz");
        mysql_query("set names LATIN2");

        //fontos változók, függvények
        $this->re_kilep();
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;
        //\\//\\//\\/\\//\\//\\//\\
        $this->re_adatok();
        $this->ip_ir();
        $this->browser_ir();
    }
    
    //Adatok átadása objektum tulajsonságoknak
    function re_adatok()
    {
        $login_var_sql = "select * from re_login where user = '".mysql_real_escape_string($_SESSION['usr'])."' limit 1";
        $login_var_query = @mysql_query($login_var_sql);
        $login_var = @mysql_fetch_assoc($login_var_query);
        //\\//\\//\\//\\//\\//\\//\\

        //felasználó joga
        $this->jog = $login_var['jog'];
        //felhasználó id -je
        $this->userid = $login_var['id'];
        //felhasználó meghívóinak száma
        $this->mh_szam = $login_var['mh'];
        //felhasználónak aktiválva van-e a mail-je
        $mailsql = "select mail,uj_mail from re_data where id = '$this->userid'";
        $mailquery = mysql_query($mailsql);
        $mailaktiv = mysql_fetch_assoc($mailquery);
        $this->mail_aktiv_true = ($mailaktiv['mail'] == $mailaktiv['uj_mail']) ? true : false;
        //Regisztráció, és bejelenkezés blokkolva van-e
        $admin_sql = "select * from re_admin limit 1";
        $admin_query = mysql_query($admin_sql);
        $fetch = mysql_fetch_assoc($admin_query);

        $this->reg_block = $fetch['reg_block'];
        $this->login_block = $fetch['login_block'];
    }
    
    //user ellenõrzése
    function re_user($user)
    {
		$user = mysql_real_escape_string($user);
        //Megvizsgáljuk, létezik-e a megadott név már.
        $sqlkod = "select * from re_login where user = '$user'";
        $query = mysql_query($sqlkod);
        $login_user = mysql_fetch_assoc($query);
        { return $login_user; }
    }
    //belépés, ha jelszó is jó
    function re_belep()
    {
        $pass = $_SESSION['psw'];

        if($this->re_user($_SESSION['usr']) == true)
        {
            $re_user = $this->re_user($_SESSION['usr']);
            $user = $re_user['user'];
            $sqlkod = "select * from re_login where user = '$user' and pass = '$pass'";
            $query = mysql_query($sqlkod);
            $login_pass = mysql_fetch_assoc($query);
            return $login_pass;
        }
    }
    //Tiltott -e a belépni próbáló tag
    function tiltott_nick()
    {
        global $ijel, $index_url;
        if($_SESSION['usr'] != NULL){
            //Ha tiltott, nem léphet be
            if($this->jog == "x"){
                session_destroy();
                session_unset();
                header("Location: $index_url{$ijel}re_error=2");
            }
        }
    }
    
    //nem lehet két azonos mail cim az adatbázisban
    function tiltott_mail($mail)
    {
        $sql = "select * from re_data where mail = '$mail'";
        $query = mysql_query($sql);
        $sor = mysql_fetch_assoc($query);
        return $sor;
    }
    
    //le van e blokkolva a bejelenkezés
    function login_zar()
    {
        global $ijel, $index_url, $super_admin;
        if($_SESSION['usr'] != NULL){
            //Ha blokkolva van, nem engedjük belépni a tagot
            if($this->login_block == "1" and $super_admin != $_SESSION['usr']){
                session_destroy();
                session_unset();
                header("Location: $index_url{$ijel}re_error=3");
            }
        }
    }
    //kilépés
    function re_kilep()
    {
        if(isset($_GET['exit']))
        {
            global $index_url;
            
            $this->status_ir();

            session_destroy();
            session_unset();
            header("Location: $index_url");
        }
    }
    
    //átirányítás
    function atiranyit()
    {
        global $index_url, $reg_form,$ijel,$nopass;
		if(!isset($nopass) or !is_array($nopass)) {$nopass = array();}
        //Az index oldalról és a regisztrációs oldalról ne irányítson vissza
        if(($index_url != $_SERVER['PHP_SELF'])
        and ($reg_form != $_SERVER['PHP_SELF'])
        and ('mail_aktiv.php' != end(explode('/',$_SERVER['PHP_SELF'])))
		and (!in_array( end(explode('/',$_SERVER['PHP_SELF'])),$nopass)))
        {

            header("Location: $index_url{$ijel}re_error=1");
        }
    }

    //Ip beírása adatbázisba
    function ip_ir()
    {   $ip = (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        ? $_SERVER['HTTP_X_FORWARDED_FOR']
        : $_SERVER['REMOTE_ADDR'];

        $ip     = $_SERVER['REMOTE_ADDR'];
        $ip_sql = "update re_login set ip = '$ip' where id = '$this->userid'";
        mysql_query($ip_sql);
    }

    //Böngészõ adatok beírása adatbázisba
    function browser_ir()
    {
        $browser     = $_SERVER['HTTP_USER_AGENT'];
        $browser_sql = "update re_login set browser = '$browser' where id = '$this->userid'";
        mysql_query($browser_sql);
    }
    
    //Online van-e a tag, vagy nem
    function status_ir()
    {
        global $max_ido;
        
        $max_sec = $max_ido * 60;
        
        $status = 1;
        $userid = $this->userid;
        if(isset($_GET['exit'])) { $status = "0"; }

        $max_time = $this->time - $max_sec;

        //státuszt 0-ra állítja, ha $max_sec másodperce nem frissitett
        $itt_sql = "update re_login set status = '0' where (frissites < '$max_time')";
        mysql_query($itt_sql);

        //státust beállítása kilépéskor, és belépéskor
        $onl_sql = "update re_login set status = '$status' where id = '$userid'";
        mysql_query($onl_sql);
    }
    //belépés ideje
    function belep_ido()
    {
        global $index_url;
        $belep_sql = "update re_login set belepes = '$this->time' where id = '$this->userid'";
        mysql_query($belep_sql);
        header("Location: $index_url");
    }
    //frissítés ideje
    function friss_ido()
    {
        $friss_sql = "update re_login set frissites = '$this->time' where id = '$this->userid'";
        mysql_query($friss_sql);
        
    }
    //online idõ beírása
    function online_ido()
    {
        global $max_ido;
        $max_sec = $max_ido * 60;

        //lekérdezzük az adatait
        $onl_sql = "select frissites, online,status from re_login where id = '$this->userid'";
        $onl_query = mysql_query($onl_sql);
        $onl_sor = mysql_fetch_assoc($onl_query);

        //kiszámoljuk a legutóbbi frissítés óta eltelt idõt
        $onl_plusz = $this->time - $onl_sor['frissites'];
        //Hozzáadjuk ezt az idõt a jelenlegi onlineidõhöz
        $onl_beir = $onl_plusz + $onl_sor['online'];

        //Annak megadása, mennyi idõ után ne növelje az online idõt,
        //ha azóta nem frissített a tag
        $max_time = $this->time - $max_sec;

        //Új online idõ beírása
        if($onl_sor['frissites'] > $max_time){

            $new_onl_sql = "update re_login set online = '$onl_beir' where id = '$this->userid'";
            mysql_query($new_onl_sql);
        }else{
            $ujra_beleptet = "update re_login set belepes = '$this->time' where id = '$this->userid'";
            mysql_query($ujra_beleptet);
        }
    }
    //Az e-mail aktiváló kód létrehozása
    function re_code($mail,$id,$pass)
    {
        return md5(sha1($pass.$mail)).$id;
    }

    //Az aktivációs mail elküldése
    function mail_aktiv_kuld($mail,$id)
    {
        global $from,$login_mappa,$aldomain;
        $login_mappa = trim($login_mappa,"/");
        $pass_sql = "select pass from re_login where id = '$id'";
        $pass_query = mysql_query($pass_sql);
        $pass = mysql_fetch_assoc($pass_query);
        
        $code = $this->re_code($mail,$id,$pass['pass']);
        
        $subject = "E-mail aktiváció";
        $msg     = "Az alábbi linkre kattintva aktiválhatod".
                   " e-mail címed rendszerünkben:\n".
                   "http://$_SERVER[HTTP_HOST]/{$aldomain}$login_mappa/moduls/mail_aktiv.php?code=$code";
		$subject = "=?ISO-8859-2?B?".base64_encode($subject)."?=";
		
        $mail_to = @mail($mail,$subject,$msg, "From: ".$from.PHP_EOL.
				"Content-type: text/plain; charset=iso-8859-2");
        if(!$mail_to) { return false; }
        
        $sql = "update re_data set uj_mail = '$mail' where id = '$id' ";
        mysql_query($sql);
        return true;
    }
    
    //A regisztráció kérelem törlése,
    //amennyiben nem aktiválta a mail címét adott idõn belül
    function reg_del($time_limit)
    {
        $time = time() - $time_limit;
        $sql1 = "
        select * from re_data,re_login where
        re_login.id = re_data.id and
        re_login.belepes = '0' and
        re_login.regido < '$time' and
        re_data.mail != re_data.uj_mail
        ";
        $query1 = mysql_query($sql1);
        while($sor = mysql_fetch_assoc($query1))
        {
            $torol[] = " id = '".$sor['id']."' ";
        }
        
        $torol1 = @implode(' or ',$torol);
        $sql2 = "delete from re_login where $torol1";
        $sql3 = "delete from re_data where $torol1";
        if($torol1) {
            mysql_query($sql2);
            mysql_query($sql3);
        }
        
    }
    
    //A régi mail cím visszaállítása, amenyiben a változtatást követõen
    //adott idõn belül nem lett aktiválva az új
    function mail_vissza($time_limit)
    {
        $time = time() - $time_limit;
        $sql1 = "
        select * from re_data, re_login where
        re_login.id = re_data.id and
        re_login.frissites != '0' and
        re_login.frissites < '$time' and
        re_data.mail != re_data.uj_mail
        ";
        $query1 = mysql_query($sql1);
        while($sor1 = mysql_fetch_assoc($query1))
        {
            $vissza[] = " id = '".$sor1['id']."' ";
        }
        $vissza1 = @implode(' or ',$vissza);
        $sql = "
        update re_data set uj_mail = mail where $vissza1
        ";
        if($vissza1){ mysql_query($sql); }
    }
    //e-mail cím ellenõrzõ függvény
    function check_mail_msn($mail_msn)
    {
        eregi("^[a-z0-9_.-]+@[a-z0-9_.-]+$", $mail_msn, $kimenet);
        if(trim($mail_msn) != $kimenet[0]) return false;
        return true;
    }
    
}



//Url vizsgálata a rugalmas felhasználhatóságért
$ijel = (eregi("\?", $index_url, $kimenet) != NULL) ? "&" : "?";


$login = new Re_login;
$login->time = time();
$login->re_sql($dbhost, $dbuser, $dbpass, $dbname);

//Ha a bejelentkezési kérelem megtörtént
if($_SESSION['usr'] != NULL && $_SESSION['psw'] != NULL){
    //Ha nem jó a felhasználónév, vagy jelszó, nem léphet be,
   if($login->re_belep() == false)
    {
        session_destroy();
        session_unset();
        header("Location: $index_url{$ijel}re_error=1");
    }elseif(!$login->mail_aktiv_true and $_SERVER['PHP_SELF'] != $reg_form){
        session_destroy();
        session_unset();
        header("Location: $index_url{$ijel}re_error=4");
    }else
    {
        //ha beléphet beírjuk online listára
        $_SESSION['belep'] = true;
        $login->status_ir();

        //Ha most lépett be, beírjuk a belépés idejét
        if(isset($_POST['login']))
        {
            $login->belep_ido();
        }
    }
}else
{
    $login->atiranyit();
}
$login->reg_del(24*60*60);
$login->mail_vissza(24*60*60);
$login->login_zar();
$login->tiltott_nick();
$login->online_ido();
$login->friss_ido();

?>
