<?php
/**
 * R.E. Login 2.0 - Telepítő - index.php
 *
 * Telepítő űrlap megjelenítése. <br />
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

/**
 * @ignore
 */
require_once 'init.php';
?>
<html>
	<head>
		<title>R.E. Login 2.0 telepítése</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body>
	<?php if (!$installed) { ?>
	<div align="center">
		<?php print $msg ?>
	</div>
	<?php if (!$install || !$run  ) { ?>
	<form action="" method="post">
		<table align="center">
		<?php foreach ($titles as $key => &$title) {
		$value = $constants[$key];
		$value_checked = ' value = "'.htmlspecialchars($value).'" ';
		$type = "text";
		//$type = (stripos($key,'password') !== false) ? 'password' : 'text';
		if (is_bool($value))
		{
			$type = 'checkbox';
			$value_checked = ($value) ? 'checked = "checked"' : '';
		} ?>
		<tr>
			<td valign="top"><?php print $title ?><td>
			<td valign="top"><input type="<?php print $type ?>" name="install[<?php print $key ?>]" <?php print $value_checked ?> /></td>
		</tr>
		<?php } ?>
		<tr><td colspan="2" align="center"><input type="submit" value="Küldés" /></td></tr>
		</table>
	</form>
	<?php }
	} else { ?>
		<div align="center">
			Úgy tűnik, ez a login már telepítve van.<br />
			Töröld a <b>Config.class.php</b> -t classes mappából, és próbáld újra telepíteni. 
		</div>
	<?php } ?>

	</body>
</html>