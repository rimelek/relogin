<?php
/**
 * R.E. Login 2.0 - Filter - class/UserFilter.class.php
 *
 * Felhasználólista szűrő<br />
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
 * Felhasználólista szűrő
 *
 * Megadhatók filterek, ami alapján szűri a felhasználókat a listában.
 * Például online van-e, mi egy mező értéke illetve érvényes-e az e-mail címe. 
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Login weblapja:</b> {@link http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0 R.E. Login v2.0}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @license http://www.gnu.org/licenses/gpl.html
 * @package RELogin
 */
class UserFilter
{
	/**
	 * Érvényes email cím
	 */
	const EMAIL_VALID = 'email_valid';

	/**
	 * Érvénytelen email cím
	 */
	const EMAIL_INVALID = 'email_invalid';

	/**
	 * Minden email cím
	 */
	const EMAIL_ALL = 'email_all';

	/**
	 * field like 'value%'
	 */
	const LIKE_LEFT = 'like_left';

	/**
	 * field like '%value'
	 */
	const LIKE_RIGHT = 'like_right';

	/**
	 * field like '%value%'
	 */
	const LIKE_BOTH = 'like_both';

	/**
	 * field = 'value'
	 */
	const LIKE_EQUAL = 'like_equal';

	/**
	 * Online felhasználók
	 */
	const ONLINE_YES = 'online_yes';

	/**
	 * Offline felhasználók
	 */
	const ONLINE_NO = 'online_no';

	/**
	 * Online és offline felhazsnálók is. 
	 */
	const ONLINE_YES_NO = 'online_yes_no';


	/**
	 * Filterek tömbje
	 *
	 * @var array
	 */
	private $filters = array();

	/**
	 * @ignore
	 */
    public function  __construct()
	{

	}

	/**
	 * Online filter felvétele
	 *
	 * @param string $online Értéke lehet {@link UserFilter::ONLINE_YES},
	 *				{@link UserFilter::ONLINE_NO}, {@link UserFilter::ONLINE_YES_NO}
	 */
	public function addOnlineFilter($online=self::ONLINE_YES_NO)
	{
		switch ($online)
		{
			case self::ONLINE_YES:
				$this->filters['online'] =
					" (users.onlinestatus = 1 and
						users.refreshtime is not null and
						timestampdiff(SECOND, users.refreshtime, '".
						System::getTimeStamp()."') < ".Config::MAX_ONLINE_TIME.") ";
				return;
			case self::ONLINE_NO:
				$this->filters['online'] =
					" (users.onlinestatus = 0 or
						users.refreshtime is null or
						timestampdiff(SECOND, users.refreshtime, '".
						System::getTimeStamp()."') >= ".Config::MAX_ONLINE_TIME.") ";
				return;
		}
		unset($this->filters['online']);

	}

	/**
	 * Like filter felvétele
	 *
	 * Mező értéke szerint szűri a felhasználót
	 *
	 * @param string $field Mező neve
	 * @param mixed $value Mező értéke
	 * @param string $like Összehasonlítás módja Lehet
	 *		{@link UserFilter::LIKE_LEFT}, {@link UserFilter::LIKE_RIGHT},
	 *		{@link UserFilter::LIKE_BOTH}, {@link UserFilter::LIKE_EQUAL}
	 */
	public function addLikeFilter($field, $value,$like=self::LIKE_BOTH)
	{
		$this->filters['like'][] = array(
			'field' => $field,
			'value' => mysql_real_escape_string($value),
			'like' => $like
		);
	}

	/**
	 * Equal filter
	 *
	 * @see UserFilter::addLikeFilter()
	 *
	 * @param string $field Mező neve
	 * @param mixed $value Mező értéke
	 */
	public function addEqualFilter($field, $value)
	{
		$this->addLikeFilter($field, $value, self::LIKE_EQUAL);
	}

	/**
	 * Email érvényesség szerinti szűrés
	 *
	 * @param string $type {@link UserFilter::EMAIL_VALID},	{@link UserFilter::EMAIL_INVALID}
	 */
	public function addValidEmailFilter($type=self::EMAIL_VALID)
	{
		if ($type == self::EMAIL_VALID)
		{
			$this->filters['valid_email'] = " profiles.useremail = users.useremail ";
		}
		else if ($type == self::EMAIL_INVALID)
		{
			$this->filters['valid_email'] = " profiles.useremail != users.useremail ";
		}
	}

	/**
	 * Filter sztring
	 *
	 * Visszaadja az sql where -t. A where kulcsszóval együtt, ha szükséges. 
	 *
	 * @return string
	 */
	public function filterString()
	{
		if (!count($this->filters)) return '';
		$sql = " where ";
		foreach ($this->filters as $name => &$filter)
		{
			switch ($name)
			{
				case 'valid_email':
					$sql .= $filter;
					break;
				case 'like':
					$like_filter = array();
					foreach ($filter as $item)
					{
					    $like = $item['like'];
						$field = $item['field'];
						$value = $item['value'];
						$left = ($like == self::LIKE_BOTH or $like == self::LIKE_LEFT) 
							? '%' : '';
						$right = ($like == self::LIKE_BOTH or $like == self::LIKE_RIGHT)
							? '%' : '';

						$op = ($like == self::LIKE_EQUAL)
							? ' = ' : ' like ';

						$like_filter[] = $field.$op."'".$left.$value.$right."'";
						
					}
					$sql .= " ".implode(' and ',$like_filter)." ";
					break;
				case 'online':
					$sql .= $filter;
					break;
			}
		}
		return $sql;
	}
}
?>
