<?php
/**
 * R.E. Login 2.0 - Kilépés - logout.php
 *
 * Kijelentkezés, ha $_GET['relogin_logout'] létezik, és értéke 1
 * Ha nem teljesül, akkor a főoldalra irányít. <br />
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

System::logout();
if (System::$logged)
{
	System::redirect(Config::FILE_HOME);
}
?>
