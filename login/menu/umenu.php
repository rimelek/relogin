<?php
/**************************************************************************
* R.E. login (1.8.1) - umenu.php                                            *
* ======================================================================= *
* User men� �ssze�ll�t�sa                                                 *
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
Ez a f�jl az user men�t �ll�tja �ssze
*/
include_once("{$gyoker}$login_mappa/menuconfig/user_menu.php");

if($login->jog != NULL){

    $v = ($menu_rend == "o") ? "<br />" : " ";
    $umenu = (array)$umenu;
    foreach($umenu as $menu)
    {
        print $menu.$v;
    }
}
?>
