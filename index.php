<?php include_once("login/inc/config.php"); ?>
<html>

   <head>
      <title>login</title>
      <meta http-equiv="generator" content="PHP Designer 2005" />
      <meta http-equiv="Content-type" content="text/html; charset=iso-8859-2" />
   </head>

   <body bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#800080" alink="#FF0000">

<table border="1" cellpadding="0" cellspacing="0">

<tr><td colspan="2" width="800" height="80">
<?php  include("login/menu/amenu.php"); ?>
</td></tr>

<tr>

<td width="150" height="400" valign="top">
<?php include("login/moduls/login.php"); ?>
<?php
include("login/menu/umenu.php");
include("login/moduls/msg.php");
?>
</td>
<td width="650" height="400">
<?php 
include("login/menu/vmenu.php");

$get = isset($_GET['get']) ? $_GET['get'] : '';
if($get == "mhiv")
{
    include("login/moduls/mh.php");
}elseif($get == "adm"){
    include("login/moduls/admin.php");
}

?>
</td>
</tr>
</table>

   </body>
</html>
