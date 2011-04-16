<?php
/**
 * R.E. Login 2.0 - Elfelejtett jelszó - includes/forgotpass.php
 *
 * Elfelejtett jelészó funkció folyamatainak indítása.
 * Változók inicializálása.<br />
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
require_once System::getIncLoginDir().'classes/ForgotPass.class.php';

if (System::$logged)
{
	System::redirect(Config::FILE_HOME);
}

$msg = "";
$fpexists = false;
if (isset($_POST['forgotpass']))
{
	$msg = ForgotPass::request($_POST['forgotpass']['email']) 
		? "Az új jelszóhoz szükséges e-mailt kiküldtük!"
		: implode('<br />'.PHP_EOL,ForgotPass::errors());
}
else if (isset($_GET['id']) and isset($_GET['fphash']))
{
	$fp = ForgotPass::getInstance((int)$_GET['id'], $_GET['fphash']);
	$msg = ($fpexists = !empty($fp->userid))
		? "Add meg az új kívánt jelszavad!"
		: "Érvénytelen jelszóváltoztatási kérelem!";
	if (isset($_POST['newpass']))
	{
		$msg = ForgotPass::newPassword($_GET['id'], $_GET['fphash'], 
				$_POST['newpass']['pass'],$_POST['newpass']['repass'])
			? "Jelszavad sikeresen módosítva!"
			: implode('<br />'.PHP_EOL,ForgotPass::errors());
	}
}
?>
