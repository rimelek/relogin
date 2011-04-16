<?php
/**
 * R.E. Login 2.0 - Admin - Rangok - includes/admin/ranks_admin.php
 *
 * Meghívók kiosztása, elvétele.
 * Változók inicializálása. Admin metódus indítása.<br />
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

$data = array(
	'act'		=> '',
	'rankid'	=> '',
	'rankname'	=> '',
	'op'		=> 'modify',
	'varname'	=> ''
);


$ranks = Ranks::getInstance();

if (isset($_POST['admin']))
{ 
	foreach($data as $key=>&$post)
	{
		$post = (isset($_POST['admin'][$key])) ? $_POST['admin'][$key] : null;
	}
	if (!Admin::runRanksAdmin($data))
	{ 
		$msg = implode('<br />'.PHP_EOL,Admin::errors());
	} 
	else if ($data['act'] == 'select' and $data['op'] != 'delete')
	{
		$data['rankname'] = Ranks::getNameById($data['rankid']);
		$data['varname'] = Ranks::getRank('varname', 'rankid', $data['rankid']);
	}
	else if ($data['op'] == 'delete')
	{
		$data['rankid'] = '';
	}
}

?>
