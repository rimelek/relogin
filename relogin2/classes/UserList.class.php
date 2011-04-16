<?php
/**
 * R.E. Login 2.0 - Felhasználó lista - class/UserList.class.php
 *
 * Felhasználólista osztálya<br />
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
 * @ignore
 */
require_once System::getIncLoginDir().'classes/UserFilter.class.php';

/**
 * Felhasználó lista
 *
 * {@link UserFilter} -el szűrt felhasználó listát hoz létre.
 * A példány maga az iterálható lista, ami {@link User} objektumokat tartalmaz.
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class UserList extends IsMySQLListClass
{
	/**
	 *
	 * @param array $fields Lekérdezendő mezők listája.
	 *		array(tábla1=>array(érték1,érték2,...),...)
	 *		formátumban
	 * @param UserFilter $filter
	 */
    public function  __construct($fields=null,UserFilter $filter=null)
	{
		parent::__construct(self::getTables($fields), 'User');  
		parent::page(self::getSql($filter),10);
	}


	/**
	 * Lekérdezéshez az SQL kód összeállítása
	 *
	 * @param UserFilter $filter
	 * @return string From utáni SQL kód
	 */
	public static function getSql(UserFilter $filter=null)
	{
		$sql =
			Config::DBPREF."users as users left join ".
			Config::DBPREF."profiles as profiles using(userid)";

		if (is_null($filter)) return $sql;
		$sql .= $filter->filterString();
		return $sql;
	}

	/**
	 *
	 * @param array $fields lekérdezendő mezők és táblák listája.
	 *			Ha null, akkor minden mező. 
	 * @return $fields lekérdezendő mezők és táblák listája
	 */
	public static function getTables($fields=null)
	{
		if(!isset($fields['users']) or !is_array($fields['users']) or count($fields['users'])==0)
		{
			$fields['users'] = array('*');
		}
		if(!isset($fields['profiles']) or !is_array($fields['profiles']) or count($fields['profiles'])==0)
		{
			$fields['profiles'] = array('*');
		} 
		$tables = array(
			Config::DBPREF.'users as users'=>$fields['users'],
			Config::DBPREF.'profiles as profiles'=>$fields['profiles']
		);
		
		return $tables;
	}

	/**
	 * Mező érték keresése a users, vagy profiles táblában
	 *
	 * Megszámolja hány olyan rekord van, ahol a $field mező értéke $value
	 *
	 * @param string $field Mező neve
	 * @param mixed $value Mező értéke
	 * @param bool $inprofiles Ha True, akkor a profiles táblában keres,
	 *				ha false, akkor a users táblában
	 * @return int
	 */
	public static function exists($field,$value,$inprofiles=true)
	{
		$table = Config::DBPREF.(($inprofiles) ? 'profiles' : 'users');

		$value = mysql_real_escape_string($value);
		$query = mysql_query("select userid from `$table` where `$field` = '$value'");

		$ret=array();
		while ($row = mysql_fetch_row($query))
		{
			$ret[] = $row[0];
		} 
		return count($ret) ? $ret : false;
	}

	/**
	 * Felhasználók száma összesen
	 *
	 * @return int
	 */
	public static function countUsers()
	{
		return (int)array_shift(mysql_fetch_row(
				mysql_query('select count(*) from '.Config::DBPREF.'users')));
	}
}
?>
