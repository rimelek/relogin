<?php
/**
 * R.E. Login 2.0 - Regisztráció - class/Register.class.php
 *
 * Regisztráció és aktiváció.<br />
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
require_once System::getIncLoginDir().'classes/Login.class.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/UserList.class.php';


/**
 * Regisztráció, és aktiváció
 *
 * Csak egy kérelmet kell indítani. A többi automatikus.
 * A kérelem visszaadja sikeres volt-e vagy sem. 
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Register 
{
	/**
	 * Hibák tömbje
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Űrlapmezők neveit kulcsként tartalmazó asszociatív tömb
	 * 
	 * @var array
	 */
	private static $values = array(
		'username'=>'','userpass'=>'','reuserpass'=>'','useremail'=>'','reuseremail'=>'',
		'bdyear'=>'','bdmonth'=>'','bdday'=>'','sex'=>'','public_mail'=>''
	);

	/**
	 *
	 * @return array Lekérdezendő táblák+mezők
	 */
	public static function getRecord()
	{
		return array(
			Config::DBPREF.'users as users' => array(
				'username','userpass','rank','invitations','useremail','regtime','newsreadtime'
			),
			Config::DBPREF.'profiles as profiles' => array(
				'birthdate','sex','useremail','public_mail',
			)
		);
	}

	/**
	 * Űrlapmezők neveit kulcsként tartalmazó tömb visszaadása
	 * 
	 * @return array
	 */
	public static function getValues()
	{
		return self::$values;
	}

	/**
	 * Megvizsgálja, érvényesek-e a megadott adatok
	 *
	 * @param User $user Példányosított User objektum
	 * @return boolean True, ha nincs hiba. És false, ha hibásak az adatok
	 */
	public static function isValidInput($user)
	{
		$pref = Config::DBPREF;
		require_once System::getIncLoginDir().'libs/PHPMailer/class.phpmailer.php';
		//felhasználó név hossza
		if (($len = strlen($user->username)) < (int)Config::MINLENGTH_USERNAME
		or $len > (int)Config::MAXLENGTH_USERNAME)
		{
			self::$errors[] = "A név ".Config::MINLENGTH_USERNAME.
				"-".Config::MAXLENGTH_USERNAME." karakter hosszú lehet!";
		}
		else if (!preg_match('/'.Config::USERNAME_PATTERN.'/i',$user->username)
			or preg_match_all('/[0-9]{1}/i',$user->username,$result) > 4
			or preg_match_all('/_{1}/',$user->username,$result) > 1){
			self::$errors[] = "A felhasználónév nem megfelelő formátumú!<br />
						(0-9: max 2db, a-z karakterek és 1 darab '_' karakter)";
		}
		else if (UserList::exists('username',$user->username,false))
		{
			self::$errors[] = "Foglalt felhasználó név!";
		} 
		//emailcím elenőrzése
		if (!PHPMailer::ValidateAddress($user->T_profiles_useremail))
		{
			self::$errors[] = "Az e-mail cím érvénytelen!";
		}
		else if (UserList::exists('useremail', $user->T_profiles_useremail))
		{
			self::$errors[] = "Van már ilyen e-mail cím az adatbázisban! ";
		}
		else if (trim($user->T_profiles_useremail) != trim($_POST['register']['reuseremail']))
		{
			self::$errors[] = "A két e-mail cím nem egyezik!";
		}
		

		//jelszó vizsgálata
		if (mb_strlen($user->userpass,Config::DBCHARSET) < ($min = Config::MINLENGTH_PASSWORD)) {
			self::$errors[] =  "A jelszó minimum $min karakter lehet!";
		} else if ($user->userpass != Login::getPasswordHash($_POST['register']['reuserpass'])){
			self::$errors[] = "A két jelszó nem egyezik!";
		}

		foreach(self::$values as $key => $rec)
		{
			$tmp = (!isset($_POST['register'][$key])) ? '' : trim($_POST['register'][$key]);
			if (empty($tmp))
			{
				self::$errors[] = "Minden mező kitöltése kötelező!";
				break;
			}
		}

		if (!isset($_POST['code']) or !isset($_SESSION['captcha_code']) or
				strtolower($_POST['code']) != strtolower($_SESSION['captcha_code']))
		{
			self::$errors[] = "Érvénytelen ellenőrzőkód!";
		}

		if (count(self::$errors) )
		{
			return false;
		}
		return true;
	}

	/**
	 * Aktivációs link létrehozása
	 * 
	 * @param int $id User id-je
	 * @param string $email User email címe
	 * @param string $file Fájlnév, ahova az aktivációs link mutat
	 * @return string Aktivációs link
	 */
	public static function createLink($id,$email,$file=null)
	{
		if ($file === null)
		{
			$file = basename($_SERVER['PHP_SELF']);
			$get = $_GET;
		}
		else
		{
			$url = parse_url($file);
			$file = $url['path'];
			if (isset($url['query']))
			{
				parse_str($url['query'],$get);
			}
		}
	
		$get['id'] = $id;
		$get['code'] = self::createHash($id, $email);
		return
			'http://'.$_SERVER['HTTP_HOST'].System::getSitedir().$file.
			'?'.http_build_query($get, '', '&amp;');
	}

	/**
	 * Az aktivációs linkhez a code hash létrehozása
	 * 
	 * @param int $id User id-je
 	 * @param string $email User email címe
	 * @return string Code hash Base64 formában
	 */
	public static function createHash($id,$email)
	{
		return base64_encode(sha1($id.md5($email)));
	}

	/**
	 * Regisztráció kérelem
	 *
	 * @return mixed False, ha nem sikerült a regisztráció. Egyébként az új
	 *			felhasználó azonosítója. 
	 */
	public static function request()
	{
		if (!self::isValidInput(($user = self::createUser()))) {
			return false;
		} 
		$userid = self::addUser($user);
		//Ha az email aktiválás ki van kapcsolva, nem küldi ki az aktiváló emailt
		//de akkor sem, ha admin regisztrált
		if (!Config::EMAIL_ACTIVATION)
		{
			return $userid;
		}

		$link = self::createLink($userid,$user->T_profiles_useremail);
		$body = 
			"Kedves ".$user->username."!<br /><br />".PHP_EOL.
			"Valaki regisztrált a nevedben az oldalunkra. ".
			"Amennyiben nem te voltál, nyugodtan hagyd figyelmen kívül a levelet.<br /><br />".PHP_EOL.
			"A következő linkre kattintva aktiválhatod a hozzáférésedet:<br />".PHP_EOL.
			"<a href='$link'>$link</a>";
		System::sendEmail($user->T_profiles_useremail, "Regisztráció", $body);

		return $userid;
	}

	/**
	 * User felvétele adatbázisba
	 *
	 * Az {@link IsMySQLListClass} osztály gondoskodik az objektumként kapott user adatbázisba viteléről
	 *
	 * @param User $user Példányosított User objektum
	 * @return int User id-je
	 */
	public static function addUser($user) 
	{
		$DB = new IsMySQLListClass(self::getRecord(),'User');
		$user->keyName = 'userid';
		return $DB->add($user);
	}

	/**
	 * Egy új User létrehozása
	 * 
	 * @return User Már az elészült User objektum
	 */
	protected static function createUser()
	{
		$rank = UserList::countUsers()
			? Ranks::getIdByVar('user')
			: Ranks::getIdByVar('owner');

		$system = System::getInstance();

		$user = new User(self::getRecord());
		
		
		foreach (self::getRecord() as $_record)
		{
			foreach ($_record as $key => $field)
			{
				if ($field == 'useremail') continue;
				$user->$field = (isset($_POST['register'][$field])) ? $_POST['register'][$field] : '';
			}
		} 
		$email = $_POST['register']['useremail'];
		if (Config::EMAIL_ACTIVATION)
		{
			$user->T_profiles_useremail = $email;
		}
		else
		{
			$user->useremail = $email;
		} 
		$user->birthdate =
				mysql_real_escape_string($_POST['register']['bdyear']).'-'.
				mysql_real_escape_string($_POST['register']['bdmonth']).'-'.
				mysql_real_Escape_string($_POST['register']['bdday']);
		$user->public_mail = ($_POST['register']['public_mail'] == 'yes') ? 1 : 0;
		$user->update(false);
		$user->userpass = Login::getPasswordHash($user->userpass);
		$user->regtime = $ts = System::getTimeStamp();
		$user->newsreadtime = $ts;

		$user->rank = $rank;

		$user->update(false);
		return $user;
	}

	/**
	 * User aktiválása
	 * 
	 * @param int $id User id-je
	 * @param string $code code Hash ($_GET['code'])
	 * @return boolean True, ha sikeres volt, false, ha sikertelen
	 */
	public static function activate($id,$code)
	{
		$pref = Config::DBPREF;
		$user = new IsMySQLClass(array(
			$pref.'users as users'=>array("userid",'useremail'),
			$pref.'profiles as profiles'=>array("useremail")
		));
		$user->keyName = "userid";
		$user->init($id); 
		if (empty($user->userid) or
			self::createHash($user->userid, $user->T_profiles_useremail) != $code or
			array_shift(mysql_fetch_row(mysql_query(
					"select count(useremail) from ".$pref."users where useremail = '".$user->T_profiles_useremail."'"))) > 0
			)
		{
			return false;
		} 
		if ($user->T_users_useremail != $user->T_profiles_useremail)
		{
			$user->T_users_useremail = $user->T_profiles_useremail;
			$user->update();
		}
		return true;
	}

	/**
	 * Hibaüzenetek tömbje
	 *
	 * @return array
	 */
	public static function errors()
	{
		return self::$errors;
	}
}

?>
