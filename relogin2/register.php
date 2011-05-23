<?php
/**
 * R.E. Login 2.0 - Regisztráció - register.php
 *
 * Regisztráció modul sablonja <br />
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
require_once System::getIncLoginDir().'includes/register.php'; ?>
<div align="center">
	<?php print $msg; ?>
</div>
<div align="center">
<?php if (System::$logged) { ?>
	Regisztrációhoz előbb ki kell jelentkezned. 
<?php } else if (!$REG_BLOCKED and (!$INVITATION_MODE or $validinvite)) { ?>
<form action="" method="post">
	<table border="0" align="center">
		<tr>
			<td>Név:</td>
			<td><input type="text" name="register[username]" value="<?php print $data['username']; ?>" /></td>
		</tr>
		<tr>
			<td>Jelszó:</td>
			<td><input type="password" name="register[userpass]" value="<?php print $data['userpass'] ?>" /></td>
		</tr>
		<tr>
			<td>Jelszó újra:</td>
			<td><input type="password" name="register[reuserpass]" value="<?php print $data['reuserpass'] ?>" /></td>
		</tr>
		<tr>
			<td>E-mail:</td>
			<td><input type="text" name="register[useremail]" value="<?php print $data['useremail'] ?>" /></td>
		</tr>
		<tr>
			<td>E-mail újra:</td>
			<td><input type="text" name="register[reuseremail]" value="<?php print $data['reuseremail'] ?>" /></td>
		</tr>
		<tr>
			<td>E-mail kezelés:</td>
			<td>
				<input type="radio" name="register[public_mail]" <?php 
				print $data['public_mail']!='yes' ? 'checked="checked"' : '' ?> value="no" /> Rejtett
				<input type="radio" name="register[public_mail]" <?php
				print $data['public_mail']=='yes' ? 'checked="checked"' : '' ?> value="yes" /> Nyilvános
			</td>
		</tr>
		<tr>
			<td>Nemed:</td>
			<td>
				<input type="radio" name="register[sex]" <?php 
				print ($data['sex']=='f') ? 'checked="checked"' : '' ?> value="f" /> Nő
				<input type="radio" name="register[sex]"<?php
				print ($data['sex']=='m') ? 'checked="checked"' : '' ?> value="m" /> Férfi
			</td>
		</tr>
		<tr>
			<td>Születési idő:</td>
			<td>
				<select name="register[bdyear]">
					<option value="0">Év</option>
					<?php foreach(range(1900,date('Y')) as $year) { ?>
					<option value="<?php print $year ?>" <?php
					print $data['bdyear'] == $year ? 'selected ="selected" ' : ''
					?>><?php print $year ?></option>
					<?php } ?>
				</select>
					<select name="register[bdmonth]">
					<option value="0">Hónap</option>
					<?php foreach(range(1,12) as $month) { ?>
					<option value="<?php print $month ?>" <?php
					print $data['bdmonth'] == $month ? 'selected ="selected" ' : ''
					?>><?php print $month ?></option>
					<?php } ?>
				</select>
				<select name="register[bdday]">
					<option value="0">Nap</option>
					<?php foreach(range(1,31) as $day) { ?>
					<option value="<?php print $day ?>" <?php
					print $data['bdday'] == $day ? 'selected ="selected" ' : ''
					?>><?php print $day ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<img src="<?php print
						System::getLoginDir().
						'includes/captcha.php' ?>" alt="captcha" />
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="text" name="code" id="relogin-captcha-input" /><br />
				Írd be a képen látható kódot, vagy a művelet ereményét. 
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" value="Regisztrálok" />
			</td>
		</tr>
	</table>
</form>
<?php } else if(!$mailact and $REG_BLOCKED) { ?>
A regisztráció jelenleg szünetel. Köszönjük a megértést.
<?php } else if (!$mailact and $INVITATION_MODE) { ?>
A regisztráció jelenleg csak meghívóval üzemel.
<?php } ?>
</div>
