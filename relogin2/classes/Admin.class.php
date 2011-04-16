<?php
/**
 * R.E. Login 2.0 - Admin - class/Admin.class.php
 *
 * Az adminisztrációs folyamatok megvalósítása egy osztályban<br />
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
 * Admin
 *
 * Minden adminisztrációs művelet itt van definiálva.
 * Egy runXXX metódus indítja az XXX-nek megfelelő műveletet.
 * Majd az XXX osztály megvalósítja azt. 
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Admin
{
	/**
	 * Meghívó elvétele
	 */
	const INVITE_SUB = 'invitesub';

	/**
	 * Meghívó adása
	 */
	const INVITE_ADD = 'inviteadd';

	/**
	 * Meghívó beállítása
	 */
	const INVITE_SET = 'inviteset';

	/**
	 * Meghívó admin
	 */
	const METHOD_INVITE = 'method_invite';

	/**
	 * Config admin
	 */
	const METHOD_CONFIG = 'method_config';

	/**
	 * Users admin
	 */
	const METHOD_USERS = 'method_users';

	/**
	 * Ranks admin
	 */
	const METHOD_RANKS = 'method_ranks';

	/**
	 * Hibaüzenetek tömbje
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Feldolgozandó beállítások tömbje
	 *
	 * @var array
	 */
	private static $values = array();

	/**
	 * Űrlap inputok címe. Egyúttal a Config fájlba generálandó
	 * megjegyzések a megfelelő konstansok fölé.
	 *
	 * @var array
	 */
	private static $titles = array();

	/**
	 * Maximálisan beállítható meghívók száma. 
	 *
	 * @var int
	 */
	private static $maxInvitation = 100;

	/**
	 * Config admin elindítása
	 *
	 * @param array $values {@link $values}
	 * @param array $titles {@link $titles}
	 * @return bool Sikeres volt-e a művelet
	 */
	public static function runConfigAdmin(&$values, &$titles)
	{
		return self::run($values, $titles,self::METHOD_CONFIG);
	}

	/**
	 * Users admin indítása
	 *
	 * @param array $values {@link $values}
	 * @return bool Sikeres volt-e a művelet
	 */
	public static function runUserAdmin(&$values)
	{
		return self::run($values, null,self::METHOD_USERS);
	}

	/**
	 * Invite admin indítása
	 *
	 * @param array $values {@link $values}
	 * @return bool Sikeres volt-e a művelet
	 */
	public static function runInviteAdmin(&$values)
	{
		return self::run($values, null,self::METHOD_INVITE);
	}

	/**
	 * Ranks admin indítása
	 *
	 * @param array $values {@link $values}
	 * @return bool Sikeres volt-e a művelet
	 */
	public static function runRanksAdmin(&$values)
	{
		return self::run($values, null,self::METHOD_RANKS);
	}

	/**
	 * Bármilyen admin indítása
	 *
	 * A $mode paraméterben várja melyik admint kell indítani.
	 * Ezt osztályszintű konstansok segítségével lehet megadni. 
	 * Ha a $_POST['admin'] tömb nincs beállítva, akkor nem indul el. 
	 *
	 *
	 * @param array $values {@link $values}
	 * @param array $titles {@link $titles}
	 * @param string $mode Melyik admint indítsa. Lehetséges értékek:
	 *				{@link METHOD_INVITE}, {@link METHOD_RANKS},
	 *				{@link METHOD_USERS}, {@link METHOD_CONFIG}
	 * @return bool Sikeres volt-e a művelet
	 */
    private static function run($values, $titles,$mode)
	{
		if (!isset($_POST['admin']) or !is_array($_POST['admin']))
		{
			return false;
		}
		self::$values = $values;
		self::$titles = $titles;

		switch ($mode) {
			case self::METHOD_INVITE:
				return self::inviteAdmin();

			case self::METHOD_USERS:
				return self::userAdmin();
				break;

			case self::METHOD_RANKS:
				return self::ranksAdmin();

			case self::METHOD_CONFIG:
				return self::writeConfigFile();

			default:
				return self::writeConfigFile();
		}
		
		return true;
	}

	/**
	 * Users admin megvalósítása
	 *
	 * Felhasználók jogainak beállítása, illetve felhasználó törlése.
	 * Törléskor törlődnek a felhasználó üzenetei, illetve a keresési
	 * naplója.
	 *
	 * @return bool Sikeres volt-e a művelet
	 */
	private static function userAdmin()
	{
		$username = trim(self::$values['username']);
		require_once System::getIncLoginDir().'classes/UserList.class.php';
		$ids = array();
		if(!($ids = UserList::exists('username', $username,false)))
		{
			self::$errors[] = 'Nincs "'.$username.'" nevű felhasználó';
			return false;
		}
		if ($ids[0] == System::$user->T_users_userid)
		{
			self::$errors[] = 'Saját magad nem módosíthatod!';
			return false;
		}
		$user = new User((int)$ids[0]);

		$isAdmin = $user->rank(array('admin','owner'));
		if ($isAdmin and !System::$user->rank('owner'))
		{
			self::$errors[] = 'Adminokat csak '.Ranks::getNameByVar('owner').' rangú módosíthat!';
			return false;
		}

		if (self::$values['rankid'] == 'x')
		{
			//user törlése
			$userlist = new UserList();
			require_once System::getIncLoginDir().'classes/Messages.class.php';
			Messages::deleteMsgsOfUser($ids[0]);
			mysql_query('delete from '.Config::DBPREF.'searchlog where userid = '.$ids[0]);
			$userlist->delete('userid', $ids[0]);
		}


		$user->rank = self::$values['rankid'];
		$user->update();
		return true;
	}

	/**
	 * Invite admin megvalósítása.
	 *
	 * Meghívók kiosztása, elvétele. 
	 *
	 * @return bool Sikeres volt-e a művelet
	 */
	private static function inviteAdmin()
	{
		$username = trim(self::$values['username']);
		require_once System::getIncLoginDir().'classes/UserList.class.php';
		$ids = array();
		if (self::$values['allusers'] === null)
		{
			if(!($ids = UserList::exists('username', $username,false)))
			{
				self::$errors[] = 'Nincs "'.$username.'" nevű felhasználó';
				return false;
			}
		}
		$userid = count($ids) ? $ids[0] : 0;
		self::setInvitations($userid, self::$values['invitations'], self::$values['mode']);
		return true;
	}

	private static function ranksAdmin()
	{
		$valid_var = preg_match('/^[a-z_]+[a-z0-9_]*$/i',self::$values['varname']);
		$err_invalid_varname = 'Érvénytelen változónév!
								Érvényes: 0-9, a-z, _, ékezet nélkül  (Számmal nem kezdődhet)';
		if (trim(self::$values['rankid']) == "")
		{
			if (!$valid_var)
			{
				self::$errors[] = $err_invalid_varname;
				return false;
			}
			Ranks::addRank(self::$values['varname'], self::$values['rankname'],true);

		}
		else if (self::$values['op'] == "delete")
		{
			if (self::$values['rankid'] < 5)
			{
				self::$errors[] = 'Alapértelmezett rangok nem törölhetők';
				return false;
			}
			Ranks::getInstance()->delete('rankid',self::$values['rankid']);
			header('Location: '.$_SERVER['REQUEST_URI']);
		}
		else if (self::$values['act'] == 'mod')
		{
			if (!$valid_var)
			{
				self::$errors[] = $err_invalid_varname;
			}
			$rank = Ranks::getRank(null, 'rankid', self::$values['rankid']);
			if ($rank->varname != self::$values['varname'])
			{
				self::$errors[] = 'Alapértelmezett rangok változóneve nem módosítható!';
			}
			
			if (count(self::$errors))
			{
				return false;
			}

			$rank->varname = self::$values['varname'];
			$rank->name = self::$values['rankname'];
			$rank->update();
		}
		return true;
	}

	/**
	 * Meghívók módosítása
	 *
	 * A $mode paraméterben megadott művelet szerint elvesz, hozzáad, vagy
	 * beállít $invitations számű meghívót a $userid id-jű felhasználónak. 
	 *
	 * @param int $userid Felhasználó azonosítója
	 * @param int $invitations Meghívók száma
	 * @param string $mode Meghívó módosítás módja. Értéke lehet:
	 *				{@link INVITE_SET}, {@link INVITE_ADD}, {@link INVITE_SUB}
	 *			Alapértelmezetten beállítás történik. 
	 */
	public static function setInvitations($userid, $invitations,$mode=self::INVITE_SET)
	{
		switch ($mode)
		{
			case self::INVITE_ADD:
				self::addInvitations($userid, $invitations);
				return;
			case self::INVITE_SUB:
				self::subInvitations($userid, $invitations);
				return;
			case self::INVITE_SET:
				self::invitationQuery($invitations,$userid);
				return;
		}
		self::invitationQuery($invitations,$userid);
	}

	/**
	 * Meghívók elvétele
	 *
	 * @see setInvitations
	 *
	 * @param int $userid Felhasználó azonosítója
	 * @param int $invitations Elveendő meghívók száma
	 */
	public static function subInvitations($userid, $invitations)
	{
		$inv = (int)$invitations;
		self::invitationQuery(" if(invitations > $inv, invitations-$inv, 0 ) ", $userid);
	}

	/**
	 * Meghívók adása
	 *
	 * @see setInvitations
	 *
	 * @param int $userid Felhasználó azonosítója
	 * @param int $invitations Elveendő meghívók száma
	 */
	public static function addInvitations($userid, $invitations)
	{
		$inv = (int)$invitations;
		self::invitationQuery(" if(invitations < ".(self::maxInvitation()-$inv).",
								invitations+$inv, ".self::maxInvitation()." ) ",
					$userid);
	}

	/**
	 * Maximálisan kiosztható meghívók száma egy felhasználónak
	 *
	 * A $max paramétert megadva be is állítja új értékre. 
	 * Mindenképpen visszatér az aktuális értékkel a beállítás után. 
	 *
	 * @param int $max Maximális meghívók száma
	 * @return int
	 */
	public static function maxInvitation($max=null)
	{
		if ($max !== null)
		{
			self::$maxInvitation = (int)$max;
		}
		return self::$maxInvitation;
	}

	/**
	 * Meghívók beállításának frissítése.
	 *
	 * Itt fut le a mysql_query() az összeállított sql kóddal.
	 *
	 * @param string $set Meghívók új számának sql kódja.
	 * @param int $userid Felhasználó azonosítója
	 */
	private static function invitationQuery($set,$userid)
	{
		$uid = (int)$userid;
		$sql = 
			"update ".Config::DBPREF."users set invitations = ".
			$set;
		if ($uid)
		{
			$sql .= " where  userid = $uid";
		}
		mysql_query($sql);
	}

	/**
	 * Config fájl módosítása
	 *
	 * @return bool Sikeres volt-e a művelet
	 */
	private static function writeConfigFile()
	{
		if (self::$values['SMTP_AUTH'] and
			(empty(self::$values['SMTP_HOST']) or
			empty(self::$values['SMTP_PORT']) or
			empty(self::$values['SMTP_USERNAME']) or
			empty(self::$values['SMTP_PASSWORD'])) )
		{
			self::$errors[] = 'Bejelölted az SMTP hitelesítést, ezért
								minden SMTP beállítást meg kell adnod';
			return false;
		}

		$t = "<?php".PHP_EOL.
			"final class Config".PHP_EOL."{".PHP_EOL.
			"\t const ";
		foreach (self::$values as $key => $value)
		{
			$value = (!is_bool($value))
					? "'".$value."'" : ($value ? 'true' : 'false');
			$title = "";
			if (isset (self::$titles[$key]))
			{
				$title = self::$titles[$key];
			}
			else if (isset(self::$titles['+'.$key]))
			{
				$title = self::$titles['+'.$key];
			}
			else if (isset(self::$titles['-'.$key]))
			{
				$title = self::$titles['-'.$key];
			}

			$t .= PHP_EOL."\t\t/*".strip_tags($title)."*/";
			$t .= PHP_EOL."\t\t".$key." = ".$value.",";
		}
		$t = rtrim($t,',').';'.PHP_EOL;
		$t .=
		"\tprivate static \$instance;".PHP_EOL.
		"\tprivate function __construct(){}".PHP_EOL.
		"\tfunction __get(\$var)".PHP_EOL.
		"\t{".PHP_EOL.
		"\t\tif (!isset(\$this->\$var))".PHP_EOL.
		"\t\t{".PHP_EOL.
		"\t\t\teval('\$ret = self::'.\$var.';');".PHP_EOL.
		"\t\t\treturn \$ret;".PHP_EOL.
		"\t\t}".PHP_EOL.
		"\t}".PHP_EOL.
		"\tpublic static function getInstance()".PHP_EOL.
		"\t{".PHP_EOL.
		"\t\tif (!isset(self::\$instance)) {".PHP_EOL.
		"\t\t\t\$c=__CLASS__;".PHP_EOL.
		"\t\t\tself::\$instance = new \$c;".PHP_EOL.
		"\t\t}".PHP_EOL.
		"\t\treturn self::\$instance;".PHP_EOL.
		"\t}".PHP_EOL.
		"}".PHP_EOL."?>";


		file_put_contents(System::getIncLoginDir().'classes/Config.class.php',$t);
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
