<?php
/**
 * R.E. Login 2.0 - Telepítő - Install.class.php
 *
 * Telepítéshez szükséges metódusokat megvalósító osztály.<br />
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
 * Telepítéshez szükséges metódusokat megvalósító osztály.
 * 
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Install
{
	/**
	 * Hibaüzenetek tömbje
	 * 
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Feldolgozandó űrlapadatok tömbje
	 *
	 * @var array
	 */
	private static $values = array();

	/**
	 * Feldolgozandó adatok címei.
	 *
	 * Ez jelenik meg az input mezők mellett címként. 
	 *
	 * @var array
	 */
	private static $titles = array();

	/**
	 * Telepítő elindítása
	 *
	 * Át kell adni a {@link $values} és {@link $titles} változókat.
	 * Ha nincs beállítva $_POST['install'] nevű tömb, akkor false-al
	 * tér vissza. Egyébként ha nincs hiba a telepítéskor, true-val.
	 * Telepítés előtt letelepíti a már létrehozott táblákat az adatbázisból. 
	 *
	 * @param array $values
	 * @param array $titles
	 * @return bool Siker esetén true, egyébként false
	 */
    public static function run($values, $titles)
	{
		if (!isset($_POST['install']) or !is_array($_POST['install']))
		{
			return false;
		}
		self::$values = &$values;
		self::$titles = &$titles;
		if (($err = self::connect()) !== true)
		{
			self::$errors[] = $err;
			return false;
		}
		self::uninstall($values['DBPREF']);
		self::installSql();
		if (count(self::$errors))
		{
			self::uninstall($values['DBPREF']);
			return false;
		}
		if (! self::installConfigFile() )
		{
			return false;
		}

		return true;
	}

	/**
	 * Kapcsolódás az adatbázis szerverhez.
	 *
	 * @return bool Sikeres volt-e a kapcsolódás
	 */
	private static function connect()
	{
		$v = self::$values;
		$connect = @mysql_connect($v['DBHOST'], $v['DBUSER'], $v['DBPASS']);
		if (!$connect)
		{
			return ('Nem sikerült a szerverhez kapcsolódni');
		}
		mysql_query("set names '".$v['DBCHARSET']."' collate '".$v['DBCOLLATE']."'");
		if (!@mysql_select_db($v['DBNAME']))
		{
			return ('Nem sikerült az adatbázist kiválasztani');
		}
		return true;
	}

	/**
	 * Adatbázis táblák feltelepítése
	 */
	private static function installSql()
	{
		$v = self::$values;
		$sql_file = file_get_contents('install.sql');
		$sql_queries = explode('-- table', $sql_file);
		array_shift($sql_queries);
		foreach ($sql_queries as &$query)
		{
			$query = trim($query, "\n\r; ");
			$query = str_replace(
					array('{prefix}','{charset}','{collate}'),
					array($v['DBPREF'],$v['DBCHARSET'],$v['DBCOLLATE']),
					$query);
			mysql_query($query);
			if ($error = mysql_error())
			{
				self::$errors[] = $error.PHP_EOL.'SQL: <pre>'.$query.'</pre>';
			}
		}
	}

	/**
	 * Config osztály létrehozása. 
	 *
	 * @return bool Sikeres volt-e a fájl létrehozás. 
	 */
	private static function installConfigFile()
	{
		if (self::$values['SMTP_ON'])
		{
			if (self::$values['SMTP_AUTH'] and
				(empty(self::$values['SMTP_USERNAME'])
				or empty(self::$values['SMTP_PASSWORD'])))
			{
				self::$errors[] = 'Bejelölted az SMTP hitelesítést, ezért meg kell adnod
					az SMTP felhasználó nevet és jelszót!';
			}
			if (empty(self::$values['SMTP_PORT']) or
				empty(self::$values['SMTP_HOST']))
			{
				self::$errors[] = 'Bejelölted az SMTP használatot, ezért
								meg kell adnod a hostot és portot!';
			}
		}

		if (empty(self::$values['MAIL_TO']) or
			empty(self::$values['MAIL_FROM']))
		{
			self::$errors[] = 'Az E-mail címeket mindenképp meg kell adnod.';
		}

		if (count(self::$errors))
		{
			return false;
		}
		$t = "<?php".PHP_EOL.
			"final class Config".PHP_EOL."{".PHP_EOL.
			"\t const ";
		foreach (self::$titles as $key => $title)
		{
			$value = (!is_bool(self::$values[$key]))
					? "'".self::$values[$key]."'" : (self::$values[$key] ? 'true' : 'false');
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


		file_put_contents('../classes/Config.class.php',$t);
		return true;
	}

	/**
	 * Adatbázis táblák letelepítése
	 *
	 * @param string $prefix Tábla prefix
	 */
	public static function uninstall($prefix)
	{
		$tables = array(
			$prefix.'profiles',
			$prefix.'users',
			$prefix.'invites',
			$prefix.'messages',
			$prefix.'forgotpass',
			$prefix.'ranks',
			$prefix.'sessions',
			$prefix.'searchlog'
		);
		@mysql_query("drop table if exists `".implode('`, `',$tables)."`");
	}

	/**
	 * Hibák tömbje
	 *
	 * @return array
	 */
	public static function errors()
	{
		return self::$errors;
	}
}
?>
