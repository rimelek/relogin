<?php
/**
 * R.E. Login 2.0 - Profil módosítás - changeprofile.php
 *
 * Profil módosítás sablonja. <br />
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
require_once System::getIncLoginDir().'includes/changeprofile.php'; ?>
<div align="center">
	<?php print $msg ?>
</div>
<form action="" method="post">
<table border="0" align="center">
	<tr>
		<td>Jelszó:<br /><span style="font-size: 0.9em;">(Kötelező)</span></td>
		<td><input type="password" name="profile[userpass]" /></td>
	</tr>
	<tr>
		<td>Új Jelszó:<br /><span style="font-size: 0.9em;">(Csak ha változtatod)</span></td>
		<td><input type="password" name="profile[newuserpass]" /></td>
	</tr>
	<tr>
		<td>Új Jelszó ismét:<br /><span style="font-size: 0.9em;">(Csak ha változtatod)</span></td>
		<td><input type="password" name="profile[renewuserpass]" /></td>
	</tr>
	<tr>
		<td>E-mail cím</td>
		<td><input type="text" name="profile[useremail]" value="<?php print $data['useremail'] ?>" /></td>
	</tr>
	<tr>
		<td>Nyilvános e-mail:</td>
		<td>
			<input type="radio" name="profile[public_mail]" <?php if(!$data['public_mail']) print 'checked="checked"' ?> value="no" /> Nem
			<input type="radio" name="profile[public_mail]" <?php if($data['public_mail']) print 'checked="checked"' ?> value="yes" /> Igen
		</td>
	</tr>
	<tr>
		<td>Születési dátum</td>
		<td>
	<select name="profile[bdyear]">
		<option value="0">Év</option>
		<?php foreach(range(1900,date('Y')) as $year) { ?>
		<option value="<?php print $year ?>" <?php print $year == $data['bdyear'] ? 'selected = "selected"' : "" ?>><?php print $year ?></option>
		<?php } ?>
	</select>
	<select name="profile[bdmonth]">
		<option value="0">Hónap</option>
		<?php foreach(range(1,12) as $month) { ?>
		<option value="<?php print $month ?>" <?php print $month == $data['bdmonth'] ? 'selected = "selected"' : "" ?>><?php print $month ?></option>
		<?php } ?>
	</select>
	<select name="profile[bdday]">
		<option value="0">Nap</option>
		<?php foreach(range(1,31) as $day) { ?>
		<option value="<?php print $day ?>" <?php print $day == $data['bdday'] ? 'selected = "selected"' : "" ?>><?php print $day ?></option>
		<?php } ?>
	</select>
		</td>
	</tr>
	<tr>
		<td>Nemed:</td>
		<td>
			<input type="radio" name="profile[sex]" <?php if($data['sex'] == "f") print 'checked="checked"' ?> value="f" /> Nő
			<input type="radio" name="profile[sex]" <?php if($data['sex'] != "f") print 'checked="checked"' ?> value="m" /> Férfi
		</td>
	</tr>
	<tr>
		<td>Avatar:</td>
		<td>
			<input type="radio" name="profile[avatar]" <?php if($data['avatar'] == "gravatar") print 'checked="checked"' ?> value="gravatar" /> Gravatar
			<input type="radio" name="profile[avatar]" <?php if($data['avatar'] == "mkavatar") print 'checked="checked"' ?> value="mkavatar" /> MKAvatar
			<input type="radio" name="profile[avatar]" <?php if(!in_array($data['avatar'],array('gravatar','mkavatar'))) print 'checked="checked"' ?> value="none" /> Nincs
		</td>
	</tr>
	<tr>
		<td>Vezetéknév:</td>
		<td><input type="text" name="profile[lastname]" value="<?php print $data['lastname'] ?>" /></td>
	</tr>
	<tr>
		<td>Keresztnév:</td>
		<td><input type="text" name="profile[firstname]" value="<?php print $data['firstname'] ?>" /></td>
	</tr>
	<tr>
		<td>Ország:</td>
		<td><input type="text" name="profile[country]" value="<?php print $data['country'] ?>" /></td>
	</tr>
	<tr>
		<td>Város:</td>
		<td><input type="text" name="profile[city]" value="<?php print $data['city'] ?>" /></td>
	</tr>
	<tr>
		<td>Weboldal:</td>
		<td><input type="text" name="profile[website]" value="<?php print $data['website'] ?>" /></td>
	</tr>
	<tr>
		<td>MSN:</td>
		<td><input type="text" name="profile[msn]" value="<?php print $data['msn'] ?>" /></td>
	</tr>
	<tr>
		<td>Skype:</td>
		<td><input type="text" name="profile[skype]" value="<?php print $data['skype'] ?>" /></td>
	</tr>
	<tr>
		<td colspan="2">Egyéb:</td>
	</tr>
	<tr>
		<td colspan="2">
			<textarea name="profile[other]" cols="40" rows="5" ><?php print $data['other'] ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="Módosítás" /></td>
	</tr>
</table>
</form>
