<?php
/**
 * R.E. Login 2.0 - Admin - admin.php
 *
 * Admin oldal sablonja.
 * Az admin mappában az aloldalak sablonja van. <br />
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
require_once System::getIncLoginDir().'includes/admin.php';
?>
<div>
	<div style="float: left; width: 200px;">
		<ul id="relogin-admin-menu">
			<li><a id="relogin-admin-settings" href="<?php print $url_settings ?>">Beállítások</a></li>
			<li><a id="relogin-admin-ranks" href="<?php print $url_ranks ?>">Rangok kezelése</a></li>
			<li><a id="relogin-admin-invites" href="<?php print $url_invite ?>">Meghívók kezelése</a></li>
			<li><a id="relogin-admin-users" href="<?php print $url_users ?>">Felhasználók</a></li>
		</ul>
	</div>
	<div style="float: left; padding-left: 70px;">
		<?php
		/**
		 * @ignore
		 */
		require_once System::getIncLoginDir().'admin/'.$file.'.php';
		?>
	</div>
	<br style="clear: both;" />
	
</div>