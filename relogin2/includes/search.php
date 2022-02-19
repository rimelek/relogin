<?php
/**
 * R.E. Login 2.0 - Felhasználó kereső - includes/search.php
 *
 * Felhasználók keresése. Egyelőre csak felhasználónév szerint.<br />
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

if(!class_exists('System'))
{
	exit('Ez a fajl nem erheto el kozvetlenul. Reszletek a readme.txt-ben.');
}

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/UserList.class.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/Ranks.class.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/UserFilter.class.php';

System::protectedSite();

$users = array();
$pageLinks = "";
$usearch_action = Url::set(array(
	'page'=>1
));
if (isset($_POST['usearch'])and trim($_POST['usearch']['username']))
{
	$usearch = &$_POST['usearch'];

	$serialized = mysql_real_escape_string(serialize($usearch));

	mysql_query(
		"insert into ".Config::DBPREF."searchlog
		(userid,logtext,logtime) values
		('".System::$user->T__users__userid."','$serialized','".System::getTimeStamp()."')");
	$site = Url::set(array(
		'searchid'=>mysql_insert_id()
	), null, '&');

	/* Felesleges logok törlése */

	$limit = 100;
	$userid = System::$user->T__users__userid;
	$sql = "select searchid from ".
		Config::DBPREF."searchlog where userid=$userid order by searchid desc limit $limit, 1";
	$row = mysql_fetch_row(mysql_query($sql));
	if ($row)
	{
		$id = $row[0];
		mysql_query("delete from ".Config::DBPREF."searchlog where ".
					"userid = $userid and searchid <= $id");
	}

	/****************************/



	System::redirect($site);
}
else
if (isset($_GET['searchid']))
{
	$sid = (int)$_GET['searchid'];

	$query = mysql_query(
		"select logtext from ".Config::DBPREF."searchlog
		where searchid = $sid and userid = ".System::$user->T__users__userid);

	$row = mysql_fetch_row($query);
	if ($row)
	{
		$filter = new UserFilter();
		$usearch = unserialize($row[0]);

		if (isset($usearch['username']))
		{
			$filter->addLikeFilter('username', $usearch['username']);
		}
		$users = new UserList(null, $filter);
		$pageLinks = $users->pageLinks(10);
	}
}

$profile_tpl_url = ADBListClass::setUrl(array(
	'uid'=>'{id}'
),Config::FILE_PROFILE);

?>
