<?php
/**************************************************************************
* R.E. login (1.8.1) - avmenu.php                                           *
* ======================================================================= *
* Vendég menü összeállítása                                               *
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
Ez a fájl az vendég menüt állítja össze,
ami mindenkinek megjelenik belépés nélkül
*/

include_once("{$gyoker}$login_mappa/menuconfig/vendeg_menu.php");


    $v = ($menu_rend == "o") ? "<br />" : " ";
    $vmenu = (array)$vmenu;
    foreach($vmenu as $menu)
    {
        print $menu.$v;
    }

?>
