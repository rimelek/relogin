<?php
/**
 * R.E. Login 2.0 - Admin - includes/admin.php
 *
 * Adminisztráció változóinak inicializálása.
 * Osztályok behívása<br />
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
require_once System::getIncLoginDir().'classes/Admin.class.php';

System::protectedSite(array('admin','owner'));

$url_settings = Url::set(array(
	'admact' => 'config'
));

$url_ranks = Url::set(array(
	'admact' => 'ranks'
));

$url_invite = Url::set(array(
	'admact' => 'invites'
));

$url_users = Url::set(array(
	'admact' => 'users'
));


$admact = (isset($_GET['admact'])) ? $_GET['admact'] : '';
$file = 'config_admin';
switch ($admact)
{
	case 'invites':
		$file = 'invite_admin';
		break;
	case 'ranks':
		$file = 'ranks_admin';
		break;
	case 'config':
		$file = 'config_admin';
		break;
	case 'users':
		$file = 'users_admin';
		break;
}

?>

