------- R.E.( RimElek ) Login 1.8.1------
+++++++++++
+Telepítés+
+++++++++++

1. Nyisd meg a login/inc/config.php fájlt, és állítsd be a szükésges adatokat.
2. a login/moduls/login.php fájlt illeszd be az oldaladon oda,
   ahova a bejelentkezési ûrlapot szeretnéd helyezni.

pl.: <?php include_once("login/moduls/login.php"); ?>

3. A fájlok elejére, ahol a login bármely modulját használod, illeszd be a config.php-t 
A '<?php' elõtt sem szóköz sem html kód nem lehet!
pl.: <?php include_once("login/inc/config.php"); ?>

4. Indítsd el a böngészõdben az install.php-t.
   Ennek helye a login/install.php

5. Ha a telepítés sikeres, jönnek a további beállítások

++++++++++++++++++++++++++++++
+Egyéb fájlok, és beállítások+
++++++++++++++++++++++++++++++

login/inc/config
----------------
A telepítés elõtt szükséges beállítások.
Mielõtt bármit tennél, állítsd be az összes szükséges adatot a fájlban

login/install.php
-----------------
A telepítést végzõ fájl.

login/moduls/admin.php.
----------------
Az admin felület.
Ezt a fájlt illeszd be oda, ahol szeretnéd megjeleníteni az admin felületet.
pl.: <?php include_once("login/moduls/admin.php"); ?>

login/moduls/list.php
--------------
A user és online lista.
Ezt a fájlt illeszd be oda, ahol a userlistát,
és ahol az online listát szeretnéd megjeleníteni.
onlinelista esetén a rá mutató link URL-jét egészitsd ki a következõ módon:

fajlod.php?list=o

Ez csak egy példa. A kérdõjel elõtt a saját fájlod útvonala szerepeljen

login/moduls/newpass.php
------------------------
A jelszó emlékeztetõ fájl. Illeszd be a már ismert módon tetszõleges helyre az oldaladon, ahol használni szeretnéd.
Itt történik az új jelszó kérés, és annak aktiválása is.

login/moduls/mh.php
------------
A meghívó ûrlap.
Ezt a fájlt illeszd be arra az oldaladra, ahol a meghívás ûrlapját szeretnéd megjeleníteni.
pl.: <?php include_once("login/moduls/mh.php");  ?>


login/moduls/uinfo.php
---------------
Az adatlap kitöltése.
Ezt a fájlt illeszd be oda, ahol az adatlap kitöltését szeretnéd megjeleníteni
pl.: <?php include_once("login/moduls/uinfo.php"); ?>


login/moduls/uinfo_olv.php
-------------------
A tagok adatlapjának kiolvasása
Ezt a fájlt illeszd be oda, ahol a userinfót szeretnéd majd megjeleníteni

login/moduls/msg.php
--------------------
Ez a fájl az üzenetküldés modul. 2 helyre kell beillesztened.
1. Ahol az üzenetküldõ ûrlapot, valamint a listázást, és olvasást szeretnéd megjeleníteni,
2. és oda, ahova az üzenetek linket szeretnéd helyezni

A program magától beállítja, hogy mikor melyiket jelenítse meg.
Neked csak a config.php -ban kell beállítani annak a fájlnak
az elérési útját, amiben az üzenetküldés van. Máshol csak a link jelenik meg


login/menuconfig/amenu.php
login/menuconfig/vmenu.php
login/menuconfig/umenu.php
------------
Ezek sorrendben az admin, vendég, és usermenük. illeszd õket tetszõleges helyre.
pl.: <?php  include_once("login/menu/umenu.php"); ?>

Az egyes menük tartalmát beállíthatod a login/menu mappában található fájlokban.
Beállítható hogy oszlopban, vagy sorban jelenítse meg a menüket.

A login rendszer beállítható nyílt regisztrációra, vagy meghívás alapúra az admin felületen.
A regisztráció le is tiltható szükség esetén.
Amennyiben a regisztráció nyilt, megjelenik a regisztráció link a bejelentkezés ûrlap alatt

Blokkolható a belépés is.
Törölhetõk a userek,
jogok változtathatók,
és a meghívókat is az admin osztja ki, vagy veszi el.
Adminok végtelen számú meghívóval rendelkezhetnek, hisz maguknak állíthatják be
a meghívók számát.
Adminnak nem fogy a meghívója a meghívó elküldésekor sem.

Alapbeállításban a telepítés után a regisztráció nyilt, így az elsõ regisztráló tag admin jogot kap.
Ez után letiltható, átállítható a regisztráció típusa is, vagy hagyható nyilt regisztrációnak.

1.2 verzióhoz képest újítások:
 - Hírküldés: Az üzenetküldésen belül, az adminok kiválaszthatják,
   hogy minden tagnak címzik az üzenetet
 - E-mail aktiváció: Minden regisztráció, és mail cím változtatás esetén aktiválni kell
   az e-mail címre érkezõ linkkel az új mailcímet
   (Ha 24 órán belül nincs aktiválva: Új regisztrációkor a regisztráció törlõdik,
                                      Változtatás esetén visszaáll az eredeti mailcím )
 - Az e-mail cím elrejthetõ a közösség elõl
 - ÚJ jelszóemlékeztetõ: Az új jelszót is aktiválni kell a mailben kapott linkkel.
 - Javítva az adatlap kitöltés, és az üzenetküldés
 - Valamint a telepítés is egyszerûsödött
1.8 -hoz képesti változás:
 - Notice szitnû hibák javítását tartalmazza
 - Hírek törlése hibás volt. Ez is javítva
 - A telepítõben a felesleges pontosvesszõk törölve az sql kódokból. 
 - A tábla karakterkódolása most már latin2, és nem latin1, mint korábban volt
 - Egyéb apróbb javítások 


|----------------------------------------------------------|
|Megjegyzés:                                               |
|A fájlok elérési útvonala attól a fájltól indul,          |
| ahova beilleszted a kódokat.                             |
|A példa fájlokat, melyek a login mappán kívûl vannak,     |
|eltávolíthatod. Csak a beállításhoz szolgálnak segítségül |
|Kérdés esetén írjatok a fórumba: http://www.phpstudio.hu  |
|----------------------------------------------------------|



