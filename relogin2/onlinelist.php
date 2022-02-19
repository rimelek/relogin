<?php
/**
 * R.E. Login 2.0 - Online lista - onlinelist.php
 *
 * Online felhasználók listájának sablonja. <br />
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
require_once System::getIncLoginDir().'includes/onlinelist.php';

$pageLinks = $users->pageLinks(10);
?>
<div align="center">
	<?php print $pageLinks; ?>
</div>
<table align="center" class="relogin-users">
	<tr>
		<th class="users-nick">Nick</th>
		<th class="users-rank">Rang</th>
		<th class="users-mail">E-mail</th>
		<th class="users-profile">Adatlap</th>
	</tr>
<?php foreach ($users as $user) { ?>
	<tr>
		<td class="users-nick"><?php print $user->username ?></td>
		<td class="users-rank"><?php print $user->rankName() ?></td>
		<td class="users-mail"><?php print ($user->public_mail and !empty($user->T__users__useremail) )
				? $user->T__users__useremail  : "Rejtett"; ?></td>
		<td class="users-profile"><a href="<?php print str_replace('%7Bid%7D',$user->T__users__userid,$profile_tpl_url) ?>">Adatlap</a></td>
	</tr>
<?php } ?>
</table>
<div align="center">
	<?php print $pageLinks; ?>
</div>