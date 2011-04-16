<?php
/**
 * R.E. Login 2.0 - Admin - Meghívók - admin/invite_admin.php
 *
 * Meghívók kiosztásához űrlap generálása.<br />
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
require_once System::getIncLoginDir().'includes/admin/invite_admin.php';
?>
<div align="center"><?php print $msg; ?></div>
<form action="" method="post">
	<table align="center">
		<tr>
			<td><?php print $titles['allusers'] ?></td>
			<td><input id="relogin_inv_all" type="checkbox" name="admin[allusers]" onclick="document.getElementById('relogin_inv_un').readOnly = this.checked;" <?php
			if ($data['allusers']) print ' checked="checked" ';
			?> />
			</td>
		</tr>
		<tr>
			<td><?php print $titles['username'] ?></td>
			<td>
				<input id="relogin_inv_un" type="text" name="admin[username]" value="<?php print $data['username'] ?>" />
				<script type="text/javascript">
					document.getElementById('relogin_inv_un').readOnly = document.getElementById('relogin_inv_all').checked;
				</script>
			</td>
		</tr>
		<tr>
			<td><?php print $titles['invitations']; ?><sup>(1)</sup></td>
			<td>
				<select name="admin[invitations]">
					<?php for ($i=0; $i <= Admin::maxInvitation(); $i++) {
						$selected = $data['invitations']==$i ? 'selected="selected"' : '';
					?>

					<option value="<?php print $i ?>" <?php print $selected ?> ><?php print $i ?> db</option>
					<?php } ?>
				</select>
				<select name="admin[mode]">
					<option value="<?php print Admin::INVITE_SET ?>" <?php
					if ($data['mode'] == Admin::INVITE_SET) print ' selected="selected" ';
					?> >Beállít</option>
					<option value="<?php print Admin::INVITE_ADD ?>" <?php
					if ($data['mode'] == Admin::INVITE_ADD) print ' selected="selected" ';
					?> >Hozzáad</option>
					<option value="<?php print Admin::INVITE_SUB ?>" <?php
					if ($data['mode'] == Admin::INVITE_SUB) print ' selected="selected" ';
					?> >Elvesz</option>
				</select>
			</td>
		</tr>
	</table>
	<div align="center"><input type="submit" value="Elküld" /></div>
</form>
<div align="center">
	<sup>(1)</sup> Egy felhasználó maximum	<?php print Admin::maxInvitation() ?> meghívóval rendelkezhet!
</div>