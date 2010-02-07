<?php
/**************************************************************************
* R.E. login (1.8.1) - install.php                                        *
* ======================================================================= *
* Telepítést végzõ fájl                                                   *
* ======================================================================= *
* Ezt a programot a PHP Studio fejlesztette, a szerzõk: / This program    *
* was developed by the PHP Studio, the authors:                           *
* Rimelek                                                                 *
* ----------------------------------------------------------------------- *
* Weboldalunk / Our webpage: http://www.phpstudio.hu                      *
* Segítségnyújtás / HelpDesk: http://forum.phpstudio.hu                   *
* Kapcsolat / Contact: php.prog@hotmail.com                               *
* ======================================================================= *
* Ez a program license alatt áll, amit itt tekinthetsz meg: / This        *
* program is under a license, which you can see here:                     *
* http://license.phpstudio.hu                                             *
* ----------------------------------------------------------------------- *
* A license-szel kapcsolatos észrevételeid, megjegyzéseid, kérdéseid  a   *
* license@phpstudio.hu e-mail címen várjuk.                               *
* ----------------------------------------------------------------------- *
* You can send your remarks, opinions, questions to the following e-mail  *
* address: license@phpstudio.hu                                           *
* ======================================================================= *
* Dátum / Date:   2010.02.07.                                             *
**************************************************************************/

/*
Install fájl. Installálás után törlendõ
*/

include_once("inc/config.php");

$tables = array('re_admin','re_data','re_login','re_msg','re_meghiv');

$sql1 = "
CREATE TABLE if not exists `re_login` (
    `id` int(11) NOT NULL auto_increment,
    `user` varchar(50) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `pass` varchar(50) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `jog` char(1) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `ip` varchar(20) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `browser` varchar(100) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `regido` int(11) NOT NULL default '0',
    `belepes` int(11) NOT NULL default '0',
    `frissites` int(11) NOT NULL default '0',
    `online` int not null default '0',
    `status` char(1) character set 'latin2' collate 'latin2_general_ci' NOT NULL default '0',
    `mh` tinyint(4) NOT NULL default '0',
    PRIMARY KEY `id` (`id`)
    ) default character set = latin2 collate = latin2_general_ci
";

$sql2 = "
CREATE TABLE if not exists `re_data` (
    `id` int(11) NOT NULL auto_increment,
    `veznev` varchar(50) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `kernev` varchar(50) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `kor` tinyint(4) NOT NULL default '0',
    `nem` char(1) character set 'latin2' collate 'latin2_general_ci' NOT NULL default 'x',
    `orszag` varchar(50) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `varos` varchar(50) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `mail` varchar(100) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `uj_mail` varchar(100) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `public_mail` char(1) character set 'latin2' collate 'latin2_general_ci' default '0',
    `website` varchar(100) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `msn` varchar(100) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `skype` varchar(100) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `egyeb` text character set 'latin2' collate 'latin2_general_ci' not null default '',
    PRIMARY KEY `id` (`id`)
    ) default character set = latin2 collate = latin2_general_ci
";

$sql3 = "
CREATE TABLE if not exists `re_meghiv` (
    `mh_id` int(11) NOT NULL primary key auto_increment,
    `id` int(11) NOT NULL default '0',
    `kod` varchar(40) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `mail` varchar(100) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `ok` char(1) not null default '0',
    `time_hiv` int(11),
    `time_reg` int(11)
    ) default character set = latin2 collate = latin2_general_ci
";

$sql4 = "
CREATE TABLE if not exists `re_admin`(
    `aid` int(11) NOT NULL primary key auto_increment,
    `reg_block` char(1),
    `login_block` char(1)
    ) default character set = latin2 collate = latin2_general_ci
";

$sql5 = "
CREATE TABLE if not exists `re_msg`(
    `msg_id` int NOT NULL primary KEY auto_increment,
    `from_id` int,
    `to_id` int,
    `subject` varchar(50) character set 'latin2' collate 'latin2_general_ci' NOT NULL default ' ',
    `message` text character set 'latin2' collate 'latin2_general_ci' NOT NULL default '',
    `date` int NOT NULL default '0',
    `msg_status` char(1) character set 'latin2' collate 'latin2_general_ci' NOT NULL default '0',
    `news_status` text character set 'latin2' collate 'latin2_general_ci' NOT NULL default ''
) default character set = latin2 collate = latin2_general_ci
";

$kapcsolat = @mysql_connect($dbhost, $dbuser, $dbpass) or die("Az adatbázis beállítások hibásak. Kérlek, nézd át a config.php-t újra");
$db = @mysql_select_db($dbname) or die("Hibásan állítottad be az adatbázis nevet. Kérlek javítsd a config.php-ben.");

if(!isset($_GET['install']) or $_GET['install'] != 'true')
{
    $count = array();
    foreach($tables as $not_exists)
    {
        if(!@mysql_query("select * from $not_exists"))
        {
            $count[] = $not_exists;
        }
    }
    if(count($count) == count($tables))
    {
        print "
        <p align=\"center\">
        R.E login telepítése: <a href=\"$_SERVER[PHP_SELF]?install=true\">Telepítés</a>
        </p>";
    }else{
        print "
        <p align=\"center\">
        A login már telepítve van".
        ((count($count) == 0) ? '!' : ',de egy vagy több tábla hiányzik!<br /><b>'.implode(', ',$count)) .
        "</b><br />Újra telepítés: <a href=\"$_SERVER[PHP_SELF]?install=true&reinstall=true\">Újra telepítés</a>
        </p>";
    }
}else{
    if(isset($_GET['reinstall']) and $_GET['reinstall'] == "true")
    {
        print "
        <p align=\"center\">
        A login rendszer újratelepítésére készülsz!<br />
        Ha folytatod, a telepített login-ból minden adat el fog veszni.<br />
        Folytatod?
        <a href=\"$_SERVER[PHP_SELF]\">Nem</a>
        <a href=\"$_SERVER[PHP_SELF]?install=true&reinstall=truetrue\">Igen</a>
        </p>";
        exit;
    }elseif(isset($_GET['reinstall']) and $_GET['reinstall'] == "truetrue")
    {
        foreach($tables as $value)
        {
            $del_sql = "DROP TABLE if exists $value";
            mysql_query($del_sql) or die("<b>ERROR: </b>".mysql_error());
        }
    }

    for($i=1;$i<=5;$i++)
    {
        $sql = 'sql'.$i;
        mysql_query($$sql)
        or die("
            <b>$i</b> A telepítés elakadt, vagy már megtörtént!<br />
            <b>ERROR: </b>".mysql_error()
        );
    }
    $insert_sql = "select * from re_admin where aid = '1'";
    $insert_query = @mysql_query($insert_sql);
    $num = @mysql_num_rows($insert_query);
    if($num < 1) { mysql_query("INSERT INTO `re_admin` VALUES('1','0','0')"); }

    print "
    <p align=\"center\">A telepítés sikeres volt. Lépj a tovább linkre, majd kérlek töröld ki ezt a fájlt ( <b>install.php</b> ) a tárhelyedrõl.<br />
    <a href=\"$index_url\">[Tovább]</a><br />
    Az esetlegesen szükséges újratelepítésre is ez a fájl szolgál!<br />
    Mentsd el valahova a törlés elõtt, de a tárhelyen ne maradjon!
    </p>";

    print "<p align=\"center\">
    Loginrendszer újratelepítése: <a href=\"$_SERVER[PHP_SELF]?install=true&reinstall=true\">Újra telepítek!</a>
    </p>";
}
?>
