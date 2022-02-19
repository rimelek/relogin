<?php
/**
 * R.E. Login 2.0 - Profil változtatás - includes/changeprofile.php
 *
 * Profil változtatás. Változók inicializálása.
 * Folyamatok elindítása. <br />
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
require_once System::getIncLoginDir().'classes/ChangeProfile.class.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/UserList.class.php';

System::protectedSite();

$msg = "";

if ( System::$user->rank(array('admin','owner'))
		and isset($_GET['uid'])
		and UserList::exists('userid', $_GET['uid']))
{
	$user = new User((int)$_GET['uid']);
}
if (empty($user->username))
{
	$user = System::$user;
}
$profile = $user;
if (isset($_POST['profile']) and is_array($_POST['profile']))
{
	$profile = $_POST['profile'];

	if (!isset($profile['public_mail']) or $profile['public_mail'] == 'no')
	{
		$profile['public_mail'] = '0';
	}
	else
	{
		$profile['public_mail'] = '1';
	}
	if (ChangeProfile::request($_POST['profile'],$user))
	{
		$msg = "Profil sikeresen módosítva!";
		if ($user->T__users__useremail != $user->T__profiles__useremail)
		{
			$msg .= "Most újra aktiválnod kell az e-mail címed.<br />".PHP_EOL.
					"Az aktiváló linket elküldtük az új e-mail címedre.";
		}
	}
	else
	{
		$msg = implode('<br />'.PHP_EOL,ChangeProfile::errors());
	}
}
foreach ($profile as $key => $value)
{
	//if ($key == 'useremail') continue;
	$data[$key] = htmlspecialchars($value);
}
if (!isset($_POST['profile']))
{
	list($data['bdyear'], $data['bdmonth'], $data['bdday']) = explode('-',$data['birthdate']);
}
?>
