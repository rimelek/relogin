<?php
/**
 * R.E. Login 2.0 - Üzenetek - Menü - includes/msgmenu.php
 *
 * Üzenetek oldalon megjelenő navigáció az üzenetek oldalak között.<br />
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

$stat = System::msgStat();
?>
<div class="relogin-msgmenu" align="center">
	<a href="<?php print Config::FILE_MESSAGES_WRITE ?>">Új</a> |
	<a href="<?php print Config::FILE_MESSAGES_INBOX ?>">Bejövő(<?php
		print $stat['inbox'].'/'.$stat['unread'];
	?>)</a> |
	<a href="<?php print Config::FILE_MESSAGES_OUTBOX ?>">Kimenő(<?php
		print $stat['outbox'].'/'.$stat['unreadout'];
	?>)</a> |
	<a href="<?php print Config::FILE_MESSAGES_NEWS ?>">Hír(<?php
		print $stat['news'].'/'.$stat['unreadnews'];
	?>)</a>
</div>