<?php
/**************************************************************************
* R.E. login (1.8.1) - mail_aktiv.php                                     *
* ======================================================================= *
* A megv�ltoztatott mail c�m aktiv�l�sa                                   *
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
include_once("../inc/config.php");
//Ha az aktiv�l� k�d bene van az url-ben
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
    //Ha az �j v�lasztott mail m�r foglalt
   if(!$login->tiltott_mail($fetch['uj_mail'])){
    //Ha az aktiv�l� k�d helyes
    if($_GET['code'] == $login->re_code($fetch['uj_mail'],$id,$fetch['pass']))
    {
        $sql1 = "
        update re_data set mail = '$fetch[uj_mail]'
        where id = '$id'
        ";
        mysql_query($sql1);
        print "
        <p align=\"center\">
        Sikeres e-mail c�m aktiv�l�s!<br />
        <a href=\"$index_url\">[Tov�bb]</a>
        </p>";
    //Ha az aktiv�l� k�d helytelen
    }else{
        print "
        <p align=\"center\">
        Helytelen aktiv�l� k�d!<br />
        <a href=\"$index_url\">[Tov�bb]</a>
        </p>";
    }
    //ha az �j v�lasztott mail c�m m�r foglalt
   }else{
    print "
    <p align=\"center\">
    Ez az e-mail c�m m�r foglalt!<br />
    <a href=\"$index_url\">[Tov�bb]</a>
    </p>";
   }
}

?>
