<?php
/**
 * R.E. Login 2.0 - Admin - Felhasználók - includes/admin/users_admin.php
 *
 * Felhasználók jogainak állítása. Felhasználó törlése.
 * Változók inicializálása. Admin metódus indítása. <br />
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

$msg = "";
if (isset($_POST['admin']))
{
	$values = &$_POST['admin'];
	if(Admin::runUserAdmin($values))
	{
		$msg = ($values['rankid']=='x') 
			? $values['username'] ." törölve!"
			: $values['username'] . " rangja mostantól: ".Ranks::getNameById($values['rankid']);
	}
	else
	{
		$msg = implode('<br />'.PHP_EOL,Admin::errors());
	}
}
?>
