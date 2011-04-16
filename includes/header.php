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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="design/style.css" />
		<link rel="stylesheet" type="text/css" href="design/relogin.css" />
        <title>R.E. Login 2.0 - Minta szerkezet / Sablon</title>
    </head>
    <body>
		<div id="site">
		<div id="header">
		<ul id="menu">
			<li><a id="menu-home" href=".">Főoldal</a></li>
			<?php if (System::$logged) { ?>
			<li><a id="menu-invite" href="invite.php">Meghívók</a></li>
			<li><a id="menu-users" href="userlist.php">Felhasználók</a></li>
			<li><a id="menu-online" href="onlinelist.php">Online Felhasználók</a></li>
			<li><a id="menu-search" href="search.php">Kereső</a></li>
			<?php if (System::$user->rank(array('admin','owner'))) { ?>
			<li><a id="menu-admin" href="admin.php">Admin</a></li>
			<?php } ?>
			<?php } ?>
		</ul><br class="clear">
	
		</div>
			<div id="content"><br />
				
