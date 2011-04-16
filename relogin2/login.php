<?php
/**
 * R.E. Login 2.0 - Login - login.php
 *
 * Login modul sablonja <br />
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
require_once System::getIncLoginDir().'includes/login.php';

if (!System::$logged) {
?>
<div align="center">
	<?php print $msg; ?>
</div>
<form action="" method="post">
	<table align="center" class="relogin-login" border="0" cellspacing="0">
		<tr class="login-username">
			<td class="relogin-label">Nick:</td>
			<td><input class="relogin-field" type="text" name="login[username]" /></td>
		</tr>
		<tr class="login-userpass">
			<td class="relogin-label">Jelszó:</td>
			<td ><input class="relogin-field" type="password" name="login[userpass]" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="checkbox" name="login[remember]" /> Jegyezz meg!<br />
				<input type="submit" value="Belépés" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<a href="<?php print Config::FILE_REGISTER ?>">Regisztráció</a><br />
				<a href="<?php print Config::FILE_FORGOTPASS ?>">Elfelejtetted jelszavad?</a>
			</td>
		</tr>
	</table>
</form>
<?php } else { ?>
<a href="<?php print System::logoutLink() ?>">Kijelentkezés</a>
<?php } ?>