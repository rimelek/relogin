<?php
/**
 * R.E. Login 2.0 - Login - class/Login.class.php
 *
 * Bejelentkezés műveletei<br />
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
require_once System::getIncLoginDir().'classes/User.class.php';

/**
 * Login műveleteit tartalmazó osztály
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @see InvalidAccountException
 * @see NotActivatedException
 * @see LoginBlockedException
 * @see BannedException
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Login
{
	/**
	 * Hibák tömbje
	 *
	 * @var array
	 */
	private static $errors=array();

	/**
	 * Usernevet tartalmazó input mező neve
	 *
	 * @var string
	 */
	private static $userNameVar;

	/**
	 * Jelszót tartalmaző input mező neve
	 *
	 * @var string
	 */
	private static $userPassVar;

	/**
	 * Ha épp most loginolt be a user, akkor true. Egyébként false
	 *
	 * @var bool
	 */
	private static $submit = false;

	/**
	 * Új hibaüzenet hozzáadása
	 *
	 * @param string $error Hibaüzenet
	 */
	public static function addError($error)
	{
		self::$errors[] = $error;
	}

	/**
	 * Usernév és jelszóhash sessionbe helyezése (Referenciaként paraméterekben visszaadása)
	 * 
	 * @param string $userName Usernév mező neve. Usernév kerül bele utána
	 * @param string $userPass Jelszó mező neve, Jelszó hash kerül bele utána
	 */
	private static function init(&$userName,&$userPass) 
	{
		if(self::$submit = isset($_POST['login']))
		{
			Session::getInstance()->setLifetime( isset($_POST['login']['remember']) ? 360*24*3600 : 0 );
		}
		if (isset($_POST['login'][$userName]) and self::$submit)
		{
			$_SESSION['s'.$userName] = $_POST['login'][$userName];
		}
		if (isset($_POST['login'][$userPass]) and self::$submit)
		{
			$_SESSION['s'.$userPass] = self::getPasswordHash($_POST['login'][$userPass]);
		} 
		if (!isset($_SESSION['s'.$userName]))
		{
			$_SESSION['s'.$userName] = '';
		}
		if (!isset($_SESSION['s'.$userPass]))
		{
			$_SESSION['s'.$userPass] = '';
		}
		$userName = $_SESSION['s'.$userName];
		$userPass = $_SESSION['s'.$userPass];
	}

	/**
	 * User beléptetése
	 * 
	 * @param string $userName Usernév input mező neve
	 * @param string $userPass Jelszó input mező neve
	 * @return boolean True, ha beléphet, false, ha nem léphet be
	 */
	public static function authUser($userName,$userPass)
	{
		self::$userNameVar=$userName;
		self::$userPassVar=$userPass;
		self::init($userName,$userPass);

		$user = System::$user = new User($userName,$userPass);
		
		if (self::$submit and isset($_POST['login'][self::$userNameVar]) and  empty($user->username))
		{
			require_once System::getIncLoginDir().'classes/exceptions/InvalidAccountException.class.php';
			throw new InvalidAccountException("Hibás név / jelszó!");
		}
		else if (!empty($user->T_profiles_useremail) and $user->T_users_useremail != $user->T_profiles_useremail)
		{
			require_once System::getIncLoginDir().'classes/exceptions/NotActivatedException.class.php';
			throw new NotActivatedException("Aktiváld hozzáférésed az emailben kapott linkkel!");
		}
		else if ($user->rank('banned'))
		{
			require_once System::getIncLoginDir().'classes/exceptions/BannedException.class.php';
			throw new BannedException("Hozzáférésed tiltva van!");
		}
		$return = !empty($user->username) and !count(self::$errors);

		if ($return and Config::LOGIN_BLOCKED and !$user->rank(array('admin','owner')))
		{
			require_once System::getIncLoginDir().'classes/exceptions/LoginBlockedException.class.php';
			throw new LoginBlockedException("A belépés ideiglenesen tiltva. Próbálkozz később!");
		}

		if (self::$submit and $return)
		{
			$user->logintime = date('Y-m-d H:i:s');
			$user->onlinestatus = 1;
		}
		return $return;
	}

	/**
	 * Hibák visszaaádsa
	 *
	 * @return array
	 */
	public static function getErrors() 
	{
		return self::$errors;	
	}

	/**
	 * User kiléptetése
	 */
	public static function logout()
	{
		$_SESSION['s'.self::$userNameVar]='';
		$_SESSION['s'.self::$userPassVar]='';
		if (System::$logged)
		{
			System::$user->onlinestatus = 0;
			System::$user->update();
		}
		Session::getInstance()->setLifetime(0);
	}

	/**
	 * Jelszóhash generálása
	 *
	 * Megadható a sózáshoz használt string a metódusban. 
	 *
	 * @param string $password Jelszó
	 * @return string Sózott jelszóhash
	 */
	public static function getPasswordHash($password)
	{
		return md5(sha1($password . " ") ." R.E. Login 2.0 " . sha1($password));
	}

	/**
	 * Megváltoztatja a session-ben levő jelszóhasht. 
	 *
	 * @param string $password
	 */
	public static function changePassword($password)
	{
		$_SESSION['s'.self::$userPassVar] = $password;
	}
}
?>
