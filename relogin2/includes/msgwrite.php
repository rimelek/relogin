<?php
/**
 * R.E. Login 2.0 - Üzenetek - Írás - includes/msgwrite.php
 *
 * Új üzenet vagy hír írása. Hírt csak admin írhat. <br />
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
require_once System::getIncLoginDir().'classes/Messages.class.php';
System::protectedSite();

$msg = "";

$data = array(
	'toname'	=> '',
	'subject'	=> '',
	'body'		=> '',
	'news'		=> false
);

$msgact = isset($_GET['msgact']) ? $_GET['msgact'] : '';

switch ($msgact)
{
	case 'reply':
		$mid = isset($_GET['reply']) ? (int)$_GET['reply'] : 0;
		$message = new Message($mid);
		if (empty($message->messageid)) break;
		
		$data['toname'] = $message->T_from_username;
		$data['subject'] = $message->subject;
		if (mb_strtolower(substr(trim($data['subject']), 0, 3),Config::DBCHARSET) != 're:')
		{
			$data['subject'] = 'Re: '.$data['subject'];
		}
		$data['body'] = $message->body;
		
		break;
	case 'write':
		if (!isset($_GET['msgto'])) break;
		$data['toname'] = ($result = mysql_fetch_row(mysql_query(
				'select username from '.Config::DBPREF.'users where userid='. ((int)$_GET['msgto']) )))
				? $result[0] : '';

		break;
}

if (isset($_POST['message']))
{
	$tmp = &$data;
	$data = &$_POST['message'];
	$data['news'] = (System::$user->rank(array('admin','owner')) and isset($_POST['message']['news']));
	if(Messages::sendRequest($data['toname'], $data['subject'], $data['body'],$data['news']))
	{
		$msg = "Üzenet elküldve";
		$data = $tmp;
	}
	else
	{
		$msg = implode('<br />'.PHP_EOL, Messages::errors());
	}
}

?>
