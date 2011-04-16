<?php
/**
 * R.E. Login 2.0 - Üzenet - class/Message.class.php
 *
 * Egy üzenetet megvalósító osztály.<br />
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
 * Üzenet osztály
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 *
 * @property int $messageid Üzenet azonosító
 * @property int $fromid Küldő azonosító
 * @property int $toid Címzett azonosító
 * @property string $subject Üzenet tárgya
 * @property string $body Üzenet tartalma
 * @property string $sendtime Üzenet elküldésének sql timestamp-ja
 * @property string $readtime Üzenet elolvasásának sql timestamp-ja
 * @property string $T_from_username Küldő neve
 * @property string $T_to_username Címzett neve
 *
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Message extends IsMySQLClass
{

	/**
	 * Üzenet létrehozása
	 *
	 * @param int $id Üzenet id-je
	 * @param bool $list Listában van-e az üzenet
	 */
	public function  __construct($id,$list=false)
	{
		parent::__construct(self::getTables(),$list);
		if (is_numeric($id))
		{
			$id = (int)$id;
			$this->keyName = 'messageid';
			$this->init(Config::DBPREF."messages as messages left join ".
				Config::DBPREF."users as `from` on
					`from`.userid = messages.fromid left join ".
				Config::DBPREF."users as `to` on
					`to`.userid = messages.toid where ((".
					System::$user->T_users_userid." in (messages.fromid, messages.toid)
					and deleted != ".System::$user->T_users_userid.") or messages.toid = 0)
					and messages.messageid = ".$id,true);
		} 
	}

	/**
	 * Táblalista+mezőlista a lekérdezéshez
	 *
	 * @return array
	 */
	public static function getTables()
	{
		return array(
				Config::DBPREF."messages as messages" =>
					array('messageid','fromid','toid','subject','body','sendtime','readtime'),
				Config::DBPREF."users as from" => array('username'),
				Config::DBPREF."users as to" => array('username')
			);
	}

	/**
	 * Tárgy lekérdezése
	 *
	 * @param bool $short Ha true, akkor rövidített, ... -al kiegészített. 
	 * @return string A tárgy hosszú, vagy rövid alakja
	 */
	public function subject($short=false)
	{
		$subject = trim($this->subject);
		if (!$subject)
		{
			$subject = "Nincs tárgy!";
		}

		if ($short)
		{
			$length = mb_strlen($subject,Config::DBCHARSET);
			$subject = mb_substr($subject, 0, 30, Config::DBCHARSET);
			if (mb_strlen($subject, Config::DBCHARSET) != $length)
			{
				$subject .= "...";
			}
		}

		return $subject;
	}

	/**
	 * Üzenet tartalma formázva
	 *
	 * @return string 
	 */
	public function body()
	{
		return nl2br(htmlspecialchars($this->body));
	}

	/**
	 *
	 * @param int $messageid Üzenet azonosítója Elhagyása esetén az üzenet
	 *		saját url-jét adja vissza. 
	 * @return string Üzenetre mutató url. 
	 */
	public function msgUrl($messageid=null)
	{
		$url = System::getSitedir().Config::FILE_MESSAGES_READ;
		$msgid = !is_null($messageid) ? $messageid : $this->messageid;
		return Url::set(array('msgid' => $msgid ), $url, '&amp;');
	}

	/**
	 * Bejövő üzenet-e az üzenet
	 *
	 * @return bool
	 */
	public function isIncomming()
	{
		return $this->toid == System::$user->T_users_userid;
	}

	/**
	 * Kimenő üzenet-e az üzenet
	 *
	 * @return bool
	 */
	public function isOutgoing()
	{
		return $this->fromid == System::$user->T_users_userid;
	}

	/**
	 * Hírről van-e szó
	 *
	 * @return bool
	 */
	public function isNews()
	{
		return $this->toid == 0;
	}

	/**
	 * Megfelelő üzenet ikon visszaadása
	 *
	 * Olvasott vagy olvasatlan ikon. 
	 *
	 * @return string
	 */
	public function icon()
	{
		$msgicon = empty($this->readtime) ? 'msg.gif' : 'msg_r.gif';
		return System::getLogindir().'images/'.$msgicon;
	}

}
?>
