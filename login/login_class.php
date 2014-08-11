<?php
/**************************************************************************
* R.E. login (1.8.1) - login_class.php                                    *
* ======================================================================= *
* A loginrendszer f� f�jlja                                               *
* ======================================================================= *
* Ezt a programot a PHP Studio fejlesztette, a szerz�k: / This program    *
* was developed by the PHP Studio, the authors:                           *
* Rimelek                                                                 *
* ----------------------------------------------------------------------- *
* Weboldalunk / Our webpage: http://www.phpstudio.hu                      *
* Seg�ts�gny�jt�s / HelpDesk: http://forum.phpstudio.hu                   *
* Kapcsolat / Contact: php.prog@hotmail.com                               *
* ======================================================================= *
* Ez a program license alatt �ll, amit itt tekinthetsz meg: / This        *
* program is under a license, which you can see here:                     *
* http://license.phpstudio.hu                                             *
* ----------------------------------------------------------------------- *
* A license-szel kapcsolatos �szrev�teleid, megjegyz�seid, k�rd�seid  a   *
* license@phpstudio.hu e-mail c�men v�rjuk.                               *
* ----------------------------------------------------------------------- *
* You can send your remarks, opinions, questions to the following e-mail  *
* address: license@phpstudio.hu                                           *
* ======================================================================= *
* D�tum / Date:   2010.02.07.                                             *
**************************************************************************/
ob_start();
session_start();


//Be�ll�tjuk a bejelentkez�si �rlapb�l �rkez� adatokat
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
    //Adatb�zis v�ltoz�k
    var $dbhost;
    var $dbuser;
    var $dbpass;
    var $dbname;

    //Jelenlegi id�
    var $time;
    
    //Felhaszn�l�i v�ltoz�k
    var $userid;
    var $jog;
    var $mh_szam;
    var $mail_aktiv_true;

    //Admin v�ltoz�k
    var $reg_block;
    var $login_block;

    //adatb�zis kapcsolat l�trehoz�sa
    function re_sql($dbhost, $dbuser, $dbpass, $dbname)
    {
        $_SESSION['belep'] = false;

        $kapcsolat = @mysql_connect($dbhost, $dbuser, $dbpass)
            or die("Nem tudok kapcsol�dni a mysql-hez");
        $db = @mysql_select_db($dbname)
            or die("Nem tudok kapcsol�dni az adatb�zishoz");
        mysql_query("set names LATIN2");

        //fontos v�ltoz�k, f�ggv�nyek
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
    
    //Adatok �tad�sa objektum tulajsons�goknak
    function re_adatok()
    {
        $login_var_sql = "select * from re_login where user = '".mysql_real_escape_string($_SESSION['usr'])."' limit 1";
        $login_var_query = @mysql_query($login_var_sql);
        $login_var = @mysql_fetch_assoc($login_var_query);
        //\\//\\//\\//\\//\\//\\//\\

        //felaszn�l� joga
        $this->jog = $login_var['jog'];
        //felhaszn�l� id -je
        $this->userid = $login_var['id'];
        //felhaszn�l� megh�v�inak sz�ma
        $this->mh_szam = $login_var['mh'];
        //felhaszn�l�nak aktiv�lva van-e a mail-je
        $mailsql = "select mail,uj_mail from re_data where id = '$this->userid'";
        $mailquery = mysql_query($mailsql);
        $mailaktiv = mysql_fetch_assoc($mailquery);
        $this->mail_aktiv_true = ($mailaktiv['mail'] == $mailaktiv['uj_mail']) ? true : false;
        //Regisztr�ci�, �s bejelenkez�s blokkolva van-e
        $admin_sql = "select * from re_admin limit 1";
        $admin_query = mysql_query($admin_sql);
        $fetch = mysql_fetch_assoc($admin_query);

        $this->reg_block = $fetch['reg_block'];
        $this->login_block = $fetch['login_block'];
    }
    
    //user ellen�rz�se
    function re_user($user)
    {
		$user = mysql_real_escape_string($user);
        //Megvizsg�ljuk, l�tezik-e a megadott n�v m�r.
        $sqlkod = "select * from re_login where user = '$user'";
        $query = mysql_query($sqlkod);
        $login_user = mysql_fetch_assoc($query);
        { return $login_user; }
    }
    //bel�p�s, ha jelsz� is j�
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
    //Tiltott -e a bel�pni pr�b�l� tag
    function tiltott_nick()
    {
        global $ijel, $index_url;
        if($_SESSION['usr'] != NULL){
            //Ha tiltott, nem l�phet be
            if($this->jog == "x"){
                session_destroy();
                session_unset();
                header("Location: $index_url{$ijel}re_error=2");
            }
        }
    }
    
    //nem lehet k�t azonos mail cim az adatb�zisban
    function tiltott_mail($mail)
    {
        $sql = "select * from re_data where mail = '$mail'";
        $query = mysql_query($sql);
        $sor = mysql_fetch_assoc($query);
        return $sor;
    }
    
    //le van e blokkolva a bejelenkez�s
    function login_zar()
    {
        global $ijel, $index_url, $super_admin;
        if($_SESSION['usr'] != NULL){
            //Ha blokkolva van, nem engedj�k bel�pni a tagot
            if($this->login_block == "1" and $super_admin != $_SESSION['usr']){
                session_destroy();
                session_unset();
                header("Location: $index_url{$ijel}re_error=3");
            }
        }
    }
    //kil�p�s
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
    
    //�tir�ny�t�s
    function atiranyit()
    {
        global $index_url, $reg_form,$ijel,$nopass;
		if(!isset($nopass) or !is_array($nopass)) {$nopass = array();}
        //Az index oldalr�l �s a regisztr�ci�s oldalr�l ne ir�ny�tson vissza
        if(($index_url != $_SERVER['PHP_SELF'])
        and ($reg_form != $_SERVER['PHP_SELF'])
        and ('mail_aktiv.php' != end(explode('/',$_SERVER['PHP_SELF'])))
		and (!in_array( end(explode('/',$_SERVER['PHP_SELF'])),$nopass)))
        {

            header("Location: $index_url{$ijel}re_error=1");
        }
    }

    //Ip be�r�sa adatb�zisba
    function ip_ir()
    {   $ip = (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        ? $_SERVER['HTTP_X_FORWARDED_FOR']
        : $_SERVER['REMOTE_ADDR'];

        $ip     = $_SERVER['REMOTE_ADDR'];
        $ip_sql = "update re_login set ip = '$ip' where id = '$this->userid'";
        mysql_query($ip_sql);
    }

    //B�ng�sz� adatok be�r�sa adatb�zisba
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

        //st�tuszt 0-ra �ll�tja, ha $max_sec m�sodperce nem frissitett
        $itt_sql = "update re_login set status = '0' where (frissites < '$max_time')";
        mysql_query($itt_sql);

        //st�tust be�ll�t�sa kil�p�skor, �s bel�p�skor
        $onl_sql = "update re_login set status = '$status' where id = '$userid'";
        mysql_query($onl_sql);
    }
    //bel�p�s ideje
    function belep_ido()
    {
        global $index_url;
        $belep_sql = "update re_login set belepes = '$this->time' where id = '$this->userid'";
        mysql_query($belep_sql);
        header("Location: $index_url");
    }
    //friss�t�s ideje
    function friss_ido()
    {
        $friss_sql = "update re_login set frissites = '$this->time' where id = '$this->userid'";
        mysql_query($friss_sql);
        
    }
    //online id� be�r�sa
    function online_ido()
    {
        global $max_ido;
        $max_sec = $max_ido * 60;

        //lek�rdezz�k az adatait
        $onl_sql = "select frissites, online,status from re_login where id = '$this->userid'";
        $onl_query = mysql_query($onl_sql);
        $onl_sor = mysql_fetch_assoc($onl_query);

        //kisz�moljuk a legut�bbi friss�t�s �ta eltelt id�t
        $onl_plusz = $this->time - $onl_sor['frissites'];
        //Hozz�adjuk ezt az id�t a jelenlegi onlineid�h�z
        $onl_beir = $onl_plusz + $onl_sor['online'];

        //Annak megad�sa, mennyi id� ut�n ne n�velje az online id�t,
        //ha az�ta nem friss�tett a tag
        $max_time = $this->time - $max_sec;

        //�j online id� be�r�sa
        if($onl_sor['frissites'] > $max_time){

            $new_onl_sql = "update re_login set online = '$onl_beir' where id = '$this->userid'";
            mysql_query($new_onl_sql);
        }else{
            $ujra_beleptet = "update re_login set belepes = '$this->time' where id = '$this->userid'";
            mysql_query($ujra_beleptet);
        }
    }
    //Az e-mail aktiv�l� k�d l�trehoz�sa
    function re_code($mail,$id,$pass)
    {
        return md5(sha1($pass.$mail)).$id;
    }

    //Az aktiv�ci�s mail elk�ld�se
    function mail_aktiv_kuld($mail,$id)
    {
        global $from,$login_mappa,$aldomain;
        $login_mappa = trim($login_mappa,"/");
        $pass_sql = "select pass from re_login where id = '$id'";
        $pass_query = mysql_query($pass_sql);
        $pass = mysql_fetch_assoc($pass_query);
        
        $code = $this->re_code($mail,$id,$pass['pass']);
        
        $subject = "E-mail aktiv�ci�";
        $msg     = "Az al�bbi linkre kattintva aktiv�lhatod".
                   " e-mail c�med rendszer�nkben:\n".
                   "http://$_SERVER[HTTP_HOST]/{$aldomain}$login_mappa/moduls/mail_aktiv.php?code=$code";
		$subject = "=?ISO-8859-2?B?".base64_encode($subject)."?=";
		
        $mail_to = @mail($mail,$subject,$msg, "From: ".$from.PHP_EOL.
				"Content-type: text/plain; charset=iso-8859-2");
        if(!$mail_to) { return false; }
        
        $sql = "update re_data set uj_mail = '$mail' where id = '$id' ";
        mysql_query($sql);
        return true;
    }
    
    //A regisztr�ci� k�relem t�rl�se,
    //amennyiben nem aktiv�lta a mail c�m�t adott id�n bel�l
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
    
    //A r�gi mail c�m vissza�ll�t�sa, amenyiben a v�ltoztat�st k�vet�en
    //adott id�n bel�l nem lett aktiv�lva az �j
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
    //e-mail c�m ellen�rz� f�ggv�ny
    function check_mail_msn($mail_msn)
    {
        eregi("^[a-z0-9_.-]+@[a-z0-9_.-]+$", $mail_msn, $kimenet);
        if(trim($mail_msn) != $kimenet[0]) return false;
        return true;
    }
    
}



//Url vizsg�lata a rugalmas felhaszn�lhat�s�g�rt
$ijel = (eregi("\?", $index_url, $kimenet) != NULL) ? "&" : "?";


$login = new Re_login;
$login->time = time();
$login->re_sql($dbhost, $dbuser, $dbpass, $dbname);

//Ha a bejelentkez�si k�relem megt�rt�nt
if($_SESSION['usr'] != NULL && $_SESSION['psw'] != NULL){
    //Ha nem j� a felhaszn�l�n�v, vagy jelsz�, nem l�phet be,
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
        //ha bel�phet be�rjuk online list�ra
        $_SESSION['belep'] = true;
        $login->status_ir();

        //Ha most l�pett be, be�rjuk a bel�p�s idej�t
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
