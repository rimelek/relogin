<?php
/**
 * R.E. Login 2.0 - Üzenetlista - class/Messages.class.php
 *
 * Különböző üzenetlisták lekérdezése<br />
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
require_once System::getIncLoginDir().'classes/Message.class.php';

/**
 * Üzenetlisták
 *
 * Kimenő / Bejövő / Hírek
 *
 * Egy üzenetet megvalósító osztály.<br />
 * <br />
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Messages extends IsMySQLListClass
{
	/**
	 * Bejövő üzenetek
	 */
	const INBOX = 'inbox';

	/**
	 * Kimenő üzenetek
	 */
	const OUTBOX = 'outbox';

	/**
	 * Hírek
	 */
	const NEWS = 'news';

	private static $errors = array();

	/**
	 *
	 * @param string $func Választott lista. Lehetséges értékei:
	 *				{@link INBOX}, {@ link OUTBOX}, {@link NEWS}
	 */
	public function  __construct($func)
	{
		parent::__construct(Message::getTables(),'Message');


		if ($func == self::NEWS)
		{
			$this->page(
				Config::DBPREF.'messages as messages left join '.
				Config::DBPREF.'users as `from` on
					`from`.userid = messages.fromid left join '.
				Config::DBPREF.'users as `to` on
					`to`.userid = messages.toid where messages.toid = 0 '.
					' order by sendtime desc',10);
			return;
		}

		$msg_uid = 'fromid';

		if ($func == self::INBOX)
		{
			$msg_uid = 'toid';
		}

		$this->page(
				Config::DBPREF.'messages as messages left join '.
				Config::DBPREF.'users as `from` on
					`from`.userid = messages.fromid left join '.
				Config::DBPREF.'users as `to` on
					`to`.userid = messages.toid where messages.'.$msg_uid.' = '.
					System::$user->T__users__userid.' and '.
					'deleted != messages.'.$msg_uid.' order by sendtime desc',10);
	}

	/**
	 * Statisztika az üzenetekről
	 *
	 * Visszaadja asszociatív tömbben a belépett user üzeneteinek számát
	 * különböző kategóriákra vonatkozóan.
	 *
	 * <ul>
	 *	<li><b>unread:</b> Olvasatlan bejövő üzenetek száma</li>
	 *	<li><b>unreadout:</b> Olvasatlan kimenő üzenetek száma</li>
	 *	<li><b>inbox:</b> Összes bejövő üzenet</li>
	 *	<li><b>outbox:</b> Összes kimenő üzenet</li>
	 *	<li><b>news:</b> Összes hír</li>
	 *	<li><b>unreadnews:</b> Utolsó olvasás óta írt hírek száma. </li>
	 * </ul>
	 *
	 * @return array
	 */
	public static function msgStat()
	{
		$uid = (int)System::$user->T__users__userid;
		$time = System::$user->newsreadtime;
		$query = mysql_query("
			select
				sum(if(toid = $uid and readtime is null, 1, 0)) as unread,
				sum(if(fromid=$uid and readtime is null, 1, 0)) as unreadout,
				sum(if(toid=$uid, 1, 0)) as inbox,
				sum(if(fromid=$uid, 1, 0)) as outbox,
				sum(if(toid=0, 1, 0)) as news,
				sum(if(toid !=0 or timestampdiff(SECOND, sendtime, '$time') >= 0, 0, 1)) as unreadnews
			from ".Config::DBPREF."messages where 
				($uid in (toid, fromid) and deleted != $uid) or toid = 0
		");
		$stat = mysql_fetch_assoc($query);
		foreach ($stat as &$item)
		{
			$item = (int)$item;
		}
		return $stat;
	}

	/**
	 * Üzenet elküldésének kérése. 
	 *
	 * @param string $toname Címzett felhazsnáló neve
	 * @param string $subject Üzenet tárgya
	 * @param string $body Üzenet tartalma
	 * @param bool $news True, ha hír ( Ekkor mindegy mi a $toname ), egyébként false
	 * @return bool Sikerült-e elküldeni az üzenetet
	 */
	public static function sendRequest($toname, $subject, $body,$news=false)
	{
		$toname = mysql_real_escape_string($toname);
		if ($news)
		{
			$toid = 0;
		}
		else
		{
			$toid = ($result = mysql_fetch_row(mysql_query(
				"select userid from ".Config::DBPREF."users where username = '$toname'"))) ? (int)$result[0] : 0;
			if (!$toid)
			{
				self::$errors[] = 'Nincs ilyen felhasználó!';
				return false;
			}
		}
		if (trim($body) == "")
		{
			self::$errors[] = 'Üres üzenetet nem küldhetsz!';
		}

		if ($toid == System::$user->T__users__userid)
		{
			self::$errors[] = 'Magadnak nem küldhetsz üzenetet!';
		}

		if (count(self::$errors)) return false;
		self::send($toid, $subject, $body);
		return true;
	}

	/**
	 * Üzenet elküldése. 
	 *
	 * @param int $toid Címzett id-je. Ha 0, akkor hír lesz. 
	 * @param string $subject Üzenet tárgya
	 * @param string $body Üzenet tartalma
	 */
	private static function send($toid, $subject, $body)
	{
		$query = mysql_query("insert into ".
				Config::DBPREF."messages(`fromid`, `toid`, `subject`, `body`, `sendtime`)
				values (".System::$user->T__users__userid.",
				".((int)$toid).",'$subject','$body','".System::getTimeStamp()."')");
	}

	/**
	 * Hibaüzenetek tömbje
	 *
	 * @return array
	 */
	public static function errors()
	{
		return self::$errors;
	}

	/**
	 * Üzenetek törlése.
	 *
	 * @param mixed $id Üzenet azonosítója, ha integer. Ha tömb, akkor több
	 *			azonosító tömbje.
	 * @param bool $del True, ha azonnal törölni kell a rekordot is. False,
	 *			ha csak akkor kell törölni a rekordot, ha már a másik fél
	 *			töröltnek jelölte az üzenetet. 
	 *
	 */
	public static function deleteMsgs($id,$del=false)
	{
		if(is_array($id))
		{
			array_walk($id,create_function('&$id','$id = (int)$id;'));
			$id = "'".implode("','",$id)."'";
		}
		else
		{
			$id = (int)$id;
		}

		$pref = Config::DBPREF;
		$uid = System::$user->T__users__userid;

		$sql = 	"delete from ".$pref."messages where messageid in ($id) ";
		if ($del)
		{
			mysql_query($sql);
			return;
		}
		$sql .= " and ".$uid. " in (toid, fromid) and
				deleted != 0 and deleted != ".$uid;
		mysql_query($sql);

		$sql = 
			"update ".$pref."messages set
				deleted = ".$uid." where ".$uid." in (toid, fromid) and
				messageid in (".$id.")";
		mysql_query($sql);
	}

	/**
	 * Egy felhasználó összes üzenetének törlése, vagy töröltnek jelölése. 
	 *
	 * @param int $userid Felhasználó azonosítója
	 */
	public static function deleteMsgsOfUser($userid)
	{
		$uid = (int)$userid;

		$pref = Config::DBPREF;

		$sql = 	"delete from ".$pref."messages where  ";
		$sql .= " and ".$uid. " in (toid, fromid) and
				deleted != 0 and deleted != ".$uid;
		mysql_query($sql);

		$sql =
			"update ".$pref."messages set
				deleted = ".$uid." where ".$uid." in (toid, fromid)";
		mysql_query($sql);
	}

	/**
	 * Hírek törlése
	 *
	 * Csak admin, vagy tulajdonos törölhet hírt. 
	 *
	 * @param mixed $id Hír azonosítója, vagy azonosítók tömbje. 
	 */
	public static function deleteNews($id)
	{ 
		if (!System::$user->rank(array('admin','owner'))) return;
		self::deleteMsgs($id,true);
	}

	/**
	 *
	 * @param string $box Kimenő, vagy bejövö üzenet
	 *			{@link MESSAGES::INBOX}, {@link MESSAGES::OUTBOX}
	 * @return string
	 */
	public static function url($box = self::INBOX)
	{
		switch ($box)
		{
			case self::OUTBOX:
				$file = Config::FILE_MESSAGES_OUTBOX;
				break;
			case self::INBOX:
				$file = Config::FILE_MESSAGES_INBOX;
				break;
			case self::NEWS:
				$file = Config::FILE_MESSAGES_NEWS;
				break;
			default:
				$file = Config::FILE_MESSAGES_INBOX;
		}
		return System::getSitedir().$file;
	}

	/**
	 * Bejövő üzenetek url-je
	 *
	 * @return string
	 */
	public static function inboxUrl()
	{
		return self::url(Messages::INBOX);
	}


	/**
	 * Kimenő üzenetek url-je
	 *
	 * @return string
	 */
	public static function outboxUrl()
	{
		return self::url(Messages::OUTBOX);
	}

	/**
	 * Hírek listájának url-je
	 *
	 * @return string
	 */
	public static function newsUrl()
	{
		return self::url(Messages::NEWS);
	}
}
?>
