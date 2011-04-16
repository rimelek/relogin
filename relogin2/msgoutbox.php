<?php
/**
 * R.E. Login 2.0 - Kimenő üzenetek - msgoutbox.php
 *
 * Kimenő üzenetek sablonja. <br />
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
require_once System::getIncLoginDir().'includes/msgoutbox.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'includes/msgmenu.php';


$pagelinks = $outbox->pageLinks(10);
if ($outbox->count()) { ?>
<div align="center">
<?php print $pagelinks ?>
</div>
<script type="text/javascript" src="<?php print System::getLogindir() ?>js/checkall.js"></script>
<form action="" method="post" id="relogin-msglst-outbox">
<table align="center" class="relogin-msglst">
	<tr>
		<th class="msglst-icon"></th>
		<th class="msglst-usr">Címzett</th>
		<th class="msglst-subject">Tárgy</th>
		<th class="msglst-time">Idő</th>
		<th class="msglst-delbox"><input type="checkbox" onclick="checkAll(this, 'relogin-msglst-outbox')" /></th>
	</tr>
<?php foreach ($outbox as $message) {
	$to = !$message->toid
			? '<b>Hír</b>'
			: (empty($message->T_to_username)
				? 'Törölt user'
				: '<a href="'.User::profileUrl($message->toid).'">'.htmlspecialchars($message->T_to_username).'</a>');
			
	?>
	<tr>
		<td class="msglst-icon"><img src="<?php print $message->icon(); ?>" alt="icon" /></td>
		<td class="msglst-usr"><?php print $to ?></td>
		<td class="msglst-subject"><a href="<?php print $message->msgUrl(); ?>"><?php print htmlspecialchars($message->subject(true)); ?></a></td>
		<td class="msglst-time"><?php print $message->sendtime ?></td>
		<td class="msglst-delbox"><input type="checkbox" name="msglist[]" value="<?php print $message->messageid ?>" /></td>
	</tr>
<?php } ?>
</table>
	<div align="center"><input type="submit" value="Kijelöltek törlése" /></div>
</form>
<div align="center">
<?php print $pagelinks ?>
</div>
<?php } else { ?>
<div align="center" >
Nincs egy üzeneted sem.
</div>
<?php } ?>