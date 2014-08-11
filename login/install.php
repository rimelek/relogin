<?php
/**************************************************************************
* R.E. login (1.8.1) - install.php                                        *
* ======================================================================= *
* Telep�t�st v�gz� f�jl                                                   *
* ======================================================================= *
* Ezt a programot a PHP Studio fejlesztette, a szerz�k: / This program    *
* was developed by the PHP Studio, the authors:                           *
* Rimelek                                                                 *
* ----------------------------------------------------------------------- *
* Weboldalunk / Our webpage: http://www.phpstudio.hu                      *
* Seg�ts�gny�jt�s / HelpDesk: http://forum.phpstudio.hu                   *
* Kapcsolat / Contact: php.prog@hotmail.com                               *
* ======================================================================= *
* Ez a program license alatt �ll, amit itt tekinthetsz meg: / This        *
* program is under a license, which you can see here:                     *
* http://license.phpstudio.hu                                             *
* ----------------------------------------------------------------------- *
* A license-szel kapcsolatos �szrev�teleid, megjegyz�seid, k�rd�seid  a   *
* license@phpstudio.hu e-mail c�men v�rjuk.                               *
* ----------------------------------------------------------------------- *
* You can send your remarks, opinions, questions to the following e-mail  *
* address: license@phpstudio.hu                                           *
* ======================================================================= *
* D�tum / Date:   2010.02.07.                                             *
**************************************************************************/

/*
Install f�jl. Install�l�s ut�n t�rlend�
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

$kapcsolat = @mysql_connect($dbhost, $dbuser, $dbpass) or die("Az adatb�zis be�ll�t�sok hib�sak. K�rlek, n�zd �t a config.php-t �jra");
$db = @mysql_select_db($dbname) or die("Hib�san �ll�tottad be az adatb�zis nevet. K�rlek jav�tsd a config.php-ben.");

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
        R.E login telep�t�se: <a href=\"$_SERVER[PHP_SELF]?install=true\">Telep�t�s</a>
        </p>";
    }else{
        print "
        <p align=\"center\">
        A login m�r telep�tve van".
        ((count($count) == 0) ? '!' : ',de egy vagy t�bb t�bla hi�nyzik!<br /><b>'.implode(', ',$count)) .
        "</b><br />�jra telep�t�s: <a href=\"$_SERVER[PHP_SELF]?install=true&reinstall=true\">�jra telep�t�s</a>
        </p>";
    }
}else{
    if(isset($_GET['reinstall']) and $_GET['reinstall'] == "true")
    {
        print "
        <p align=\"center\">
        A login rendszer �jratelep�t�s�re k�sz�lsz!<br />
        Ha folytatod, a telep�tett login-b�l minden adat el fog veszni.<br />
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
            <b>$i</b> A telep�t�s elakadt, vagy m�r megt�rt�nt!<br />
            <b>ERROR: </b>".mysql_error()
        );
    }
    $insert_sql = "select * from re_admin where aid = '1'";
    $insert_query = @mysql_query($insert_sql);
    $num = @mysql_num_rows($insert_query);
    if($num < 1) { mysql_query("INSERT INTO `re_admin` VALUES('1','0','0')"); }

    print "
    <p align=\"center\">A telep�t�s sikeres volt. L�pj a tov�bb linkre, majd k�rlek t�r�ld ki ezt a f�jlt ( <b>install.php</b> ) a t�rhelyedr�l.<br />
    <a href=\"$index_url\">[Tov�bb]</a><br />
    Az esetlegesen sz�ks�ges �jratelep�t�sre is ez a f�jl szolg�l!<br />
    Mentsd el valahova a t�rl�s el�tt, de a t�rhelyen ne maradjon!
    </p>";

    print "<p align=\"center\">
    Loginrendszer �jratelep�t�se: <a href=\"$_SERVER[PHP_SELF]?install=true&reinstall=true\">�jra telep�tek!</a>
    </p>";
}
?>
