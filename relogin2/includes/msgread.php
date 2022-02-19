<?php
/**
 * R.E. Login 2.0 - Üzenetek - Olvasás - includes/msgread.php
 *
 * Egy üzenet olvasása. Olvasott üzenet törlése. <br />
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

System::protectedSite();

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/Message.class.php';

/**
 * @ignore
 */
require_once System::getIncLoginDir().'classes/Messages.class.php';

$id = isset($_GET['msgid']) ? (int)$_GET['msgid'] : 0;

$message = new Message($id);

$url_delete = Url::set(array(
	'delete'=>'1'
));

$url_reply = Url::set(array(
	'msgact'=>'reply',
	'reply' => $id
), Config::FILE_MESSAGES_WRITE);

$msgUserId = 0;
$msgLabel = "";
$msgUserName = "";
if (!empty($message->messageid))
{
	if (!$message->toid or
		(System::$user->T__users__userid == $message->toid
		and empty($message->readtime)))
	{
		$message->readtime = System::getTimeStamp();
		$message->update();
	}

	if (!$message->toid)
	{
		$msgLabel = "Írta:";
		$msgUserName = $message->T__from__username;
	}
	else if ($message->toid == System::$user->T__users__userid)
	{
		$msgLabel = "Feladó:";
		$msgUserId = $message->fromid;
		$msgUserName = $message->T__from__username;
	}
	else
	{
		$msgLabel = "Címzett:";
		$msgUserId = $message->toid;
		$msgUserName = $message->T__to__username;
	}

}


if (isset($_GET['delete']) and $_GET['delete'] == 1)
{
	if ($message->isNews())
	{
		Messages::deleteNews($id);
	}
	else
	{
		Messages::deleteMsgs($id);
	}
	if (!empty($message->messageid))
	{
		System::redirect($message->isIncomming()
				? Messages::inboxUrl()
				: ($message->isNews() 
						? Messages::newsUrl()
						: Messages::outboxUrl()));
	}
	else
	{
		System::redirect(Message::msgUrl($id));
	}
}

?>
