<?php
/**
 * R.E. Login 2.0 - Telepítő - init.php
 *
 * Betölti a szükséges fájlokat, osztályokat. Inicializálja a használt változókat
 * Itt történik a telepítő elindítása a telepítés gombra kattintva.<br />
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
require_once 'Config_tpl.class.php';

/**
 * @ignore
 */
require_once '../classes/System.class.php';
System::setSlashes($_POST);

$titles = array(
		'DBHOST' => 'Adatbázis hoszt',
		'DBNAME' => 'Adatbázis név',
		'DBUSER' => 'Adatbázis Felhasználó',
		'DBPASS' => 'Adatbázis Jelszó',
		'DBPREF' => 'Tábla prefix',
		'DBCHARSET' => 'Karakterkódolás pl. (utf8)',
		'DBCOLLATE' => 'Egybevetés',

		'MINLENGTH_USERNAME' => 'Felhasználónév min hossza',
		'MAXLENGTH_USERNAME' => 'Felhasználónév max hossza',
		'MINLENGTH_PASSWORD' => 'Jelszó min hossza',

		'USERNAME_PATTERN' => 'Felhasználó pattern',

		'SMTP_ON' => 'SMTP használata',
		'SMTP_HOST' => 'SMTP szolgáltató',
		'SMTP_PORT' => 'SMTP port',
		'SMTP_AUTH' => 'Hitelesített kapcsolat',
		'SMTP_USERNAME' => 'SMTP felhasználó',
		'SMTP_PASSWORD' => 'SMTP jelszó',

		'MAIL_FROM' => 'E-mail cím (Feladóként)',
		'MAIL_TO' => 'E-mail cím (Válaszcím)',
		'REG_BLOCKED' => 'Blokkolt regisztráció',
		'LOGIN_BLOCKED' => 'Blokkolt belépés',
		'INVITATION_MODE' => 'Meghívó mód',
		'EMAIL_ACTIVATION'=>'Email aktiváció szükséges',
		'FILE_HOME' => 'Főoldal url-je',
		'FILE_REGISTER' => 'Regisztrációs fájl neve',
		'FILE_PROFILE'	=> 'Profil fájl neve',
		'FILE_CHANGE_PROFILE' => 'Profil módosítás',
		'FILE_FORGOTPASS' => 'Elfelejtett jelszó fájl neve',
		'FILE_MESSAGES_READ' => 'Üzenet olvasó fájl neve',
		'FILE_MESSAGES_WRITE' => 'Üzenet író fájl neve',
		'FILE_MESSAGES_NEWS' => 'Hírek fájlja',
		'FILE_MESSAGES_INBOX' => 'Bejövő levelek fájlja',
		'FILE_MESSAGES_OUTBOX' => 'Kimenő levelek fájlja',
		'FILE_USERLIST' => 'User lista',
	    'FILE_SEARCH' => 'User kereső',
		'FILE_PROTECTED_SITE' => 'Védett oldalról jogosultság<br />
								hiba esetén ide irányít',
		'FILE_LOGOUT' => 'Kijelentkezés',
		'MAX_ONLINE_TIME' => 'Hány mp-ig számítson <br />onlinenak egy user?',
		'SYMLINK' => 'PATH prefix (pl symlink esetén)<br /> <small>'.
					'Hagyd üresen, ha nem tudod, mi ez. </small> '
	);


$reflection = new ReflectionClass('Config_tpl');
$constants = $reflection->getConstants();

$run = false;
$msg = "";
$installed = true;
if (!file_exists('../classes/Config.class.php'))
{
	$installed = false;
	if ($install = isset ($_POST['install']))
	{
		$constants = $_POST['install'];
		$constants['SMTP_ON'] = ($constants['SMTP_ON'] == 'on') ? true : false;
		$constants['SMTP_AUTH'] = ($constants['SMTP_AUTH'] == 'on') ? true : false;
		$constants['REG_BLOCKED'] = ($constants['REG_BLOCKED'] == 'on') ? true : false;
		$constants['LOGIN_BLOCKED'] = ($constants['LOGIN_BLOCKED'] == 'on') ? true : false;
		$constants['INVITATION_MODE'] = ($constants['INVITATION_MODE'] == 'on') ? true : false;
		$constants['EMAIL_ACTIVATION'] = ($constants['EMAIL_ACTIVATION'] == 'on') ? true : false;
		require_once 'Install.class.php';
		$msg = "A telepítés befejeződött!";
		if (!($run = Install::run($constants, $titles )))
		{
			$msg = "A telepítés közben hiba történt:<br /> ".PHP_EOL;
			foreach (Install::errors() as $error);
			{
				$msg .= $error."<br />".PHP_EOL;
			}
			Install::uninstall($constants['DBPREF']);
		}
	}
}
?>
