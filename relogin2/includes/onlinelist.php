<?php
/**
 * R.E. Login 2.0 - Online lista - includes/onlinelist.php
 *
 * Online userek lekérdezése<br />
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
require_once System::getIncLoginDir().'classes/UserFilter.class.php';

$filter = new UserFilter();
$filter->addOnlineFilter(UserFilter::ONLINE_YES);

$users = new UserList(null, $filter);
$profile_tpl_url = ADBListClass::setUrl(array(
	'uid'=>'{id}'
),Config::FILE_PROFILE);
?>
