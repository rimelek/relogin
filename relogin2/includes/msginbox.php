<?php
/**
 * R.E. Login 2.0 - Üzenetek - Bejövő - includes/msginbox.php
 *
 * Bejövő üzenetek lekérdezése, törlése.<br />
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
require_once System::getIncLoginDir().'classes/Messages.class.php';
System::protectedSite();

if (isset($_POST['msglist']))
{
	Messages::deleteMsgs($_POST['msglist']);
}

$inbox = new Messages(Messages::INBOX);

?>
