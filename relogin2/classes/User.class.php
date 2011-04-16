<?php
/**
 * R.E. Login 2.0 - Felhasználó - class/User.class.php
 *
 * Felhasználót megvalósító osztály<br />
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
require_once System::getIncLoginDir().'classes/System.class.php';


/**
 * Felhasználót reprezentáló osztály
 *
 * <b>Dátum:</b> 2010.04.02.
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @property int $T_users_userid Felhasználó azonosítója
 * @property string $username Felhasználó neve
 * @property string $userpass Felhasználó jelszavának hash-e
 * @property int $rank Rang azonosítója
 * @property string $regtime Lásd {@link System::getTimeStamp()}
 * @property string $refreshtime Lásd {@link System::getTimeStamp()}
 * @property string $logintime Lásd {@link System::getTimeStamp()}
 * @property int $onlinetime Online idő másodpercekben
 * @property char $onlinestatus '1', Ha kilépett a kilépés funkcióval a user.
 *					És '0', ha nem lépett ki.
 * @property int $invitations Meghívóinak száma
 * @property string $T_users_useremail Felhasználó aktivált e-mail címe.
 * @property string $newsreadtime Mikor olvasott utoljára hírt.
 *					Lásd {@link System::getTimeStamp()}
 * @property int $T_profiles_userid Felhasználó azonosítója a profiles táblában
 * @property string $firstname Keresztnév
 * @property string $lastname Vezetéknév
 * @property string $birthdate Születési idő. Y-m-d formátumban
 * @property char $sex 'm', ha nő. 'f', ha férfi. NULL? ha nincs beállítva.
 * @property string $country Ország
 * @property string $city Város
 * @property string $T_profiles_useremail Felhasználó e-mail címe a profiljában.
 *					Még nem biztos, hogy meg van erősítve.
 * @property char $public_mail '1', ha publikus a megadottemail címe. Egyébként '0'
 * @property string $website Weboldal címe
 * @property string $msn MSN cím
 * @property string $skype Skype név
 * @property string $other Egyéb információ a felhasználóról. (Bemutatkozás)
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 * @version 2.0
 */
class User extends IsMySQLClass
{
	/**
	 * Létező vagy új felhasználó létrehozása
	 *
	 * @param mixes $userName User neve, ha a második paraméter is meg van adva. (Létező felhasználó)
	 *				ha nincs meg adva második paraméter, akkor a táblalista a mezőlistákkal (Új felhasználó)
	 * @param string $userPass User jelszava
	 */
	public function __construct($userName,$userPass=null)
	{
		$pref = Config::DBPREF;
		$config = System::$config;
		$properties = array(
			$pref.'users as users'=>array('*'),
			$pref.'profiles as profiles'=>array('*')
		);

		if (is_array($userName))
		{
			parent::__construct($userName);
			return;
		}
		else if (!is_string($userPass))
		{
			parent::__construct($properties);
			$this->keyName = 'userid';
			parent::init($userName);
			return;
		}

		if ($userPass === null)
		{
			return;
		}

		parent::__construct($properties);

		$userName = mysql_real_escape_string($userName);
		$sql = 
			$pref."users as users left join ".
			$pref."profiles as profiles using(userid) where username = '".
			mysql_real_escape_string($userName).
			"' and userpass = '".mysql_real_escape_string($userPass)."'";

		$this->init($sql);
	}

	/**
	 * User inicializálása
	 *
	 * @param string $sql Sql lekérdezés from utáni része
	 */
	public function init($sql)
	{
		parent::init($sql,true);
	}

	/**
	 * Kor meghatározása
	 *
	 * 
	 * @param string $bdtimestamp Szletési id Y-m-d formátumban.
	 * @return int A felhasználó kora
	 */
	public static function getAge($bdtimestamp)
	{
		$szarray = explode('-',$bdtimestamp);
		$time = System::getTime();
		$szmonth = $szarray[1];
		$mmonth = date('m',$time);
		$szday = $szarray[2];
		$mday = date('d',$time);
		$age = date('Y',$time) - $szarray[0];
		if( ($szmonth > $mmonth )
		or ($szmonth == $mmonth and $szday > $mday) ) {
			$age--;
		}
		return $age;
	}

	/**
	 *
	 * @param int $sec Online idő másodpercben
	 * @param string $str eredmény sablonja. Amiben a következő helyettesítők
	 *			használhatók:
	 *			<ul>
	 *				<li>{day}: Nap</li>
	 *				<li>{hour}: Óra</li>
	 *				<li>{min}: Perc</li>
	 *				<li>{sec}: másodperc</li>
	 *			</ul>
	 *			<code>print User::getOnlineTime($sec, '{day} nap, {hour} óra');</code>
	 * @return string
	 */
	public static function getOnlineTime($sec,$str=null)
	{
		$ret = array();
		$ret['sec'] = $sec % 60;
		$tmpmin = floor($sec / 60);	
		$ret['min'] = $tmpmin %  60;
		$tmphour = floor($tmpmin / 60);	
		$ret['hour'] = $tmphour % 24;
		$ret['day'] = floor($tmphour / 24);
		if (!$str)
		{
			return $ret;
		}

		return str_replace(array(
			'{day}','{hour}','{min}','{sec}'
		),array(
			$ret['day'], $ret['hour'], $ret['min'], $ret['sec']
		), $str);
	}

	/**
	 * Online van-e a user
	 *
	 * @return bool
	 */
	public function isOnline()
	{ 
		return ($this->onlinestatus and	$this->refreshtime and
			strtotime($this->refreshtime) >= System::getTime() - Config::MAX_ONLINE_TIME );
	}

	/**
	 *
	 * @return User rangjának neve
	 */
	public function rankName()
	{
		require_once System::getIncLoginDir().'classes/Ranks.class.php';
		return Ranks::getRank('name', 'rankid', $this->rank());
	}

	/**
	 * Rang lekérdezése, vizsgálata
	 *
	 * 
	 *
	 * @param mixed $rank Ha nincs megadva, akkor visszaadja a user rangjának
	 *			azonosítóját. Ha nincs neki megfelelő a ranks táblában, akkor
	 *			választ egyet az alapján, hogy az éppen böngésző felhasználóról van
	 *			szó, vagy valakiről a felhasználó listában.
	 *			Ha meg van adva, akkor vagy egy rang változó, vagy azok tömbje.
	 *			Bármelyik illik a userre, true-t ad vissza. Egyébként false-t.
	 *			<code>
	 *			if (System::$user->rank( array('admin','owner') ))
	 *			{
	 *				print "Te admin, vagy tulajdonos ranggal rendelkezel. "
	 *			}
	 *			</code>
	 * @return mixed Vizsgálat esetén bool, egyébként nincs visszatérési érték. 
	 */
	public function rank($rank=null)
	{
		require_once System::getIncLoginDir().'classes/Ranks.class.php';
		$var = Ranks::getRank('varname', 'rankid', $this->rank);
		if (!$var and $this == System::$user and !System::$logged)
		{
			$var = 'guest';
		}
		else if (!$var)
		{
			$var = 'user';
		}
		if(is_null($rank))
		{
			return Ranks::getIdByVar($var);
		}
		else if (is_string($rank))
		{
			return $var == $rank;
		}
		else if (is_array($rank))
		{
			foreach ($rank as &$item)
			{
				if (strtolower($var) == strtolower($item))
				{
					return true;
				}
			}
			return false;
		}
	}

	/**
	 * User profiljának url-je
	 * 
	 * @param int $userid Felhasználó azonosítója
	 * @return string
	 */
	public static function profileUrl($userid)
	{
		$url = System::getSitedir().Config::FILE_PROFILE;
		return Url::set(array(
			'uid' => $userid
		), $url, '&amp;');
	}

	/**
	 * Gravatar url-je. 
	 *
	 * @param int $size Avatar mérete
	 * @return string
	 */
	public function gravatar($size)
	{
		$email = empty($this->T_users_useremail) ? '' : $this->T_users_useremail;
		return "http://www.gravatar.com/avatar/"
			. md5( strtolower( $email ) )
			."?d="
			.urlencode( self::defaultAvatar($this->sex) )
			."&amp;s=" . $size
			."&amp;r=g";

	}

	/**
	 * Alapértelmezett avatar url-je
	 *
	 * @param char $sex 'f', ha nő, 'm', ha férfi
	 * @return string
	 */
	public static function defaultAvatar($sex)
	{
		$default = System::getLoginDirWithHTTP().'images/';
		$default .= (empty($sex) or $sex == 'm')
				? 'male.png' : 'female.png';
		return $default;
	}

	/**
	 * MKAvatar url-je
	 *
	 * @param int $size Egész szám. maximum 80-ig
	 * @return string
	 */
	public function mkavatar($size)
	{
		$email = empty($this->T_users_useremail) ? '' : $this->T_users_useremail;
		return "http://www.mkavatar.hu/avatar.php?email="
			. md5( strtolower( $email ) )
			."&amp;default="
			.urlencode( self::defaultAvatar($this->sex) )
			."&amp;size=" . $size
			."&amp;rating=g";
	}

	/**
	 * User által kiválasztott avatar megjelenítése
	 *
	 * @param int $size Avatar mérete
	 * @return string
	 */
	public function avatar($size)
	{
		if ($this->avatar == 'gravatar')
		{
			return $this->gravatar($size);
		}
		else if ($this->avatar == 'mkavatar')
		{
			return $this->mkavatar($size);
		}
		else
		{
			return $this->defaultAvatar($this->sex);
		}
	}
}
?>
