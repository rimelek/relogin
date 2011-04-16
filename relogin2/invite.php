<?php
/**
 * R.E. Login 2.0 - Meghívó - invite.php
 *
 * Meghívó oldal sablonja <br />
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
require_once System::getIncLoginDir().'includes/invite.php'; ?>
<div align="center">
<?php print $msg; ?>
<br />
Meghívóid száma: <?php print (int)System::$user->invitations; ?>
</div>
<?php if (Config::INVITATION_MODE and System::$user->invitations) { ?>
<form action="" method="post">
	<table border="0" align="center">
		<tr>
			<td>E-mail:</td>
			<td><input type="text" name="invite[email]" /></td>
		</tr>
		<tr>
			<td colspan="2">Üzenj valamit:</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea name="invite[message]" cols="40" rows="8" ></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Küldés" /></td>
		</tr>
	</table>
</form>
<?php } ?>