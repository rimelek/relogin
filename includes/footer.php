<?php
/*
 * Alap sablon az R.E. Login 2.0 -hoz.
 *
 * Szerző: Takács Ákos (Rimelek)
 * E-mail: programmer@rimelek.hu
 * Weboldal: http://rimelek.hu
 * Login weboldala: http://rimelek.hu/meghivos-loginrendszer-r-e-login-v2-0
 *
 * Ikonokat a következő weboldalakról töltöttem le:
 * http://www.freeiconsweb.com/
 * http://sixrevisions.com/resources/40-beautiful-free-icon-sets/
 */
?>

</div>
<div id="right-side">
	<?php if(!System::$logged) {
			require_once 'relogin2/login.php';
	} else { ?>
	<img src="<?php print System::$user->avatar(120); ?>" alt="avatar" />
	<ul id="right-menu">
		<li><a id="relogin-logout" href="<?php print System::logoutLink() ?>">Kijelentkezés</a></li>
		<li><a id="relogin-profile" href="profile.php">Profil</a></li>
		<li><a id="relogin-inbox" href="msginbox.php">Posta (<?php
			
			$stat = System::msgStat();
			print $stat['inbox'].'/'.$stat['unread'];
		?>)</a></li>
	</ul>
	<?php } ?>
</div>
	<div id="footer" class="clear">
		Minden jog fenntartva &copy; 2010 Teneved<br />
		Ez csak egy példa struktúra a loginhoz. Nem kötelező ezt használni.<br />
	</div>
</div>
	</body>
</html>
