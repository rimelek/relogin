<?php
/**
 * R.E. Login 2.0 - A rendszer magja - class/System.class.php
 *
 * A rendszerrel összefüggő műveletek itt történnek. Itt inicializálja a
 * login rendszerváltozókat, amikre bármelyik osztályban szükség lehet,
 * és mindig beállítottnak kell lenniük addigra már. <br />
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
 * A rendszer magja
 *
 * Egyke osztály. Példányosítás:
 * <code>$system = System::getInstance();</code>
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
final class System
{
	public static
		/**
		 * Konfig adatokat tartalmazó objektum
		 * 
		 * @var Config
		 */
		$config,

		/**
		 * User be van-e lépve
		 *
		 * @var bool
		 */
		$logged=false,

		/**
		 * Belépés után a user maga, lekérhető adataival
		 *
		 * @var User
		 */
		$user,

		/**
		 * Session
		 *
		 * @var Session
		 */
		$session;

	private static

		/**
		 * A weboldal gyökérmappája. include-hoz / jellel végződik
		 *
		 * @var string
		 */
		$INC_SITEDIR = './',

		/**
		 * A Login gyökérmappája. include-hoz / jellel végződik
		 *
		 * @var string
		 */
		$INC_LOGINDIR = './',

		/**
		 * A szerver gyökér mappája
		 *
		 * @var string
		 */
		$BASEDIR='./',

		/**
		 * A weboldal gyökere a doc root-tól
		 *
		 * @var string
		 */
		$SITEDIR='./',

		/**
		 * Időbélyeg. Szerveridőtől különbözhet
		 *
		 * @var int
		 */
		$TIME;
	
	private static

		/**
		 * System instancia
		 *
		 * @var System
		 */
		$instance;

	/**
	 * Üres konstruktor
	 * 
	 * @ignore
	 */
	private function __construct() 
	{
		
	}

	/**
	 * Rendszer inicializálása
	 *
	 * Adatbázis kapcsolat létrehozás, szükséges osztályok beemelése, nyelv beállítás, stb
	 */
	public function init()
	{
		self::setSlashes($_POST,false);
		//self::setSlashes($_GET, false);

		self::setIncSiteDir();
		require_once self::$INC_LOGINDIR."classes/Config.class.php";
		self::setBaseDir();
		self::setSitedir();

		require_once self::$INC_LOGINDIR.'libs/REDBObjects/REDBObjects.class.php';
		REDBObjects::uses('mysql');

		require_once self::$INC_LOGINDIR.'classes/Session.class.php';
		require_once self::$INC_LOGINDIR."classes/Login.class.php";
		require_once self::$INC_LOGINDIR.'classes/Url.class.php';

		self::$TIME = time();

		self::$config = Config::getInstance();

		self::dbconnect();
		self::dbselect();

		self::$session = Session::getInstance();

		try
		{
			self::$logged = Login::authUser('username','userpass');
		}
		catch (LoginException $e)
		{
			self::$logged = false;
			Login::addError($e->getMessage());
			Login::logout();
		}

		if (isset($_GET['logout'])) self::logout();
		self::runIfLogged();

	}

	/**
	 * Adatbázisszerver kapcsolat létrehozása
	 */
	private static function dbconnect()
	{
		mysql_connect(
			self::$config->DBHOST,
			self::$config->DBUSER,
			self::$config->DBPASS);
		mysql_query("set names '".self::$config->DBCHARSET."' collate '".self::$config->DBCOLLATE."'");		
	}

	/**
	 * Adatbázis kiválasztása
	 */
	private static function dbselect()
	{
		mysql_select_db(self::$config->DBNAME);
	}

	/**
	 * Idő lekérdezése
	 *
	 * A time() függvényhez hasonlóan működik. Csak rosszul működő szerveróra esetén
	 * már a módosított időt tartalmazza. 
	 *
	 * @return int
	 */
	public static function getTime()
	{
		return self::$TIME;
	}

	/**
	 * TimeStamp lekérdezése. Y-m-d H:i:s formátumban
	 *
	 * @return string
	 */
	public static function  getTimeStamp()
	{
		return date('Y-m-d H:i:s',System::getTime());
	}

	/**
	 * Login gyökér beállítása az includehoz
	 */
	private static function setIncLoginDir()
	{
		//változó definiálása az oldal könyvtárához, az include használata esetén
		self::$INC_LOGINDIR = str_replace('\\', '/', dirname(dirname(__FILE__)) . '/');
	}

	/**
	 * Login gyökér lekérdezése az includehoz
	 *
	 * @return string Login gyökér az includehoz
	 */
	public static function getIncLoginDir()
	{
		return self::$INC_LOGINDIR;
	}

	/**
	 * Weboldal gyökér beállítása szerver gyökértől
	 */
	private static function setIncSiteDir()
	{
		self::setIncLoginDir();
		//változó definiálása az oldal könyvtárához, az include használata esetén
		self::$INC_SITEDIR = dirname(self::getIncLoginDir()).'/';
	}	


	/**
	 * Weblap gyökér lekérdezése a szerver gyökértől
	 *
	 * @return string Weblap gyökér az includehoz
	 */
	public static function getIncSitedir()
	{
		return self::$INC_SITEDIR;
	}

	/**
	 * Tárhely gyökér beállítása
	 */
	private static function setBaseDir()
	{
		//Könyvtárak száma a gyökértől
		$dir_count = substr_count($_SERVER['PHP_SELF'], '/') - 1;
		//Változó definiálása gyökérkönyvtárra
		self::$BASEDIR = str_repeat('../', $dir_count);
	}

	/**
	 * Tárhelygyökér lekérdezése
	 * 
	 * @return string tárhely gyökér
	 */
	public static function getBasedir()
	{
		return self::$BASEDIR;
	}

	/**
	 * Weblap gyökér beállítása linkekhez
	 *
	 * Tárhely gyökértől számított útvonal
	 *
	 */
	private static function setSitedir()
	{

		$path_base = trim(Config::PATH_BASE);
		$path = '';
		$matches = array(
			'pref' => '/',
			'base' => $path_base
		);
		
		if (!preg_match('~^(?P<pref>(https?:/)?/)(?P<base>.*)$~', $path_base, $_matches) or !$path_base) 
		{
			$explode_fn = explode('/', str_replace("\\\\","/",$_SERVER['SCRIPT_FILENAME']));
			for ($i = 0; $i < substr_count($_SERVER['PHP_SELF'], '/'); $i++)
			{ 
				array_pop($explode_fn);
			}

			$fn_count = count($explode_fn);
			$explode_isd = array_reverse(explode('/', self::$INC_SITEDIR));
			for ($i = 0; $i < $fn_count; $i++)
			{
					array_pop($explode_isd);
			}

			$explode_isd = array_reverse($explode_isd);
			$path = implode('/', $explode_isd); 
		} else {
			$matches = $_matches;
		}

		$sitedir = $matches['base'] .'/'. $path.'/';
		self::$SITEDIR = $matches['pref'].ltrim(preg_replace('~[/]+~', '/', $sitedir),'/');
	} 

	/**
	 * Weblap gyökér a tárhely gyökértől számítva
	 *
	 * @return string Weblap gyökér linkekhez
	 */
	public static function getSitedir()
	{
		return self::$SITEDIR;
	}

	/**
	 * Login mappa útvonala a tárhely gyökértől
	 *
	 * @return string Login gyökér linkekhez
	 */
	public static function getLogindir()
	{
		return self::getSitedir().basename(self::getIncLoginDir()).'/';
	}

	/**
	 * Weblap gyökér url-je HTTP-vel
	 * 
	 * @return string
	 */
	public static function getSitedirWithHTTP()
	{
		$url = self::getSitedir();
		if (!preg_match('~^https?://.*$~', $url)) {
			$url = 'http://'.$_SERVER['HTTP_HOST'].$url;
		}
		return $url;
	}

	/**
	 * Login mappa url-je HTTP-vel
	 *
	 * @return string
	 */
	public static function getLoginDirWithHTTP()
	{
		$url = self::getLogindir();
		if (!preg_match('~^https?://.*$~', $url)) {
			$url = 'http://'.$_SERVER['HTTP_HOST'].$url;
		}
		return $url;
	}
	/**
	 * URL a sitedir-től számítva
	 * 
	 * @return string
	 */
	public static function getURIFromSitedir()
	{
		$sitedir = self::$SITEDIR;
		$uri = $_SERVER['REQUEST_URI'];
		$ret = substr($uri, strlen($sitedir));
	
		if (isset($_GET['lang']))
		{
			$ret = (($pos = strpos($ret,'/')) !== false) ?
					substr($ret,strpos($ret,'/')) : '';
		}
		return trim($ret,'/');
	}

	/**
	 * Tömb elemeinek backslashelt vagy nem backslashelt verzióra állítása
	 *
	 * @param mixed $string array vagy string. Tömb esetén rekurzivan működik
	 * @param boolean $bool true, ha a felhasználói adatbeviteleket backslashelni akarjuk. Amúgy false
	 */
	public static function setSlashes(&$string,$bool=false)
	{
		if (!is_array($string))
		{
			if ($bool) {
				if (!get_magic_quotes_gpc())
				{
					$string = addslashes($string);
				} 
			}
			else
			{ 
				if (get_magic_quotes_gpc())
				{
					$string = stripslashes($string);
				}
			}
			return;
		}
		foreach ($string as $key => &$str) {
			self::setSlashes($str, $bool);
		}
	}

	/**
	 * Példány lekérése
	 * 
	 * @return System
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$c=__CLASS__;
			self::$instance = new $c; 
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Statikus tulajdonságok elérhetővé tétele nem statikusan
	 *
	 * @ignore
	 * @param string $var
	 * @return mixed
	 */
	function __get($var)
	{
		if (!isset($this->$var)) 
		{
			$ret = self::$$var;
			return $ret;
		}
	}

	/**
	 * Kiléptetés
	 *
	 * Csak akkor, ha $_GET['relogin_logout'] létezik, és értéke 1
	 * Utána eltünteti az url-ből ezt a változót.
	 */
	public static function logout()
	{
		if (!isset($_GET['relogin_logout']) or $_GET['relogin_logout'] != 1)
		{
			return;
		}
		Login::logout();
		self::$logged = false;
		$url = Url::set(array(
			'relogin_logout' => null
		), Config::FILE_LOGOUT, '&amp;');
 		self::redirect($url);
	}

	/**
	 * Kijelentkező link
	 *
	 * @return string
	 */
	public static function logoutLink()
	{
		return Url::set(array(
			'relogin_logout'=>1
		),System::getSitedir().Config::FILE_LOGOUT,'&amp;');
	}

	/**
	 * Átirányítás egy oldalra
	 * 
	 * @param string $site Erre az oldalra irányít át
	 */
	public static function redirect($site=null)
	{
		if (!$site)
		{
			$site = $_SERVER['REQUEST_URI'];
		}
		header('Location: '.$site);		
	}

	/**
	 * Ha a felhasználó nem jogosult az oldal megtekintésére, átirányítja az
	 * index oldalra. 
	 *
	 * @param mixed $rank Az oldalt megtekinthető felhasználók rangja, ha string.
	 *		Ha tömb, akkor több rang engedélyezhető.
	 *		Ha nincs megadva ez a paraméter, akkor csak belépett felhasználók
	 *		nézhetik az oldalt. 
	 */
	public static function protectedSite($rank=null)
	{
		
		if (!$rank and System::$logged) return;
		require_once System::getIncLoginDir().'classes/Ranks.class.php';

		if (System::$user->rank($rank))
		{
			return;
		}

		$site = Config::FILE_PROTECTED_SITE;
		if (!file_exists($site))
		{
			$site = isset($_SERVER['HTTP_REFERER'])
				? $_SERVER['HTTP_REFERER']
				: null;
		}
		
		if (!is_null($site))
		{
			if (!isset($_SESSION['redirected']))
			{
				$_SESSION['redirected'] = true;
				self::redirect($site);
				exit;
			}
			else
			{
				self::redirect(System::getSitedir());
			}
		}
		ob_end_clean();
		exit('Protected site');
	}
	
	/**
	 * E-mail küldése
	 *
	 * @see http://phpmailer.worxware.com/
	 * @see PHPMailer
	 *
	 * @param string $to címzett, ha $params['toadmin] = false, egyébként válaszcím
	 * @param string $subject Tárgy
	 * @param string $body Tartalom
	 * @param array $params Egyéb paraméterek tömbje:<br />
	 *			boolean $html True, ha html levél, false, ha szöveges<br />
	 *			boolean $toadmin True, ha adminnak megy a levél a usertől. Egyébként false. default: false
	 */
	public static function sendEmail($to,$subject,$body,$params=array())
	{
		$html = isset($params['html']) ? (bool)$params['html'] : true;
		$toadmin = isset($params['toadmin']) ? $params['toadmin'] : false;

		require_once self::getIncLoginDir().'libs/PHPMailer/class.phpmailer.php';
		$mailer = new PHPMailer();

		if (Config::SMTP_ON)
		{
			$mailer->IsSMTP();

			$mailer->Host = Config::SMTP_HOST.':'.Config::SMTP_PORT;
			if ($mailer->SMTPAuth = Config::SMTP_AUTH) {
				$mailer->Username = Config::SMTP_USERNAME;
				$mailer->Password = Config::SMTP_PASSWORD;
			}
		}
		$mailer->Subject = "=?UTF-8?B?".base64_encode($subject)."?=";


		$from = Config::MAIL_FROM;
		$http_host = $_SERVER['HTTP_HOST'];
		if (substr($http_host,0,4) == 'www.')
		{
			$http_host = substr($http_host, 4);
		}
		$http_host = "=?UTF-8?B?".base64_encode($http_host)."?=";

		$mailer->SetFrom($from, $http_host);
		if ($toadmin !== true)
		{
			$mailer->AddAddress($to);
		}
		else
		{ 
			$mailer->AddAddress(Config::MAIL_TO);
			$mailer->AddReplyTo($to);
		}
		
		
		$type = ($html) ? 'html' : 'plain';
		$footer = @file_get_contents(self::getIncLoginDir().'includes/email/email_footer-'.$type.'.html');
		$footer = str_replace(array(
			'{website}', '{email}'
		),array(
			self::getSitedirWithHTTP(),
			Config::MAIL_TO
		), $footer);
		$mailer->CharSet = 'UTF-8';
		$mailer->Body = $body.$footer;
		$mailer->IsHTML($html);

		if (($err = $mailer->send()) !== true) {
			ob_end_clean();
			exit('Hiba az email küldésnél');
		}
	}

	/**
	 * Akkor fut le, ha be van jelentkezve a felhasználó.
	 *
	 * Online idő állítása, frissítési idő módosítása
	 */
	private static function runIfLogged()
	{
		if (!self::$logged) return;

		if (self::$user->refreshtime and
			($refreshtime = strtotime(self::$user->refreshtime)) >= self::getTime() - Config::MAX_ONLINE_TIME)
		{
			self::$user->onlinetime += self::getTime() - $refreshtime;
		}
		self::$user->refreshtime = self::getTimeStamp();
		self::$user->update();
	}


	/**
	 * Üzenetek statisztika
	 *
	 * @see Messages::msgStat()
	 *
	 * @return array
	 */
	public static function msgStat()
	{
		require_once self::getIncLoginDir().'classes/Messages.class.php';
		return Messages::msgStat();
	}
}

?>
