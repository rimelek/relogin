<?php
/**
 * R.E. Login 2.0 - Admin - Felhasználók - admin/users_admin.php
 *
 * Felhasználók kezeléséhez űrlap generálása.<br />
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
require_once System::getIncLoginDir().'includes/admin/users_admin.php';
?>
<div align="center"><?php print $msg; ?></div>
<form action="" method="post">
	<table cellpadding="0" cellspacing="2" align="center">
		<tr>
			<td>User neve:</td>
			<td><input type="text" name="admin[username]" /></td>
		</tr>
		<tr>
			<td>Rangja:</td>
			<td>
				<select name="admin[rankid]">
					<?php foreach (Ranks::getInstance() as $rank) { 
					if ($rank->rankid == 0) continue; 
					?>
					<option value="<?php print $rank->rankid ?>" ><?php print $rank->name ?></option>
					<?php } ?>
					<option value="x" style="color: red; background: silver;">Törlés</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" value="Módosít" />
			</td>
		</tr>
	</table>
</form>