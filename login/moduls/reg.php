<?php
/**************************************************************************
* R.E. login (1.8.1) - reg.php                                            *
* ======================================================================= *
* A regisztrációt végzõ fájl                                              *
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
if (!defined('__RELOGIN__'))
{
	exit('Ezt a fájlt nem érheted el közvetlenül.
		Be kell illesztened egy fájlba, amiben elõtte a config.php-t is beillesztetted már.
		Ahogy azt a readme.txt-ben is olvashatod.');
}
class Re_regi extends Re_login
{

    var $aktiv_sor = array();

    //lekérdezzük az aktivációs kódot
    function re_regi()
    {
		if (isset($_GET['aktival']))
		{
			$aktiv_kod = "select * from re_meghiv where kod = '$_GET[aktival]' and ok = '0'";
		    $aktiv_query = @mysql_query($aktiv_kod);
			$aktiv_sor = @mysql_fetch_assoc($aktiv_query);
			$this->aktiv_sor = $aktiv_sor;
		}
		//újra beállítjuk a felhasználói adatokat
		$this->re_adatok();
	}



    //adatok felvitele, tényleges regisztráció
    function re_bevitel()
    {
        //Létezik -e a kért nicknév
        $a = Re_login::re_user($_POST['nick']);
        $user_email = trim($_POST['mail']);
		preg_match('/[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,3}/i',$user_email,$minta);
        
		if(isset($_POST['nick'])){
        //Megvizsgáljuk a kért nick hosszát
        if( strlen(trim($_POST['nick'])) >= 3 )
        {
            //Ha létezik a kért nick, más nicket kérünk
            if(strtolower($a['user']) == strtolower($_POST['nick']))
            {
                print "
                <p align=\"center\">
                Van már ilyen nick. Kérlek válassz másikat!<br />
                </p>
                ";
            }elseif($this->tiltott_char($_POST['nick'])){
              //ha tiltott karakterek vannak a nickben, új nicket kérünk
                print "
                <p align=\"center\">
                A nicked nem tartalmazhat ékezetes, vagy különleges karaktert!
                </p>
                ";
            }elseif(!isset($minta[0]) or $minta[0] != $user_email ) {
				print "<p align=\"center\">Érvénytelen e-mail cím!</p>";
            }elseif($this->tiltott_mail($_POST['mail']) and $this->reg_block != "2"){
              //Ha létezik már a regisztrálandó e-mail cím az adatbázisban
                print "
                <p align=\"center\">
                Már létezik ilyen e-mail cím az adatbázisban
                </p>
                ";
            }elseif(empty($_POST['mail']) and $this->reg_block != "2"){
              //Ha a regisztráció nem meghívásos, és nem adtunk meg mail címet
                print "
                <p align=\"center\">
                Add meg az e-mail címed!
                </p>
                ";
            }elseif($_POST['pass'] != $_POST['repass']){
                //Ha nem egyezik a két jelszó, kérjük a javítását
                print "<p align=\"center\">A két jelszó nem egyezik!</p>";
                
            }elseif(strlen($_POST['pass']) < 5 ){
                //Ha túl rövid a jelszó, hosszabbat kérünk
                print "<p align=\"center\">Jelszavad minimum 5 karakter legyen</p>";
            }else{
        
                if(isset($_POST['nick']))
                {
                    global $mh_alap,$jel;
                    //Ha ez az elsõ reg, akkor admin jogot kap
                    $elsoreg = "select id from re_login";
                    $elsoregq = mysql_query($elsoreg);
                    $rows = mysql_num_rows($elsoregq);
                    if($rows < 1) { $jog = "a"; }
                    else{ $jog = "t"; }

                    //Regisztráció adatai
                    $time = time();
                    $pass = md5($_POST['pass']);
                    $nick = trim($_POST['nick']);
                    $mail = $this->aktiv_sor['mail'];
                    if(isset($_POST['mail'])) { $mail = $_POST['mail']; }
                    
                    $public_mail = $_POST['public_mail'];
                    
                    //ip meghatározása
                    $ip = (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                    ? $_SERVER['HTTP_X_FORWARDED_FOR']
                    : $_SERVER['REMOTE_ADDR'];
                    //Böngészõ adatok
                    $browser = $_SERVER['HTTP_USER_AGENT'];
                    //Beírjuk az adatokat a re_login táblába
                    $reg_sql = "
                    insert into re_login(user, pass, jog, ip, browser, regido, mh)
                    values( '$nick', '$pass', '$jog', '$ip', '$browser', '$time', '$mh_alap')
                    ";
                    $reg_query = mysql_query($reg_sql);
                    
                    //Beírjuk az adatokat a re_data táblába
                    if(!isset($_POST['mail'])){
                        $reg2_sql = "
                        insert into re_data(mail,uj_mail,public_mail)
                        values('$mail','$mail', '$public_mail')
                        ";
                    }else{
                        $reg2_sql = "
                        insert into re_data(uj_mail,public_mail)
                        values('$mail', '$public_mail')
                        ";
                    }
                    $reg2_query = mysql_query($reg2_sql);

                    //Frissítjük a re_meghiv tábla adatait
                    //Újra nem lehet felhasználni az aktivációs kódot
                    $reg_time = time();
                    $reg3_sql = "
                    update re_meghiv set
                    ok = '1', time_reg = '$reg_time' where kod = '$_GET[aktival]'
                    ";

                    $reg3_query = mysql_query($reg3_sql);
                    $id_query = mysql_query("
                        select id from re_login where
                        user = '$_POST[nick]' limit 1
                        ");
                    $id_fetch = mysql_fetch_assoc($id_query);
                    $id = $id_fetch['id'];
                    if($this->reg_block != "2"){
                    $this->mail_aktiv_kuld($mail,$id);
                    }
                    //Ha megtörtén a regisztráció,
                    //átirányítunk az "sikeres regisztráció" üzenethez
                    $rjel = (eregi("\?", $index_url, $kimenet) != NULL) ? "&" : "?";

                    header("Location: $_SERVER[PHP_SELF]{$rjel}x=true");

                }else{print "Hiba";}
            }
        }else{
            //Ha túl rövid a kért nick, hosszabbat kérünk
            print "
            <p align=\"center\">
            A nicked minimum 3 karakter legyen!
            </p>
            ";
        }
        }
    }
    //Mi számít tiltott karakternek
    function tiltott_char($nick)
    {
        $nick = trim($nick);
        $e_char =  eregi("[a-z0-9_-]+", $nick, $char);
        
        if($char[0] === $nick) return false;
        return true;
    }

    //a registrációs ûrlap elõállítása
    function re_regform()
    {
		$mform = "";
        if($this->reg_block != "2")
        {
            $mform = '
            <tr>
            <td class="cim">
             E-mail cím:
            </td>
            <td class="input">
             <input type="text" name="mail" size="20" />
            </td>
            </tr>
            ';
        }
        print <<<REGFORM
        <table id="reg" align="center" border="0" cellpadding="3" cellspacing="0">
         <form action="$_SERVER[REQUEST_URI]" method="POST">
          <tr>
            <td class="cim">
            Nick: <small>(min 3 karakter)</small>
            </td>
            <td class="input">
            <input type="text" name="nick" size="20" maxlength="15" />
            </td>
          </tr>
            $mform
          <tr>
            <td class="cim">
            E-mail cím elrejtése:
            </td>
            <td class="input">
            <input type="radio" name="public_mail" value="0" /> Elrejt
            <input type="radio" name="public_mail" value="1" checked="checked" /> Publikus
            </td>
          </tr>
          <tr>
            <td class="cim">
            Jelszó: <small>(min 5 karakter)</small>
            </td>
            <td class="input">
            <input type="password" name="pass" size="20" maxlength="15" />
            </td>
          </tr>
          <tr>
            <td class="cim">
            Jelszó újra:
            </td>
            <td class="input">
            <input type="password" name="repass" size="20" maxlength="15" />
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
            <input type="submit" name="regist" value="Mehet" />
            </td>
          </tr>
          </form>
        </table>
REGFORM;
    }
}
$re_regi = new Re_regi();

//Ha még nem történt meg a reg
if(!isset($_GET['x']) or $_GET['x'] != "true"){
    //Ha nincs blokkolva a reg
    if($re_regi->reg_block != "1")
    {
        //Adatok felvitele adatbázisba
		if (isset($_POST['nick']))
		{
			$re_regi->re_bevitel();
		}
        //ha nincs meghívásos rendszer beállítva,
        //vagy létezik az aktivációs kód
        if( $re_regi->aktiv_sor != NULL  or $re_regi->reg_block != "2"){
            //Regisztrációs ûrlap kiiratása
            $re_regi->re_regform();
        }else{
            //Ha meghívásos rendszer van beállítva
            print "
            <p align=\"center\">
            <u>A regisztráció jelenleg meghívásos alapon üzemel.</u><br />
            Hibás aktivációs kód. Nem regisztrálhatasz!<br />
            <a href=\"$index_url\">[Tovább]</a>
            </p>
            ";
        }
    }else{
        //Ha blokkolva van a regisztráció
        print "
        <p align=\"center\">A regisztráció technikai okok miatt blokkolva.<br />
        Kérlek próbálkozz késõbb, vagy vedd fel a kapcsolatot az adminnal<br />
        <a href=\"$index_url\">[Tovább]</a>
        </p>";
    }
}
if(isset($_GET['x']) and $_GET['x']=="true")
{
    if($re_regi->reg_block == "2"){
        $aktival = "Most már beléphetsz<br /><br />";
    }else{
        $aktival = "Az aktivációs linket elküldtük a megadott mailcímedre!<br />
    Amennyiben nem aktiválod a címed 24 órán belül,<br />
    töröljük a regisztrációdat!<br /><br />";
    }
    //Ha megtörtént a regisztráció
    print "
    <p align=\"center\">
    Sikeres regisztráció.<br />
    $aktival
    <a href=\"$index_url\">[Tovább]</a></p>
    ";
}

?>
