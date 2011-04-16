<?php
/**
 * R.E. Login 2.0 - Profil - includes/profile.php
 *
 * Profil megtekintése. Saját, vagy kiválasztott user. <br />
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

$titles = array(
	'regtime'				=> 'Regisztrált:',
	'logintime'				=> 'Belépett:',
	'refreshtime'			=> 'Frissített:',
	'onlinetime'			=> 'Online ideje:',
	'T_users_useremail'		=> 'E-mail címe:',
	'lastname'				=> 'Vezetéknév:',
	'firstname'				=> 'Keresztnév:',
	'birthdate'				=> 'Született:',
	'sex'					=> 'Neme:',
	'country'				=> 'Ország:',
	'city'					=> 'Város:',
	'website'				=> 'Weboldala:',
	'msn'					=> 'MSN:',
	'skype'					=> 'Skype:',
	'other'					=> 'Egyéb:'
);

if (!System::$logged and !isset($_GET['uid']))
{
	System::redirect(Config::FILE_HOME);
}

if (System::$logged and (!isset($_GET['uid']) or System::$user->T_users_userid == (int)$_GET['uid']))
{
	$user = System::$user;
}
else
{
	$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
	$user = new User($uid);
}

$sendMsgUrl = Url::set(array(
	'msgact' => 'write',
	'msgto' => $user->T_users_userid
),Config::FILE_MESSAGES_WRITE);

$chProfUrl = Url::set(array(
	'uid' => $user->T_users_userid
), Config::FILE_CHANGE_PROFILE, '&amp;');
?>
