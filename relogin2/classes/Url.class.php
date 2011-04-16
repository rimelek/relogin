<?php
/**
 * R.E. Login 2.0 - Url-ek kezelése - class/Url.class.php
 *
 * URL-ek kezelése<br />
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
 * URL-ek kezelése
 *
 * Jelenleg csak az url-ek beállítására tartalmaz egy metódust.
 * A jövőben ez bővülhet új verzióban. 
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Url
{
	/**
	 * Url-ben query változók beállítása vagy törlése
	 *
	 * @param array $vars query változók és értékeik asszociatív tömbje.
	 *			Ha egy változó értéke null, akkor azt törli az url-ből. 
	 * @param string $url Jelenlegi url, amihez képest új query változókat kell megadni. 
	 * @param string $sep query változókat elválasztó jel. (&amp;amp; az alapértelmezett.)
	 * @return string
	 */
	public static function set($vars, $url=null,$sep=null)
	{
		if ($sep === null)
		{
			$sep = '&amp;';
		}
		if ($url === null)
		{
			$url = $_SERVER['REQUEST_URI'];
		}
		$parse = parse_url($url);
		$file = $parse['path'];
		$get = array();
		if (isset($parse['query']))
		{
			parse_str($parse['query'],$get);
		}
		foreach ($vars as $key => &$value)
		{
			if (is_null($value))
			{
				unset($get[$key]);
				continue;
			}
			$get[$key] = $value;
		}
		$ret = $file;
		$query = http_build_query($get, '', $sep);
		if ($query)
		{
			$ret  .= '?'.$query;
		}
		return $ret;
	}
}
?>
