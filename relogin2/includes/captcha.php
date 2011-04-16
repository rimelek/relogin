<?php
/**
 * R.E. Login 2.0 - Captcha - includes/captcha.php
 *
 * Captcha kép létrehozása. Ez egy kép típusú fájl. <br />
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
require_once '../../init.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/RECaptcha.class.php';

//kép létrehozás
$captcha = new RECaptcha(array('fonttype'=>System::getIncLoginDir().'classes/fonts/arial.ttf'));
//kép kimenetre küldése
$captcha->flush();
?>
