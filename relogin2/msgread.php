<?php
/**
 * R.E. Login 2.0 - Üzenet olvasás - msgread.php
 *
 * Üzenet olvasás sablonja. <br />
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
require_once System::getIncLoginDir().'includes/msgread.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'includes/msgmenu.php';

if (!empty($message->messageid)) { ?>
<table align="center" style="width: 600px;">
	<tr>
		<th style="text-align: right; width: 100px; vertical-align: top;"><?php print $msgLabel; ?></th>
		<td style="width: 500px; vertical-align: top;">
			<?php if (!empty($msgUserName)) { ?>
			<a href="<?php print User::profileUrl($msgUserId) ?>"><?php print htmlspecialchars($msgUserName); ?></a>
			<?php } else { ?>
			Törölt felhasználó
			<?php } ?>
		</td>
	</tr>
	<tr>
		<th style="text-align: right; vertical-align: top;">Tárgy:</th>
		<td style="vertical-align: top;"><div style="width: 500px; overflow: hidden;"><?php print $message->subject() ?></div></td>
	</tr>
	<tr>
		<th style="text-align: right; vertical-align: top;">Üzenet:</th>
		<td style="vertical-align: top;"><div style="width: 500px; overflow: hidden;">
		<?php print $message->body(); ?>
		</div></td>
	</tr>
</table>
	<?php if(!empty($message->toid) or System::$user->rank(array('admin','owner'))) { ?>
	<div align="center">
		<?php if (!empty($msgUserName)) { ?>
		<a href="<?php print $url_reply ?>">Válasz</a>
		<?php } ?>
		<a href="<?php print $url_delete ?>">Törlés</a>
	</div>
	<?php }
} else { ?>
<div align="center">
Nincs ilyen üzenet!
</div>
<?php } ?>
