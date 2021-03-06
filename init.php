<?php
/*
 * Alap sablon az R.E. Login 2.0 -hoz.
 *
 * Szerző: Takács Ákos (Rimelek)
 * E-mail: programmer@rimelek.hu
 * Weboldal: http://rimelek.hu
 * Login weboldala: http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0
 *
 * Ikonokat a következő weboldalakról töltöttem le:
 * http://www.freeiconsweb.com/
 * http://sixrevisions.com/resources/40-beautiful-free-icon-sets/
 */

if ($_SERVER['HTTP_HOST'] == 'localhost') {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

$errRepSwitch = getenv('RE_ERROR_REPORTING_SWITCH');
if ($errRepSwitch) {
    $errRepVal = filter_input(INPUT_GET, $errRepSwitch);
    if ($errRepVal !== null) {
        error_reporting($errRepVal === '1' ? E_ALL : 0);
    }
}

ob_start();

require_once(dirname(__FILE__).'/relogin2/classes/System.class.php');
$system = System::getInstance();
header('Content-type: text/html; charset='.Config::DBCHARSET);

?>
