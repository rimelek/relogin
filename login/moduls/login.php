<?php

/**************************************************************************
* R.E. login (1.8.1) - login.php                                          *
* ======================================================================= *
* Bejelentkezés ûrlapja                                                   *
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
    function reg_block()
    {
        $sql   = "select * from re_admin limit 1";
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);
        if($fetch['reg_block'] == "0")
        {return true;}
        return false;
    }

/* Ha még nem lépett be a user, megjenelik a belépés ûrlapja */
if($_SESSION['belep'] == false)
{
?>
<div style="width: 180px;">
<fieldset style="background: #eeeeee;"><legend>Login</legend>
<form action="<?php print $_SERVER['PHP_SELF'];?>" method="post">

<small><b>Nick:</b></small><br />
<input type="text" name="user" size="15" maxlength="15" /><br />

<small><b>Jelszó:</b></small><br />
<input type="password" name="pass" size="15" maxlength="15" /><br />
<input type="submit" name="login" value="Belép" /><br />
<?php
print "<a href=\"$newpass_url\">Elfelejtett jelszó</a>";
print (reg_block() == true) ? "<br /><a href=\"$reg_form\">Regisztráció</a>" : "";
/* Bármilyen okból, ha nem lehetséges a bejelentkezés, kiíratjuk a hiba okát. */
$re_error = isset ($_GET['re_error']) ? $_GET['re_error'] : 0;
switch($re_error)
{
    case "1": print "<br /><small>Hibás nick/jelszó</small>";         break;
    case "2": print "<br /><small>Tiltott nick</small>";              break;
    case "3": print "<br /><small>Belépés blokolva</small>";          break;
    case "4": print "<br /><small>Aktiváld az e-mail címed!</small>"; break;
    default:  print "";                                               break;
}
?>
</form>
</fieldset>
</div>
<?php
}else{
    /* Ha belépett a felhasználó, megjelenik a kilépés link */
    print "<a href=\"?exit\">Kilépés</a><br />";
}
?>

