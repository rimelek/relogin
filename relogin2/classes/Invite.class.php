<?php
/**
 * R.E. Login 2.0 - Meghívó - class/Invite.class.php
 *
 * Egy meghívó adatainak lekérdezése<br />
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
 * Meghívó
 *
 * Egy meghívót valósít meg. Ki küldte, milyen címre.
 * Mi lett a meghívott személy azonosítója. MIkor lett elküldve a meghívó.
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @property int $inviteid Meghívó azonosítója
 * @property int $fromid A meghívó személy azonosítója
 * @property int $toid A meghívott személy azonosítója, ha már beregisztrált.
 * @property string $code A meghívóhoz tartozó hash
 * @property string $email A meghívott személy e-mail címe
 * @property string $sendtime timestamp típusú mysql mező. Mikor küldték a meghívót. 
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class Invite extends IsMySQLClass
{
	/**
	 *
	 * @param mixed $tablelist Ha tömb akkor tábla+mezőlista.
	 *			Ha null, akkor az összes mezőt jelent. Mindkét esetben
	 *			új meghívóról van szó. 
	 *			Ha integer, akkor a meghívó azonosítója. És egy meghívót kérdez le. 
	 */
    public function  __construct($tablelist=null)
	{
		$inviteid = null;
		if (is_integer($tablelist))
		{
			//Az inviteid lett megadva
			$inviteid = $tablelist;
			$tablelist = null;
		}
		if ($tablelist === null)
		{
			$tablelist = array(
				Config::DBPREF."invites as invites" => array("*")
			);
		}
		parent::__construct($tablelist);
		if ($inviteid !== null)
		{
			$this->init(
				Config::DBPREF."invites as invites where
				inviteid = $inviteid");
		}
	}

	/**
	 * Meghívó inicializálása
	 *
	 * @see IsMySQLClass::init()
	 *
	 * @param string $sql From utáni sql kód
	 */
	public function init($sql)
	{
		parent::init($sql, true);
	}
}
?>
