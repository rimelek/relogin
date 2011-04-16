<?php
/**
 * R.E. Login 2.0 - Admin - Rangok - admin/ranks_admin.php
 *
 * Rangok kezeléséhez űrlap generálása.<br />
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
require_once System::getIncLoginDir().'includes/admin/ranks_admin.php';
?>
<div align="center"><?php print $msg; ?></div>

<form action="" method="post">
	<table align="center">
		<tr>
			<td>Rang:</td>
			<td><input type="text" name="admin[rankname]" value="<?php print htmlspecialchars($data['rankname']) ?>" /></td>
		</tr>
		<tr>
			<td>Változó neve:</td>
			<td><input type="text" name="admin[varname]" value="<?php print $data['varname'] ?>" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="hidden" name="admin[rankid]" value="<?php print $data['rankid'] ?>" />
				<input type="hidden" name="admin[act]" value="mod" />
				<input type="submit" value="Létrehoz/Módosít" />
			</td>
		</tr>
	</table>
</form>

<form action="" method="post">
	<table align="center">
		<tr>
			<td>Rang:</td>
			<td>
				<select name="admin[rankid]">
				<?php foreach ($ranks as $rank) {
				$selected = $data['rankid'] == $rank->rankid ? 'selected="selected"' : "";
				?>
					<option value="<?php print $rank->rankid ?>" <?php 
						print $selected
					?> ><?php print $rank->name ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Művelet:</td>
			<td>
				<input type="radio" name="admin[op]" value="delete" <?php
					if ($data['op'] == 'delete') print ' checked="checked" ';
				?> /> Törlés
				<input type="radio" name="admin[op]" value="modify" <?php
					if ($data['op'] == 'modify') print ' checked="checked" ';
				?> /> Módosítás
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="hidden" name="admin[act]" value="select" />
				<input type="submit" value="Kiválaszt" />
			</td>
		</tr>
	</table>
</form>
