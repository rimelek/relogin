<?php

/**************************************************************************
* R.E. login (1.8.1) - mh.php                                             *
* ======================================================================= *
* A meghívás ûrlapja                                                      *
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

class Re_meghivo extends Re_login
{
    var $subject;    //email tárgya
    var $text_plusz; //admin elõre beállított üzenete
    var $from;       //Feladó címe.

    //adatok beálítása
    function re_meghivo($subject, $text_plusz, $from)
    {
        if(isset($_POST['mh_mail'])) { $_SESSION['xmail'] = $_POST['mh_mail']; }
        $this->subject    = $subject;
        $this->text_plusz = $text_plusz;
        $this->from       = $from;
        $this->re_adatok();

    }
    //Ha elküldte a meghívót, levon egyet a user meghívóiból
    function mh_levon()
    {
        if($this->jog != "a" and $this->mh_szam > 0)
        {
            mysql_query("update re_login set mh = mh-1 where id = '$this->userid'");
        }
    }
    
    //E-mail elküldése
    function re_kuld()
    {
        $mjel = (eregi("\?", $_SERVER['REQUEST_URI'], $kimenet) != NULL) ? "&" : "?";
        //Kódolt aktivációs kód létrehozása
        $rand_id = mt_rand();
        $md5_link = md5($_POST['mh_mail'].$rand_id);
        //*******************************\\
        $subject = $this->subject;
        $text_plusz = $this->text_plusz;
        $from = $this->from;
        $time = time();
        global $reg_form;
		$subject = "=?ISO-8859-2?B?".base64_encode($subject)."?=";
        //E-mail összeállítása
        $fejlecek = 'From: '.$from.PHP_EOL.
			"Content-type: text/plain; charset=iso-8859-2";
        if($this->check_mail_msn($_POST['mh_mail'])){
            if($this->mh_szam > 0){
            $maill = @mail(
                    $_POST['mh_mail'], $subject,
                    "$_POST[note]\n\n$text_plusz\n\n". //megjegyzés és admin által beállított szöveg
                    "http://$_SERVER[HTTP_HOST]$reg_form?aktival=$md5_link", //aktivációs link
                    $fejlecek
                    );
            }
        }else{
            print '
            <p align="center">
            Hibás mail címet adtál meg! (Egyszerre 1 mail címet írhatsz be)
            </p>';
        }


        $userid = $this->userid;
        //Ha nem sikerült a küldés
        if(!$maill) {
            if($this->check_mail_msn($_POST['mh_mail'])){
            print "<p align=\"center\">Hiba történt a levél elküldésekor</p>";
            }
        }else{
            //Ha sikerült a küldés, beírjuk adatbázisba az aktivációs kódot.
            $sqlkod = "
            insert into re_meghiv(id, kod, mail, time_hiv)
            values('$userid', '$md5_link', '$_POST[mh_mail]', '$time');
            ";
            $query = @mysql_query($sqlkod);
            
            $this->mh_levon();
            
            header("Location: $_SERVER[REQUEST_URI]{$mjel}x=true");
        }

    }
    
    
}

$re_meghivo = new re_meghivo($subject, $text_plusz, $from);


$time = time();

//Külsõ behatolás levédése
if($login->jog != NULL){
if(isset($_POST['mh_mail']))
{
    if(!$login->tiltott_mail($_POST['mh_mail'])){
        $re_meghivo->re_kuld();
    }else{
        print '
        <p align="center">Létezik már ez a mail cím az adatbázisban!<br />
        Nem küldhetsz neki meghívót!
        </p>';
    }
}
    if(isset($_GET['x']) and $_GET['x'] == "true")
    {
        //Ha sikerült meghívás, kiiírjuk a címzett e-mail címével együtt
        $next = trim(eregi_replace("(x=true)|(x=true&)", "", $_SERVER['QUERY_STRING']),"&?");
        print "
        <p align=\"center\">
        A meghívó sikeresen elküldve a következõ címre:<br />
        $_SESSION[xmail]<br /><br />
        <a href=\"$_SERVER[PHP_SELF]?{$next}\">[Tovább]</a>
        </p>
        ";
        unset($_SESSION['xmail']);
    }else{
        //A meghívó ûrlap
        if($re_meghivo->mh_szam > 0)
        {
?>
            <div align="center">
            
            <?php print "Meghívóid száma: ";
                if($login->jog == "a") {print "Végtelen "; }
                else{ print  $re_meghivo->mh_szam . "db"; }
            ?>
            <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
            Írd be a meghívni kívánt személy e-mail címét:<br />
            <input type="text" name="mh_mail" size="20" /><br />
            Megjegyzés:<br />
            <textarea name="note" cols="20" rows="4"></textarea><br />
            <input type="submit" value="Meghív" />
            </form>
            </div>

<?php
        }else{
        print "<p align=\"center\">Sajnos nincs meghívód :(</p>";
        }
    }
}else{
  //Ha nem regisztrált tag
    print "<p align=\"center\">Ehhez regisztrálnod kell</p>";
}

?>
