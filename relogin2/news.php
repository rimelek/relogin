<?php
/**
 * R.E. Login 2.0 - Hírek - news.php
 *
 * Hírlista sablonja. <br />
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
require_once System::getIncLoginDir().'includes/news.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'includes/msgmenu.php';

$pagelinks = $inbox->pageLinks(10);
if ($inbox->count()) { ?>
<div align="center">
<?php print $pagelinks ?>
</div>
<script type="text/javascript" src="<?php print System::getLogindir() ?>js/checkall.js"></script>
<form action="" method="post" id="relogin-msglst-news">
<table align="center" class="relogin-msglst">
	<tr>
		<th class="msglst-usr">Írta</th>
		<th class="msglst-subject">Tárgy</th>
		<th class="msglst-time">Idő</th>
		<?php if (System::$user->rank('admin')) { ?>
		<th class="msglst-delbox"><input type="checkbox" onclick="checkAll(this, 'relogin-msglst-news')" /></th>
		<?php } ?>
	</tr>
<?php foreach ($inbox as $message) {
	
	?>
	<tr>
		<td class="msglst-usr"><a href="<?php print User::profileUrl($message->fromid) ?>"><?php print htmlspecialchars($message->T_from_username); ?></a></td>
		<td class="msglst-subject"><a href="<?php print $message->msgUrl(); ?>"><?php print htmlspecialchars($message->subject(true)); ?></a></td>
		<td class="msglst-time"><?php print $message->sendtime ?></td>
		<?php if (System::$user->rank('admin')) { ?>
		<td class="msglst-delbox"><input type="checkbox" name="msglist[]" value="<?php print $message->messageid ?>" /></td>
		<?php } ?>
	</tr>
<?php } ?>
</table>
	<?php if (System::$user->rank('admin')) { ?>
	<div align="center"><input type="submit" value="Kijelöltek törlése" /></div>
	<?php } ?>
</form>
<div align="center">
<?php print $pagelinks ?>
</div>
<?php } else { ?>
<div align="center" >
Nincs egy hír sem.
</div>
<?php } ?>