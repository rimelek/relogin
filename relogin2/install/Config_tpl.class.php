<?php
/**
 * R.E. Login 2.0 - Telepítő - Config_tpl.class.php
 *
 * A config fájl sablonja. A telepítőben ezen értékek lesznek az űrlapon
 * megjelenítve alapértelmezetten. Ebből lesz legenerálva majd a végleges
 * Config.class.php a classes mappában a telepítés végeztével.<br />
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
 * Config osztály sablonja
 *
 * A létrehozandó Config osztály alapértelmezett értkeit tartalmazza.
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
final class Config_tpl
{
	/**
	 * Adatbázis hoszt
	 */
	const DBHOST = 'localhost';

	/**
	 * Adatbázis név
	 */
	const DBNAME = '';

	/**
	 * Adatbázis Felhasználó
	 */
	const DBUSER = '';

	/**
	 * Adatbázis Jelszó
	 */
	const DBPASS = '';

	/**
	 * Tábla prefix
	 */
	const DBPREF = 'relogin_';

	/**
	 * Karakterkódolás pl. (utf8)
	 */
	const DBCHARSET = 'utf8';

	/**
	 * Egybevetés
	 */
	const DBCOLLATE = 'utf8_general_ci';

	/**
	 * Felhasználónév min hossza
	 */
	const MINLENGTH_USERNAME = '3';

	/*
	 * Felhasználónév max hossza
	 */
	const MAXLENGTH_USERNAME = '20';

	/**
	 * Jelszó min hossza
	 */
	const MINLENGTH_PASSWORD = '5';

	/**
	 * Felhasználó pattern
	 */
	const USERNAME_PATTERN = '^[a-z0-9_]+$';

	/**
	 * Levelek küldése smtp-n keresztül történjen-e. 
	 */
	const SMTP_ON = false;

	/**
	 * smtp szolgáltató
	 */
	const SMTP_HOST = 'localhost';

	/**
	 * smtp port
	 */
	const SMTP_PORT = '25';

	/**
	 * Hitelesített kapcsolat
	 */
	const SMTP_AUTH = false;

	/**
	 * STMP felhasználó
	 */
	const SMTP_USERNAME = '';

	/**
	 * SMTP jelszó
	 */
	const SMTP_PASSWORD = '';

	/**
	 * E-mail cím (Feladóként)
	 */
	const MAIL_FROM = '';

	/**
	 * E-mail cím (Válaszcím)
	 */
	const MAIL_TO = '';

	/**
	 * Blokkolt regisztráció
	 */
	const REG_BLOCKED = false;

	/**
	 * Blokkolt belépés
	 */
	const LOGIN_BLOCKED = false;

	/**
	 * Meghívó mód
	 */
	const INVITATION_MODE = true;

	/**
	 * Email aktiválás szükséges-e
	 */
	const EMAIL_ACTIVATION = true;

	/**
	 * Ennyi ideig marad online az online lista szerint a user
	 */
	const MAX_ONLINE_TIME = 300;

	/**
	 * Főoldal fájlja
	 */
	const FILE_HOME = 'index.php';

	/**
	 * Regisztrációs fájl neve
	 */
	const FILE_REGISTER = 'register.php';

	/**
	 * Profil fájl neve
	 */
	const FILE_PROFILE = 'profile.php';

	/**
	 * Profil módosítás fájl neve
	 */
	const FILE_CHANGE_PROFILE = 'changeprofile.php';

	/**
	 * Elfelejtett jelszó fájl
	 */
	const FILE_FORGOTPASS = 'forgotpass.php';

	/**
	 * Üzenet olvasás fájlja
	 */
	const FILE_MESSAGES_READ = 'msgread.php';

	/**
	 * Bejövő üzenetek fájlja
	 */
	const FILE_MESSAGES_INBOX = 'msginbox.php';

	/**
	 * Kimenő üzenetek fájlja
	 */
	const FILE_MESSAGES_OUTBOX = 'msgoutbox.php';

	/**
	 * Üzenet írás fájlja
	 */
	const FILE_MESSAGES_WRITE = 'msgwrite.php';

	/**
	 * Hírek fájlja
	 */
	const FILE_MESSAGES_NEWS = 'news.php';

	/**
	 * Felhasználó lista fájlja
	 */
	const FILE_USERLIST = 'userlist.php';

	/**
	 * Felhasználó kereső fájlja
	 */
	const FILE_SEARCH = 'search.php';

	/**
	 * Kijelentkezés fájlja
	 */
	const FILE_LOGOUT = 'logout.php';

	/**
	 * Védett oldalra lépés esetén ide irányít.
	 */
	const FILE_PROTECTED_SITE = 'index.php';

	/**
	 * Weboldal gyökérkönyvtára. Üresen hagyva a login maga számolja ki.
	 * Megadható tárhelygyökértől: /weblapom
	 * vagy relatívan: weblapom
	 * Utóbbi esetben a login által számított útvonal elé teszi. /-el 
	 * vagy http-vel kezdve viszont lecseréli azt. 
	 */
	const PATH_BASE = '';

	/**
	 * Példány változója
	 *
	 * @ignore
	 * @var Config_tpl
	 */
	private static $instance;

	/**
	 * Privát konstruktor
	 * @ignore
	 */
	private function __construct(){}

	/**
	 * @ignore
	 */
	function __get($var)
	{
		if (!isset($this->$var))
		{
			eval('$ret = self::'.$var.';');
			return $ret;
		}
	}
	/**
	 * @ignore
	 * @return Példány lekérdezése
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$c=__CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
}
?>