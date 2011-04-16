<?php
/**
 * R.E. Login 2.0 - Elfelejtett jelszó - class/ForgotPass.class.php
 *
 * Elfelejtett jelszó funkció.<br />
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
require_once System::getIncLoginDir().'classes/UserList.class.php';

/**
 * Elfelejtett jelszó funkció
 *
 * Új jelszó választásához kiküldi e-mailben a linket. <br />
 * <br />
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class ForgotPass
{
	/**
	 * Hibaüzenetek tömbje
	 *
	 * @var array
	 */
	private static $errors=array();

	/**
	 * Aktuális idő
	 *
	 * @see System::getTimeStamp()
	 *
	 * @var string
	 */
	private static $now = '';

	/**
	 * Elfelejtett jelszó rekord példányok
	 *
	 * @var IsMySQLClass[]
	 */
	private static $instances = array();

	/**
	 * Privát konstruktor
	 * @ignore
	 */
	private function  __construct()
	{
	}

	/**
	 * Használt távlák és mezőinek listája
	 *
	 * @return array
	 */
	public static function getTables()
	{
		return array(
			Config::DBPREF.'forgotpass as fp' => array('*')
		);
	}

	/**
	 * Lekérdezi az adatbázisból  aparamétereknek megfelelően
	 * az elfelejtett jelszó rekordját. 
	 *
	 * @param int $id Elfelejtett jelszó azonosítója
	 * @param string $code Elfelejtett jelszó generált kódja. 
	 * @return IsMySQLClass
	 */
	public static function getInstance($id, $code)
	{
		$id = (int)$id;
		$code = mysql_real_escape_string($code);
		if (!isset(self::$instances[$id][$code]))
		{
			self::$instances[$id][$code] = new IsMySQLClass(self::getTables());
		
			self::$instances[$id][$code]->init(
					Config::DBPREF."forgotpass as fp where userid = '$id' and
					code = '$code'",true);
		}
		return self::$instances[$id][$code];
	}

	/**
	 * Egy user id-jét, mail címét és jelszavát kérdezi le, ha a $name
	 * mező értéke megegyezik a $value -val. 
	 *
	 * @param string $name Mező neve
	 * @param mixed $value Mező értéke
	 * @return User
	 */
	public static function user($name,$value)
	{
		$user = new User(array(
			Config::DBPREF.'users as users' => array('userid','useremail','userpass'))
		);
		$value = mysql_real_escape_string($value);
		$user->init(Config::DBPREF."users as users where `$name` = '$value'",true);
		return $user;
	}

	/**
	 * Új jelszó igénylése
	 *
	 * $email e-mail címre küldi ki az instrukciókat. 
	 *
	 * @param string $email E-mail cím
	 * @return bool Sikeres volt-e a művelet
	 */
	public static function request($email)
	{
		$fpe = false;
		self::$now = System::getTimeStamp();
		$email = trim($email);
		if( ($ue = UserList::exists('useremail', $email)) and
			!($fpe = self::existsEmail($email)))
		{
			$code = self::randomCode();
			$user = self::user('useremail',$email);

			mysql_query("replace ".Config::DBPREF."forgotpass set
					userid = '".$user->userid."',
					code = '$code',
					sendtime = '".self::$now."'");
			self::send($user->userid,$email,$code);
			return true;
		}
		if ($ue)
		{
			self::$errors[] = "24 órán belül csak egyszer kérhetsz új jelszót!";
		}
		else if (!$fpe)
		{
			self::$errors[] = "Nincs ilyen e-mail cím!";
		}
		return count(self::$errors) == 0;
	}

	/**
	 * E-mail címek számolása
	 *
	 * 24 órán belül csak egyszer lehet jelszó emlékeztetőt kérni.
	 * Ez a metódus megszámolja hány kérelem történt 24 órán belül az $email
	 * e-mail címre.
	 *
	 * @param string $email Elenőrízendő email cím
	 * @return int Hány darab email cím volt (Értelem szerűen max 1 lehetett)
	 */
	public static function existsEmail($email)
	{
		return (int)array_shift(mysql_fetch_row(mysql_query(
					"select count(fp.userid) from ".
						Config::DBPREF."forgotpass as fp left join ".
						Config::DBPREF."users as users
						on fp.userid = users.userid
					 where users.useremail = '".mysql_real_escape_string($email)."' and
						 timestampdiff(DAY,fp.sendtime, '".self::$now."') = 0 limit 1")));
	}

	/**
	 * Véletlenszerá hash
	 *
	 * @return string
	 */
	public static function randomCode()
	{
		return md5(microtime(true).mt_rand());
	}

	/**
	 * Elfelejtett jelszó link létrehozása
	 * 
	 * @param int $id User id-je
	 * @param string $email User email címe
	 * @param string $file Fájlnév, ahova az aktivációs link mutat
	 * @return string Elfelejtett jelszó link
	 */
	private static function createLink($id,$code,$file=null)
	{
		if ($file === null)
		{
			$file = basename(Config::FILE_FORGOTPASS);
		}
		$url = parse_url($file);
		$file = $url['path'];
		if (isset($url['query']))
		{
			parse_str($url['query'],$get);
		}

		$get['id'] = $id;
		$get['fphash'] = $code;
		return
			System::getSitedirWithHTTP().$file.
			'?'.http_build_query($get, '', '&amp;');
	}

	/**
	 * Link kiküldése a megadott e-mail címre.
	 *
	 * @param int $id Felhasználó id-je
	 * @param string $email Felhasználó e-mail címe
	 * @param string $code Random generált kód
	 */
	private static function send($id, $email, $code)
	{
		$link = self::createLink($id, $code);
		$body =
			"Az új jelszavad a következő linkre kattintva állíthatod be: <br />".PHP_EOL.
			"<a href='$link'>$link</a>";
		System::sendEmail($email, 'Elfelejtett jelszó', $body);
	}


	/**
	 * Megváltoztatja a felhasználó jelszavát
	 *
	 * Ha érvényes az id, és hash. valamint megegyezik a két megadott jelszó. 
	 *
	 * @param int $id Felhasználó id-je
	 * @param string $code Elfelejtett jelszó hash
	 * @param string $pass Új jelszó
	 * @param string $repass Új jelszó újra
	 * @return bool Sikeres volt-e az új jelszó beállítása
	 */
	public static function newPassword($id, $code, $pass,$repass)
	{
		$fp = self::getInstance($id, $code);
		if (empty($fp->userid))
		{
			self::$errors[] = 'Hibás jelszóváltoztatás kérelem! Talán már meg lett változtatva a jelszó.';
			return false;
		}

		//jelszó vizsgálata
		if (strlen($pass) < ($min = Config::MINLENGTH_PASSWORD)) {
			self::$errors[] =  "A jelszó minimum $min karakter lehet!";
		} else if ($pass != $repass){
			self::$errors[] = "A két jelszó nem egyezik!";
		}

		if (count(self::$errors)) return false;
	
		$user = self::user('userid',$fp->userid);
		$user->userpass = Login::getPasswordHash($pass);
		$user->update();

		mysql_query("delete from ".Config::DBPREF."forgotpass where userid = '".$fp->userid."'");

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
