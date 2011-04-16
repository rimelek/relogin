<?php
/**
 * R.E. Login 2.0 - Üzenet Írása - msginbox.php
 *
 * Üzenet, vagy hír írás sablonja <br />
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
require_once System::getIncLoginDir().'includes/msgwrite.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'includes/msgmenu.php';
?>

<div align="center">
	<?php print $msg; ?>
</div>
<form action="" method="post">
	<?php if (System::$user->rank(array('admin','owner'))) { ?>
	<div align="center">
		<input type="checkbox" name="message[news]" <?php if($data['news']) print 'checked="checked"'; ?> />
		Hír küldése
	</div>
	<?php } ?>
	<table align="center" border="0">
		<tr>
			<td>Címzett:</td>
			<td><input type="text" name="message[toname]" value="<?php print htmlspecialchars($data['toname']) ?>" /></td>
		</tr>
		<tr>
			<td>Tárgy:</td>
			<td> <input type="text" name="message[subject]" value="<?php print htmlspecialchars($data['subject']) ?>" /></td>
		</tr>
		<tr>
			<td colspan="2">Üzenet</td>
		</tr>
		<tr>
			<td colspan="2"><textarea cols="40" rows="7" name="message[body]"><?php print htmlspecialchars($data['body']) ?></textarea></td>
		</tr>
	</table>
	<div align="center">
		<input type="submit" value="Küldés" />
	</div>
</form>
