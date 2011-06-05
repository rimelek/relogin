<?php
/**
 * R.E. Login 2.0 - Regisztráció - includes/register.php
 *
 * Regisztrációs, aktivációs folyamatok elindítása.
 * Változók inicializálása.<br />
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

if (System::$logged)
{
	System::redirect(Config::FILE_HOME);
}

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/Register.class.php';

$msg = "";
$userid = false;
$validinvite = false;
$mailact = false;

$data = array(
	'username' => '',
	'userpass' => '',
	'reuserpass' => '',
	'useremail' => '',
	'reuseremail' => '',
	'public_mail' => 'no',
	'sex' => '',
	'bdyear' => '',
	'bdmonth' => '',
	'bdday' => ''	
);


$c = UserList::countUsers();
$REG_BLOCKED = (Config::REG_BLOCKED and $c);
$INVITATION_MODE = (Config::INVITATION_MODE and $c);
//Ha nincs blokkolva a reg, akkor érvényes-e a meghívó
if (!$REG_BLOCKED and isset($_GET['invitehash']) and isset($_GET['inviteid']))
{
	/**
	 * @ignore
	 */
	require_once System::getIncLoginDir().'classes/Invite.class.php';
	$invite = new Invite((int)$_GET['inviteid']);
	$validinvite = (!empty($invite->inviteid) and !$invite->toid and urldecode($_GET['invitehash']) == $invite->code);
	if (!$validinvite)
	{
		$msg = "Érvénytelen meghívó, vagy már beregisztráltak vele!";
	}
	if (!Config::INVITATION_MODE)
	{
		$msg .= "<br />A regisztrációt folytathatod, de a meghívó figyelmen kívül lesz hagyva.";
	}
}
//regisztráció csak akkor engedélyezett, ha elküldték az űrlapot,
//és a reg sincs blokkolva, és meghívó mód esetén érvényes a meghívó
if (isset($_POST['register']) and !$REG_BLOCKED and (!$INVITATION_MODE or $validinvite))
{
	$msg = "A regisztráció sikeres volt.";
	if (Config::EMAIL_ACTIVATION)
	{
		$msg .= " E-mailben kiküldtük az aktiváló linket.";
	}
	if( !($userid = Register::request() ) )
	{
		$msg = "Hiba történt: <br />";
		foreach (Register::errors() as $error)
		{
			$msg .= $error."<br />".PHP_EOL;
		}
	}
	if (!$userid)
	{
		foreach ($_POST['register'] as $key => &$item)
		{
			$data[$key] = htmlspecialchars($item);
		}
	}
}
else if (isset($_GET['id']) and isset($_GET['code']))
{
	$mailact = true;
	$msg = Register::activate($_GET['id'], urldecode($_GET['code']))
			? "Az aktiváció sikeres volt. Most már beléphetsz. "
			: "Az aktiváló kód érvénytelen, vagy már aktiváltad a hozzáférésedet.";
}

if ($validinvite)
{
	if($userid)
	{
		$invite->toid = $userid;
		$invite->update();
	}
}

?>
