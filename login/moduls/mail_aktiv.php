<?php
/**************************************************************************
* R.E. login (1.8.1) - mail_aktiv.php                                     *
* ======================================================================= *
* A megváltoztatott mail cím aktiválása                                   *
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
include_once("../inc/config.php");
//Ha az aktiváló kód bene van az url-ben
if(isset($_GET['code']) and strlen($_GET['code']) >= 32)
{
    $id = substr($_GET['code'],32);
    $sql = "
    select * from re_login,re_data
    where re_data.id = re_login.id and
    re_login.id = '$id'
    ";
    
    $query = mysql_query($sql);
    $fetch = mysql_fetch_assoc($query);
    //Ha az új választott mail már foglalt
   if(!$login->tiltott_mail($fetch['uj_mail'])){
    //Ha az aktiváló kód helyes
    if($_GET['code'] == $login->re_code($fetch['uj_mail'],$id,$fetch['pass']))
    {
        $sql1 = "
        update re_data set mail = '$fetch[uj_mail]'
        where id = '$id'
        ";
        mysql_query($sql1);
        print "
        <p align=\"center\">
        Sikeres e-mail cím aktiválás!<br />
        <a href=\"$index_url\">[Tovább]</a>
        </p>";
    //Ha az aktiváló kód helytelen
    }else{
        print "
        <p align=\"center\">
        Helytelen aktiváló kód!<br />
        <a href=\"$index_url\">[Tovább]</a>
        </p>";
    }
    //ha az új választott mail cím már foglalt
   }else{
    print "
    <p align=\"center\">
    Ez az e-mail cím már foglalt!<br />
    <a href=\"$index_url\">[Tovább]</a>
    </p>";
   }
}

?>
