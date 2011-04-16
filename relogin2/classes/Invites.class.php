<?php
/**
 * R.E. Login 2.0 - Meghívólista - class/Invites.class.php
 *
 * Meghívók listája<br />
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
 * Meghívók listájának megvalósítása
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Invites extends IsMySQLListClass
{
	/**
	 * Hibaüzenetek tömbje
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Lista példányosítása
	 * {@link Invite} objektumok lesznek  alistában
	 */
    public function  __construct()
	{
		parent::__construct(self::getTables(),'Invite');
	}

	/**
	 * Tábla és mezőlista a lekérdezéshez
	 *
	 * @return array
	 */
	public static function getTables()
	{
		return array(
				Config::DBPREF."invites as invites" => array("email","fromid","code")
				);
	}

	/**
	 * Meghívó kiküldése
	 *
	 * @see System::sendEmail()
	 *
	 * @param string $email E-mail cím, ahova küldeni kell a meghívót. 
	 * @return bool Sikeres volt-e a meghívó küldés
	 */
	public function send($email)
	{
		$email = trim($email);
		require_once System::getIncLoginDir().'classes/UserList.class.php';
		if (UserList::exists('useremail', $email, false))
		{
			self::$errors[] = 'Ő már regisztrált!';
			return false;
		}

		if (self::exists('email', $email))
		{
			self::$errors[] = 'Már meghívtad őt, de még nem regisztrált!';
			return false;
		}

		require_once System::getIncLoginDir().'classes/Invite.class.php';
		
		$invite = new Invite(self::getTables());
		$invite->email = $email;
		$invite->fromid = System::$user->T_users_userid;
		$invite->code = self::createHash();
		$invite->update(false);
		$invite->keyName = "inviteid";
		$inviteid = $this->add($invite);

		$site = System::getSitedirWithHTTP();
		$reglink = System::getSitedirWithHTTP().Config::FILE_REGISTER;
		$get = array();
		parse_str(parse_url($reglink,PHP_URL_QUERY),$get);
		$get['invitehash'] = $invite->code;
		$get['inviteid'] = $inviteid;
		$reglink = $reglink."?".http_build_query($get, '', '&amp;');
		$body =
			"Kedves címzett!<br /><br />".PHP_EOL.
			"Weboldalunk (<a href='$site'>$site</a>) <br />".PHP_EOL.
			"egy felhasználója (".System::$user->username.") meghívót küldött neked.<br />".PHP_EOL.
			"Az alábbi linkre kattintva létrehozhatod saját profilodat.<br />".PHP_EOL.
			"<a href='$reglink'>$reglink</a>";
		if (isset($_POST['invite']['message']))
		{
			$body 
				.= "<br /><br />".PHP_EOL.
				System::$user->username." üzenete: <br />".PHP_EOL.
				nl2br(htmlspecialchars($_POST['invite']['message']));
		}

		System::sendEmail($email, 'Meghívó', $body);
		if (System::$user->invitations)
		{
			System::$user->invitations--;
			System::$user->update();
		}
		return true;
	}

	/**
	 * Random hash generálása
	 *
	 * @return string
	 */
	public static function createHash()
	{
		return md5(mt_rand().microtime(true));
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

	/**
	 * Létezik-e egy meghívó
	 *
	 * Létezik-e egy meghívó, aminek $field tulajdonsága egyenlő $value -val. 
	 *
	 * @param string $field Mezőnév
	 * @param mixed $value Mező értéke
	 * @return bool
	 */
	public static function exists($field,$value)
	{
		$value = mysql_real_escape_string($value);
		return (bool)array_shift(mysql_fetch_row(mysql_query(
				"select count(fromid) from ".
				Config::DBPREF."invites where `$field` = '$value'")));
	}
}
?>
