<?php
/**
 * R.E. Login 2.0 - Profil nézet - profile.php
 *
 * Profil megtekintés sablonja. <br />
 * <br />
 * <b>Dátum:</b> 2010.04.02.
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 * @version 2.0
 */

if(!class_exists('System'))
{
	exit('Ez a fajl nem erheto el kozvetlenul. Reszletek a readme.txt-ben.');
}

/**
 * @ignore
 */
require_once System::getIncLoginDir().'includes/profile.php';

if (empty($user->T__users__userid)) { ?>

<div align="center">
	Nem létezik ilyen felhasználó
</div>

<?php } else { ?>
<div align="center" >
<?php print "<b>".$user->username.'</b> '. ($user->isOnline() ? "online" : "offline") . ' (' .  $user->rankName().')'; ?>
	<br />
	<img src="<?php print $user->avatar(120); ?>" alt="gravatar" />
	<br /><a href="<?php print $sendMsgUrl ?>"><img style="border: 0;" src="<?php print System::getLogindir() ?>images/send.png" alt="send" /></a>
	<?php if ($user == System::$user or System::$user->rank(array('admin','owner'))) { ?>
	<a href="<?php print $chProfUrl; ?>"><img style="border: 0;" src="<?php print System::getLogindir() ?>images/my-account.png" title="Módosítás" alt="Profil szerkesztés" /></a>
	<?php } ?>
</div>

<table border="0" align="center">
<?php foreach ($titles as $var => $title) {
	$value = trim($user[$var]);
	if (empty($value)) continue;
	
	if ($var == 'sex')
	{
		$value = ($value == 'f') ? 'Nő' : 'Férfi';
	}
	else if ($var == 'T__users__useremail' and !$user->public_mail)
	{
		if (System::$user->rank != 1 and $user != System::$user) continue;
		$value = "Rejtett: ".$user->T__users__useremail;
	} 
	else if($var == 'onlinetime')
	{
		$value = User::getOnlineTime($user->onlinetime,'{day} nap, {hour} óra, {min} perc és {sec} mp');
		
	}

	$value = nl2br(htmlspecialchars($value));
	if ($var == 'website')
	{
		$value = trim($value);
		if (!empty($value))
		{
			$value = "<a href='".$value."'>Link</a>";
		}
	}
	?>
	<tr>
		<td valign="top"><?php print $title ?></td>
		<td valign="top"><?php print $value; ?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>