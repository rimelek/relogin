<?php
/**
 * R.E. Login 2.0 - Admin - Beállítások - indludes/admin/config_admin.php
 *
 * Beállítások módosítása. Config osztály módosítása<br />
 *
 * <ul>
 *	<li>Ami "-" jellel kezdődik, az nem jelenik meg az űrlapon,
 * de visszaírja az űrlap elküldésekor</li>
 *	<li>Ami "+" jellel kezdődik, azt admin "admin" rangú is módosíthatja,
 *	nem csak tulajdonos.</li>
 *	<li>Ami előtt nincs jel, azt csak tulajdonos módosíthatja</li>
 * </ul>
 * <br /><br />
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
		'-DBHOST' => 'Adatbázis hoszt',
		'-DBNAME' => 'Adatbázis név',
		'-DBUSER' => 'Adatbázis Felhasználó',
		'-DBPASS' => 'Adatbázis Jelszó',
		'-DBPREF' => 'Tábla prefix',
		'-DBCHARSET' => 'Karakterkódolás pl. (utf8)',
		'-DBCOLLATE' => 'Egybevetés',

		'MINLENGTH_USERNAME' => 'Felhasználónév min hossza',
		'MAXLENGTH_USERNAME' => 'Felhasználónév max hossza',
		'MINLENGTH_PASSWORD' => 'Jelszó min hossza',

		'USERNAME_PATTERN' => 'Felhasználó pattern',

		'SMTP_ON' => 'SMTP használata',
		'SMTP_HOST' => 'SMTP szolgáltató',
		'SMTP_PORT' => 'SMTP port',
		'SMTP_AUTH' => 'Hitelesített kapcsolat',
		'SMTP_USERNAME' => 'STMP felhasználó',
		'SMTP_PASSWORD' => 'SMTP jelszó',

		'MAIL_FROM' => 'E-mail cím (Feladóként)',
		'MAIL_TO' => 'E-mail cím (Válaszcím)',
		'+REG_BLOCKED' => 'Blokkolt regisztráció',
		'+LOGIN_BLOCKED' => 'Blokkolt belépés',
		'+INVITATION_MODE' => 'Meghívó mód',
		'+EMAIL_ACTIVATION'=>'Email aktiváció szükséges',
		'FILE_HOME' => 'Főoldal url-je',
		'FILE_LOGOUT' => 'Kijelentkezés url-je',
		'FILE_REGISTER' => 'Regisztrációs fájl neve',
		'FILE_PROFILE'	=> 'Profil fájl neve',
		'FILE_CHANGE_PROFILE' => 'Profil módosítás',
		'FILE_FORGOTPASS' => 'Elfelejtett jelszó fájl neve',
		'FILE_MESSAGES_READ' => 'Üzenet olvasó fájl neve',
		'FILE_MESSAGES_WRITE' => 'Üzenet író fájl neve',
		'FILE_MESSAGES_INBOX' => 'Bejövő levelek fájlja',
		'FILE_MESSAGES_OUTBOX' => 'Kimenő levelek fájlja',
		'FILE_MESSAGES_NEWS' => 'Hírek fájlja',
		'FILE_SEARCH' => 'Kereső fájl',
		'FILE_USERLIST' => 'User lista',
	    'FILE_SEARCH' => 'User kereső',
		'FILE_PROTECTED_SITE' => 'Védett oldal fájlja.<br />Ide ugrik jogosultság hibakor',
		'+MAX_ONLINE_TIME' => 'Hány mp-ig számítson <br />onlinenak egy user?',
		'-SYMLINK' => 'PATH prefix (pl symlink esetén)<br /> <small>'.
					'Hagyd üresen, ha nem tudod, mi ez. </small> '
	);


$reflection = new ReflectionClass('Config');
$constants = $reflection->getConstants();

$run = false;
$msg = "";
if (isset ($_POST['admin']))
{
	foreach ($constants as $key=>&$item)
	{
		if (!isset($_POST['admin'][$key])) continue;
		if (isset($titles['-'.$key])) continue;
		if (System::$user->rank('admin') and !isset($titles['+'.$key])) continue;
		$constants[$key] = $_POST['admin'][$key];
	}
	//$constants = $_POST['admin'];
	$constants['SMTP_ON'] = isset($_POST['admin']['SMTP_ON']);
	$constants['SMTP_AUTH'] = isset($_POST['admin']['SMTP_AUTH']);
	$constants['REG_BLOCKED'] = isset($_POST['admin']['REG_BLOCKED']);
	$constants['LOGIN_BLOCKED'] = isset($_POST['admin']['LOGIN_BLOCKED']);
	$constants['INVITATION_MODE'] = isset($_POST['admin']['INVITATION_MODE']);
	$constants['EMAIL_ACTIVATION'] = isset($_POST['admin']['EMAIL_ACTIVATION']);
	$msg = "Beállítások módosítva!";
	if (!Admin::runConfigAdmin($constants, $titles ))
	{
		$msg = "";
		foreach (Admin::errors() as $error)
		{
			$msg .= $error."<br />".PHP_EOL;
		}
	}
}
?>


