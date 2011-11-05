<?php
/**
 * R.E. Login 2.0 - Profil módosítás - class/ChangeProfile.class.php
 *
 * Profil módosítás megvalósítása<br />
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
 * Profil módosítás
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class ChangeProfile
{
	/**
	 * Módosítandó user példánya. 
	 *
	 * @var User
	 */
	private static $user;

	/**
	 * Hibaüzenetek tömbje
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Űrlapon megjelenítendő inputok értékei
	 *
	 * @var array
	 */
	private static $data = array(
		'lastname'=>'','firstname'=>'','bdyear'=>'','avatar'=>'',
		'bdmonth'=>'','bdday'=>'','sex'=>'','country'=>'',
		'city'=>'','useremail'=>'','public_mail'=>'','website'=>'',
		'msn'=>'','skype'=>'','other'=>''
	);

	/**
	 * Hibaüzenetek tömbje
	 *
	 * @return array
	 */
	public static function errors()
	{
		return self::$errors;
	}

	/**
	 * Űrlapon megjelenítendő inputok értékeinek kezelése
	 *
	 * Paraméter nélkül hívva az összes értéket visszaadja.
	 * Egy paraméterrel az $index-nek megfelelő értéket visszaadja.
	 * Két paraméterrel hívva az $index-nek megfelelő értéket $value-ra állítja.
	 * Utóbbi esetben nem tér vissza semmivel. 
	 *
	 * @param string $index
	 * @param mixed $value
	 * @return mixed
	 */
	public static function data($index=null, $value=null)
	{
		$num = func_num_args();
		if ($num == 0) return self::$data;
		if ($num == 1) return isset(self::$data[$index]) ? self::$data[$index] : null;
		self::$data[$index] = $value;
	}

	/**
	 * Elküldött adatok ellenőrzése. 
	 *
	 * @param array $post Posztolt adatok tömbjének referenciája. 
	 * @return bool Érvényes adatokat adott-e meg a user.
	 */
	private static function isValidInput(&$post)
	{
		require_once System::getIncLoginDir().'libs/PHPMailer/class.phpmailer.php';
		if (!isset($post['userpass']) or Login::getPasswordHash($post['userpass']) != System::$user->userpass)
		{
			self::$errors[] = 'Hibás jelszó!';
		} 
		else if (!empty($post['newuserpass']))
		{
			if ($post['newuserpass'] != $post['renewuserpass'])
			{
				self::$errors[] = 'A két új jelszó nem egyezik!';
			}
			if (strlen($post['userpass']) < ($min = Config::MINLENGTH_PASSWORD))
			{
				self::$errors[] =  "A jelszó minimum $min karakter lehet!";
			}
		}
		if (isset($post['useremail']) and !PHPMailer::ValidateAddress($post['useremail']))
		{
			self::$errors[] = 'Az e-mail cím érvénytelen formátumú!';
		}
		else if (array_shift(mysql_fetch_row(mysql_query("select count(userid) from ".Config::DBPREF.
				"users where useremail = '".mysql_real_escape_string($post['useremail']).
				"' and userid != ".self::$user->T_users_userid))))
		{
			self::$errors[] = 'Már létezik ilyen e-mail cím!';
		}

		$y = date('Y');
		if (isset($post['bdyear']) and max(1900, min((int)$y, (int)$post['bdyear'])) != $post['bdyear'])
		{
			self::$errors[] = 'Az évszám 1900 és '.$y.' között lehet! - ';
		}
		if (isset($post['bdmonth']) and max(1, min((int)$post['bdmonth'], 12)) != $post['bdmonth'])
		{
			self::$errors[] = 'A hónap 1 és 12 között lehet!';
		}
		if (isset($post['bdday']) and max(1, min((int)$post['bdday'], 31)) != $post['bdday'])
		{
			self::$errors[] = 'A nap 1 és 31 között lehet!';
		}


		return count(self::errors()) == 0;
	}

	/**
	 *
	 * @param array $post Elküldött adatok tömbjének referenciája. 
	 * @param User $user Módosítandó felhasználó példánya.
	 * @return bool Sikeres volt-e a módosítás. 
	 */
    public static function request(&$post,User $user)
	{
		self::$user = $user;
		if (!self::isValidInput($post))
		{
			return false;
		}
		
		$email = strtolower(trim($post['useremail']));
		if ($email != strtolower(self::$user->T_users_useremail))
		{
			if (Config::EMAIL_ACTIVATION)
			{
				self::$user->T_profiles_useremail = $email;
				require_once System::getIncLoginDir().'classes/Register.class.php';
				$link  = Register::createLink(self::$user->T_users_userid, $email,Config::FILE_REGISTER);
				$body =
					"Az e-mail címed sikeresen megváltoztattad.".PHP_EOL.
					"Kattints az alábbi linkre az aktiválásához: <br />".PHP_EOL.
					"<a href='".$link."'>".$link."</a>";
				System::sendEmail($email, "E-mail cím aktiválás", $body);
			}
			else
			{
				self::$user->useremail = $email;
			}
		}

		if ($post['newuserpass'])
		{
			$userpass = Login::getPasswordHash($post['newuserpass']);
			self::$user->userpass = $userpass;
                        if (System::$user == self::$user) {
                            Login::changePassword($userpass);
                        }

		}


		self::$user->birthdate = $post['bdyear']."-".$post['bdmonth']."-".$post['bdday'];
		self::$user->public_mail = $post['public_mail'] == 'yes' ? true : false;
		
		self::$user->sex = $post['sex'] == 'f' ? 'f' : 'm';
		self::$user->lastname = $post['lastname'];
		self::$user->firstname = $post['firstname'];
		self::$user->country = $post['country'];
		self::$user->city = $post['city'];
		self::$user->website = $post['website'];
		self::$user->msn = $post['msn'];
		self::$user->skype = $post['skype'];
		self::$user->other = $post['other'];
		self::$user->avatar = $post['avatar'];

	
		if (count(self::errors()))
		{
			return false;
		}

		self::$user->update();
		return true;
	}
}
?>
