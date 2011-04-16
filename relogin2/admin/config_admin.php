<?php
/**
 * R.E. Login 2.0 - Admin - Beállítások - admin/config_admin.php
 *
 * Admin beállítások űrlap generálása. <br />
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
require_once System::getIncLoginDir().'includes/admin/config_admin.php';
?>
<div align="center">
	<?php print $msg ?>
</div>
<form action="" method="post">
	<table align="center">
	<?php foreach ($titles as $key => &$title) {
	$const = $key;
	$sign = '';
	if ($const[0] == '-' or $const[0] == '+')
	{
		$sign = $const[0];
		$const = substr($const, 1);
	}
	if ($sign == '-') continue;
	if (System::$user->rank('admin') and $sign != '+')
	{
		continue;
	}

	$value = $constants[$const];
	$value_checked = ' value = "'.htmlspecialchars($value).'" ';
	$type = "text";

	if (is_bool($value))
	{
		$type = 'checkbox';
		$value_checked = ($value) ? 'checked = "checked"' : '';
	} ?>
	<tr>
		<td valign="top"><?php print $title ?><td>
		<td valign="top"><input type="<?php print $type ?>" name="admin[<?php print $const ?>]" <?php print $value_checked ?> /></td>
	</tr>
	<?php } ?>
	<tr><td colspan="2" align="center"><input type="submit" value="Küldés" /></td></tr>
	</table>
</form>
