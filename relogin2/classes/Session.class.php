<?php
/**
 * R.E. Login 2.0 - Session - class/Session.class.php
 *
 * Munkamenetek kezelése<br />
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
 * Munkamenetek
 *
 * Adatbázisban tárolja a munkameneteket. Beállítható megjegyezze, és az is,
 * hogy meddig jegyezze meg. Ezt a {@link setLifetime()} metódussal lehet megadni. 
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Session
{
	/**
	 * Hány másodpercig jegyezze meg a munkamenetet.
	 * Ha nulla, akkor megszűnik.
	 *
	 * @var int
	 */
	private $lifetime = 0;

	/**
	 * Munkamenet példánya
	 *
	 * @var Session
	 */
	private static $instance = null;

	/**
	 * Munkamenet példánya
	 *
	 * @return Session
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * Munkamenet kezelő előkészítése
	 *
	 * @ignore
	 */
	private	function __construct()
	{
		session_set_save_handler(
			array($this,'open'),
			array($this,'close'),
			array($this,'read'),
			array($this,'write'),
			array($this,'destroy'),
			array($this,'gc')
		);

		session_start();

		if (isset($_COOKIE['cremember']))
		{
			$this->lifetime = (int)$_COOKIE['cremember'];
		}
	}

	/**
	 * Munkamenet élethossza másodpercben.
	 *
	 * $lifetime másodpercig marad életben a munkamenet.
	 * Ha $lifetime nulla, akkor megszünteti a létező munkamenetet. 
	 *
	 * @param int $lifetime
	 */
	public function setLifetime($lifetime)
	{
		$this->lifetime = $lifetime;
	
		setcookie(session_name(),session_id(),$lifetime!=0 ? time()+$lifetime : 0,'/');
		$this->rememberMe($lifetime > 0);
	}

	/**
	 * Jegyezze-e meg a munkamenetet
	 *
	 * @param bool $bool True esetén {@link $lifetime} ideig megjegyez.
	 *				Egyébként megszüntet. 
	 */
	public function rememberMe($bool=true)
	{
		if ($bool)
		{
			setcookie('cremember', $this->lifetime,System::getTime()+$this->lifetime,'/');
		}
		else
		{
			setcookie('cremember', $this->lifetime,System::getTime()-1000,'/');
		}
	}

	/**
	 * Munkamenet megnyitása
	 *
	 * @ignore
	 *
	 * @param string $path Session útvonala a fájlrendszeren. 
	 * @param sting $name Session neve
	 * @return bool Minidg true
	 */
	public function open($path,$name)
	{	
		return true;
	}

	/**
	 * Munkamenet zárása
	 *
	 * @ignore
	 *
	 * @return bool Mindig true
	 */
	function close()
	{
		return true;
	}

	/**
	 * Munkamenet adatok lekérdezése
	 *
	 * @ignore
	 *
	 * @param string $sess_id Munkamenet azonosító
	 * @return string Sorosított tömb
	 */
	function read($sess_id)
	{
		if ($sess = mysql_fetch_row(mysql_query("select sess_data from ".Config::DBPREF."sessions where sess_id = '$sess_id'")))
		{
			return $sess[0];
		}
		else
		{
			return '';
		}
	}

	/**
	 * Munkamenet írása
	 *
	 * @ignore
	 *
	 * @param string $sess_id Munkamenet azonosító
	 * @param string $sess_data Munkamenet adatok sorosított tömbje
	 * @return bool Sikeres volt-e az írás
	 */
	function write($sess_id,$sess_data )
	{
		$uid = System::$logged ? System::$user->T_users_userid : 0;
		$sess_data = mysql_real_escape_string($sess_data);
		return (is_resource(@mysql_query("replace ".
				Config::DBPREF."sessions set sess_id = '$sess_id', sess_data = '$sess_data', mtime = '".
				System::getTime()."', remember = ".(int)$this->lifetime.", uid = $uid")));
	}

	/**
	 * Munkamenet megszüntetése
	 *
	 * @ignore
	 *
	 * @param string $sess_id Munkamenet azonosító
	 * @return bool Sikerült-e a munkamenet megszüntetése
	 */
	function destroy($sess_id)
	{
		return (is_resource(@mysql_query("delete from ".Config::DBPREF."sessions where sess_id = '$sess_id'")));
	}


    /**
	 * Felesleges munkamenetek törlése az adatbázisból. 
	 *
	 * @param int $maxlifetime Munkamenet élettartama. 
	 * @return bool Mindig true
	 */
	function gc($maxlifetime)
	{
		$mtime = System::getTime() - $maxlifetime;
		$time = System::getTime();
		mysql_query("delete from ".Config::DBPREF."sessions where mtime < if(remember = 0,$mtime,$time - remember)");
		return true;
	}
	
}


?>
