<?php
/**
 * R.E. Login 2.0 - Meghívók - includes/invite.php
 *
 * Meghívó küldés indítása.
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
require_once System::getIncLoginDir().'classes/Invites.class.php';

System::protectedSite();

$msg = "";
if (Config::INVITATION_MODE and System::$user->invitations and isset($_POST['invite']))
{
	$msg = "Meghívó elküldve!";
	$invites = new Invites();

	if (!$invites->send($_POST['invite']['email']))
	{
		$msg = "Hiba történt!<br />";
		$msg .= implode("<br />".PHP_EOL,Invites::errors());
	}

}

?>
