<?php
/**
 * R.E. Login 2.0 - Elfelejtett jelszó - forgotpass.php
 *
 * Elfelejtett jelszó oldal sablonja <br />
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
require_once System::getIncLoginDir().'includes/forgotpass.php'; ?>
<div align="center">
	<?php print $msg; ?>
</div>

<form action="" method="post">
	<div align="center">
	<?php if (isset($_GET['id']) and isset($_GET['fphash']) ) {
		if ($fpexists) { ?>
		Jelszó:<br />
		<input type="password" name="newpass[pass]" /><br />
		Jelszó újra:<br />
		<input type="password" name="newpass[repass]" /><br />
		<input type="submit" value="Küldés" />
		<?php }
	} else { ?>
	Elfelejtetted jelszavad?<br />
	Add meg az e-mail címed, és kövesd az instrukciókat a kiküldött e-mailben.<br />
	E-mail: 
	<input type="text" name="forgotpass[email]" />
	<input type="submit" value="Küldés" />
	<?php } ?>
	</div>
</form>
