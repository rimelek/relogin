<?php
/**************************************************************************
* R.E. login (1.8.1) - reg.php                                            *
* ======================================================================= *
* A regisztr�ci�t v�gz� f�jl                                              *
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
if (!defined('__RELOGIN__'))
{
	exit('Ezt a f�jlt nem �rheted el k�zvetlen�l.
		Be kell illesztened egy f�jlba, amiben el�tte a config.php-t is beillesztetted m�r.
		Ahogy azt a readme.txt-ben is olvashatod.');
}
class Re_regi extends Re_login
{

    var $aktiv_sor = array();

    //lek�rdezz�k az aktiv�ci�s k�dot
    function re_regi()
    {
		if (isset($_GET['aktival']))
		{
			$aktiv_kod = "select * from re_meghiv where kod = '$_GET[aktival]' and ok = '0'";
		    $aktiv_query = @mysql_query($aktiv_kod);
			$aktiv_sor = @mysql_fetch_assoc($aktiv_query);
			$this->aktiv_sor = $aktiv_sor;
		}
		//�jra be�ll�tjuk a felhaszn�l�i adatokat
		$this->re_adatok();
	}



    //adatok felvitele, t�nyleges regisztr�ci�
    function re_bevitel()
    {
        //L�tezik -e a k�rt nickn�v
        $a = Re_login::re_user($_POST['nick']);
        $user_email = trim($_POST['mail']);
		preg_match('/[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,3}/i',$user_email,$minta);
        
		if(isset($_POST['nick'])){
        //Megvizsg�ljuk a k�rt nick hossz�t
        if( strlen(trim($_POST['nick'])) >= 3 )
        {
            //Ha l�tezik a k�rt nick, m�s nicket k�r�nk
            if(strtolower($a['user']) == strtolower($_POST['nick']))
            {
                print "
                <p align=\"center\">
                Van m�r ilyen nick. K�rlek v�lassz m�sikat!<br />
                </p>
                ";
            }elseif($this->tiltott_char($_POST['nick'])){
              //ha tiltott karakterek vannak a nickben, �j nicket k�r�nk
                print "
                <p align=\"center\">
                A nicked nem tartalmazhat �kezetes, vagy k�l�nleges karaktert!
                </p>
                ";
            }elseif(!isset($minta[0]) or $minta[0] != $user_email ) {
				print "<p align=\"center\">�rv�nytelen e-mail c�m!</p>";
            }elseif($this->tiltott_mail($_POST['mail']) and $this->reg_block != "2"){
              //Ha l�tezik m�r a regisztr�land� e-mail c�m az adatb�zisban
                print "
                <p align=\"center\">
                M�r l�tezik ilyen e-mail c�m az adatb�zisban
                </p>
                ";
            }elseif(empty($_POST['mail']) and $this->reg_block != "2"){
              //Ha a regisztr�ci� nem megh�v�sos, �s nem adtunk meg mail c�met
                print "
                <p align=\"center\">
                Add meg az e-mail c�med!
                </p>
                ";
            }elseif($_POST['pass'] != $_POST['repass']){
                //Ha nem egyezik a k�t jelsz�, k�rj�k a jav�t�s�t
                print "<p align=\"center\">A k�t jelsz� nem egyezik!</p>";
                
            }elseif(strlen($_POST['pass']) < 5 ){
                //Ha t�l r�vid a jelsz�, hosszabbat k�r�nk
                print "<p align=\"center\">Jelszavad minimum 5 karakter legyen</p>";
            }else{
        
                if(isset($_POST['nick']))
                {
                    global $mh_alap,$jel;
                    //Ha ez az els� reg, akkor admin jogot kap
                    $elsoreg = "select id from re_login";
                    $elsoregq = mysql_query($elsoreg);
                    $rows = mysql_num_rows($elsoregq);
                    if($rows < 1) { $jog = "a"; }
                    else{ $jog = "t"; }

                    //Regisztr�ci� adatai
                    $time = time();
                    $pass = md5($_POST['pass']);
                    $nick = trim($_POST['nick']);
                    $mail = $this->aktiv_sor['mail'];
                    if(isset($_POST['mail'])) { $mail = $_POST['mail']; }
                    
                    $public_mail = $_POST['public_mail'];
                    
                    //ip meghat�roz�sa
                    $ip = (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                    ? $_SERVER['HTTP_X_FORWARDED_FOR']
                    : $_SERVER['REMOTE_ADDR'];
                    //B�ng�sz� adatok
                    $browser = $_SERVER['HTTP_USER_AGENT'];
                    //Be�rjuk az adatokat a re_login t�bl�ba
                    $reg_sql = "
                    insert into re_login(user, pass, jog, ip, browser, regido, mh)
                    values( '$nick', '$pass', '$jog', '$ip', '$browser', '$time', '$mh_alap')
                    ";
                    $reg_query = mysql_query($reg_sql);
                    
                    //Be�rjuk az adatokat a re_data t�bl�ba
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

                    //Friss�tj�k a re_meghiv t�bla adatait
                    //�jra nem lehet felhaszn�lni az aktiv�ci�s k�dot
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
                    //Ha megt�rt�n a regisztr�ci�,
                    //�tir�ny�tunk az "sikeres regisztr�ci�" �zenethez
                    $rjel = (eregi("\?", $index_url, $kimenet) != NULL) ? "&" : "?";

                    header("Location: $_SERVER[PHP_SELF]{$rjel}x=true");

                }else{print "Hiba";}
            }
        }else{
            //Ha t�l r�vid a k�rt nick, hosszabbat k�r�nk
            print "
            <p align=\"center\">
            A nicked minimum 3 karakter legyen!
            </p>
            ";
        }
        }
    }
    //Mi sz�m�t tiltott karakternek
    function tiltott_char($nick)
    {
        $nick = trim($nick);
        $e_char =  eregi("[a-z0-9_-]+", $nick, $char);
        
        if($char[0] === $nick) return false;
        return true;
    }

    //a registr�ci�s �rlap el��ll�t�sa
    function re_regform()
    {
		$mform = "";
        if($this->reg_block != "2")
        {
            $mform = '
            <tr>
            <td class="cim">
             E-mail c�m:
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
            E-mail c�m elrejt�se:
            </td>
            <td class="input">
            <input type="radio" name="public_mail" value="0" /> Elrejt
            <input type="radio" name="public_mail" value="1" checked="checked" /> Publikus
            </td>
          </tr>
          <tr>
            <td class="cim">
            Jelsz�: <small>(min 5 karakter)</small>
            </td>
            <td class="input">
            <input type="password" name="pass" size="20" maxlength="15" />
            </td>
          </tr>
          <tr>
            <td class="cim">
            Jelsz� �jra:
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

//Ha m�g nem t�rt�nt meg a reg
if(!isset($_GET['x']) or $_GET['x'] != "true"){
    //Ha nincs blokkolva a reg
    if($re_regi->reg_block != "1")
    {
        //Adatok felvitele adatb�zisba
		if (isset($_POST['nick']))
		{
			$re_regi->re_bevitel();
		}
        //ha nincs megh�v�sos rendszer be�ll�tva,
        //vagy l�tezik az aktiv�ci�s k�d
        if( $re_regi->aktiv_sor != NULL  or $re_regi->reg_block != "2"){
            //Regisztr�ci�s �rlap kiirat�sa
            $re_regi->re_regform();
        }else{
            //Ha megh�v�sos rendszer van be�ll�tva
            print "
            <p align=\"center\">
            <u>A regisztr�ci� jelenleg megh�v�sos alapon �zemel.</u><br />
            Hib�s aktiv�ci�s k�d. Nem regisztr�lhatasz!<br />
            <a href=\"$index_url\">[Tov�bb]</a>
            </p>
            ";
        }
    }else{
        //Ha blokkolva van a regisztr�ci�
        print "
        <p align=\"center\">A regisztr�ci� technikai okok miatt blokkolva.<br />
        K�rlek pr�b�lkozz k�s�bb, vagy vedd fel a kapcsolatot az adminnal<br />
        <a href=\"$index_url\">[Tov�bb]</a>
        </p>";
    }
}
if(isset($_GET['x']) and $_GET['x']=="true")
{
    if($re_regi->reg_block == "2"){
        $aktival = "Most m�r bel�phetsz<br /><br />";
    }else{
        $aktival = "Az aktiv�ci�s linket elk�ldt�k a megadott mailc�medre!<br />
    Amennyiben nem aktiv�lod a c�med 24 �r�n bel�l,<br />
    t�r�lj�k a regisztr�ci�dat!<br /><br />";
    }
    //Ha megt�rt�nt a regisztr�ci�
    print "
    <p align=\"center\">
    Sikeres regisztr�ci�.<br />
    $aktival
    <a href=\"$index_url\">[Tov�bb]</a></p>
    ";
}

?>
