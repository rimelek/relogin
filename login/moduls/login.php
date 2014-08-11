<?php

/**************************************************************************
* R.E. login (1.8.1) - login.php                                          *
* ======================================================================= *
* Bejelentkez�s �rlapja                                                   *
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
    function reg_block()
    {
        $sql   = "select * from re_admin limit 1";
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);
        if($fetch['reg_block'] == "0")
        {return true;}
        return false;
    }

/* Ha m�g nem l�pett be a user, megjenelik a bel�p�s �rlapja */
if($_SESSION['belep'] == false)
{
?>
<div style="width: 180px;">
<fieldset style="background: #eeeeee;"><legend>Login</legend>
<form action="<?php print $_SERVER['PHP_SELF'];?>" method="post">

<small><b>Nick:</b></small><br />
<input type="text" name="user" size="15" maxlength="15" /><br />

<small><b>Jelsz�:</b></small><br />
<input type="password" name="pass" size="15" maxlength="15" /><br />
<input type="submit" name="login" value="Bel�p" /><br />
<?php
print "<a href=\"$newpass_url\">Elfelejtett jelsz�</a>";
print (reg_block() == true) ? "<br /><a href=\"$reg_form\">Regisztr�ci�</a>" : "";
/* B�rmilyen okb�l, ha nem lehets�ges a bejelentkez�s, ki�ratjuk a hiba ok�t. */
$re_error = isset ($_GET['re_error']) ? $_GET['re_error'] : 0;
switch($re_error)
{
    case "1": print "<br /><small>Hib�s nick/jelsz�</small>";         break;
    case "2": print "<br /><small>Tiltott nick</small>";              break;
    case "3": print "<br /><small>Bel�p�s blokolva</small>";          break;
    case "4": print "<br /><small>Aktiv�ld az e-mail c�med!</small>"; break;
    default:  print "";                                               break;
}
?>
</form>
</fieldset>
</div>
<?php
}else{
    /* Ha bel�pett a felhaszn�l�, megjelenik a kil�p�s link */
    print "<a href=\"?exit\">Kil�p�s</a><br />";
}
?>

