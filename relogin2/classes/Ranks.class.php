<?php
/**
 * R.E. Login 2.0 - Rangok - class/Ranks.class.php
 *
 * Rangok kezelése.<br />
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
 * Rangok kezelése
 *
 * Új rangot lehet felvenni ezzel az osztállyal. Illetve a meglévő rangok
 * tulajdonságai kérdezhetők le. Valamint a példánya a rangok listáját tartalmazza.
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Ranks
{
	public static $instance = null;

	/**
	 * Visszaadja a Ranks példányát. 
	 *
	 * @return Ranks
	 */
	public static function getInstance()
	{	
		if (self::$instance === null)
		{
			self::$instance = new IsMySQLListClass(self::fields());
			self::$instance->init(Config::DBPREF.'ranks as ranks order by `name`');
		}
		return self::$instance;
	}

	/**
	 * Mely mezők legyenek lekérdezve. (mindegyik)
	 *
	 * @return array
	 */
	public static function fields()
	{
		return array(Config::DBPREF.'ranks as ranks'=>array('*'));
	}

	/**
	 * @ignore
	 */
    private function  __construct()
	{
	}

	/**
	 * Új rang felvétele
	 *
	 * @param string $varname Rang változóneve.
	 * @param string $name Rang neve
	 * @param bool $append Adja-e hozzá rögtön a listához is, vagy csak
	 *			adatbázisba írja
	 */
	public static function addRank($varname, $name,$append=false)
	{
		$rank = new IsMySQLClass(self::fields());
		$rank->keyName = 'rankid';

		$rank->varname = $varname;
		$rank->name = $name;
		$rank->update(false);
		$rank->rankid = self::getInstance()->add($rank,$append);
		$rank->update(false);
	}

	/**
	 * A rang egy adatának lekérdezése
	 *
	 * A $return mező értékét kérdezi le, ha a $by mező értéke $value
	 * Ha egy rang sem felel meg a feltételnek, akkor null-t ad vissza. 
	 *
	 * @param string $return Lekérdezendő mező neve
	 * @param string $by Milyen mezőnév alapján kérdezzen le
	 * @param mixed $value Mi legyen a $by mező értéke.
	 * @return mixed
	 */
	public static function getRank($return, $by, $value)
	{
		$by = strtolower($by);
		$value = strtolower($value);
		foreach (self::getInstance() as $row)
		{
			if (strtolower($row[$by]) == $value)
			{
				return (is_null($return)) ? $row : $row[$return];
			}
		}
		return null;
	}

	/**
	 * Rang nevének lekérdezése id alapján
	 *
	 * $id azonosítójú rang nevének lekérdezése
	 *
	 * @param int $id
	 * @return string Rang neve
	 */
	public static function getNameById($id)
	{
		return self::getRank('name', 'rankid', $id);
	}

	/**
	 * Rang nevének lekérdezése változónév alapján
	 *
	 * $var változónevű rang nevének lekérdezése
	 *
	 * @param string $var
	 * @return string
	 */
	public static function getNameByVar($var)
	{
		return self::getRank('name', 'varname', $var);
	}

	/**
	 * Rang id-je változónév alapján
	 *
	 * $var változónevű rang azonosítóját kérdezi le.
	 *
	 * Ha nem volt ilyen rang, akkor -1 lesz a visszatérési érték. 
	 *
	 * @param string $var Rang változó neve
	 * @return int
	 */
	public static function getIdByVar($var)
	{
		return ($id = self::getRank('rankid', 'varname', $var)) !== null ? $id : -1;
	}
}
?>
