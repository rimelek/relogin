<?php

/**************************************************************************
* R.E. login (1.8.1) - mh.php                                             *
* ======================================================================= *
* A megh�v�s �rlapja                                                      *
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

class Re_meghivo extends Re_login
{
    var $subject;    //email t�rgya
    var $text_plusz; //admin el�re be�ll�tott �zenete
    var $from;       //Felad� c�me.

    //adatok be�l�t�sa
    function re_meghivo($subject, $text_plusz, $from)
    {
        if(isset($_POST['mh_mail'])) { $_SESSION['xmail'] = $_POST['mh_mail']; }
        $this->subject    = $subject;
        $this->text_plusz = $text_plusz;
        $this->from       = $from;
        $this->re_adatok();

    }
    //Ha elk�ldte a megh�v�t, levon egyet a user megh�v�ib�l
    function mh_levon()
    {
        if($this->jog != "a" and $this->mh_szam > 0)
        {
            mysql_query("update re_login set mh = mh-1 where id = '$this->userid'");
        }
    }
    
    //E-mail elk�ld�se
    function re_kuld()
    {
        $mjel = (eregi("\?", $_SERVER['REQUEST_URI'], $kimenet) != NULL) ? "&" : "?";
        //K�dolt aktiv�ci�s k�d l�trehoz�sa
        $rand_id = mt_rand();
        $md5_link = md5($_POST['mh_mail'].$rand_id);
        //*******************************\\
        $subject = $this->subject;
        $text_plusz = $this->text_plusz;
        $from = $this->from;
        $time = time();
        global $reg_form;
		$subject = "=?ISO-8859-2?B?".base64_encode($subject)."?=";
        //E-mail �ssze�ll�t�sa
        $fejlecek = 'From: '.$from.PHP_EOL.
			"Content-type: text/plain; charset=iso-8859-2";
        if($this->check_mail_msn($_POST['mh_mail'])){
            if($this->mh_szam > 0){
            $maill = @mail(
                    $_POST['mh_mail'], $subject,
                    "$_POST[note]\n\n$text_plusz\n\n". //megjegyz�s �s admin �ltal be�ll�tott sz�veg
                    "http://$_SERVER[HTTP_HOST]$reg_form?aktival=$md5_link", //aktiv�ci�s link
                    $fejlecek
                    );
            }
        }else{
            print '
            <p align="center">
            Hib�s mail c�met adt�l meg! (Egyszerre 1 mail c�met �rhatsz be)
            </p>';
        }


        $userid = $this->userid;
        //Ha nem siker�lt a k�ld�s
        if(!$maill) {
            if($this->check_mail_msn($_POST['mh_mail'])){
            print "<p align=\"center\">Hiba t�rt�nt a lev�l elk�ld�sekor</p>";
            }
        }else{
            //Ha siker�lt a k�ld�s, be�rjuk adatb�zisba az aktiv�ci�s k�dot.
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

//K�ls� behatol�s lev�d�se
if($login->jog != NULL){
if(isset($_POST['mh_mail']))
{
    if(!$login->tiltott_mail($_POST['mh_mail'])){
        $re_meghivo->re_kuld();
    }else{
        print '
        <p align="center">L�tezik m�r ez a mail c�m az adatb�zisban!<br />
        Nem k�ldhetsz neki megh�v�t!
        </p>';
    }
}
    if(isset($_GET['x']) and $_GET['x'] == "true")
    {
        //Ha siker�lt megh�v�s, kii�rjuk a c�mzett e-mail c�m�vel egy�tt
        $next = trim(eregi_replace("(x=true)|(x=true&)", "", $_SERVER['QUERY_STRING']),"&?");
        print "
        <p align=\"center\">
        A megh�v� sikeresen elk�ldve a k�vetkez� c�mre:<br />
        $_SESSION[xmail]<br /><br />
        <a href=\"$_SERVER[PHP_SELF]?{$next}\">[Tov�bb]</a>
        </p>
        ";
        unset($_SESSION['xmail']);
    }else{
        //A megh�v� �rlap
        if($re_meghivo->mh_szam > 0)
        {
?>
            <div align="center">
            
            <?php print "Megh�v�id sz�ma: ";
                if($login->jog == "a") {print "V�gtelen "; }
                else{ print  $re_meghivo->mh_szam . "db"; }
            ?>
            <form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
            �rd be a megh�vni k�v�nt szem�ly e-mail c�m�t:<br />
            <input type="text" name="mh_mail" size="20" /><br />
            Megjegyz�s:<br />
            <textarea name="note" cols="20" rows="4"></textarea><br />
            <input type="submit" value="Megh�v" />
            </form>
            </div>

<?php
        }else{
        print "<p align=\"center\">Sajnos nincs megh�v�d :(</p>";
        }
    }
}else{
  //Ha nem regisztr�lt tag
    print "<p align=\"center\">Ehhez regisztr�lnod kell</p>";
}

?>
