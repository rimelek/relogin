Figyelem! Ez nem egy portálrendszer, hanem loginrendszer. Bármily sokat is tud. 
Így a PHP alapszintű ismerete elengedhetetlen a használatához. 
Vagy legalább a kellő kitartás és hajlandóság a tanulásra. 

Szerző: Takács Ákos (Rimelek)
Weboldala: http://rimelek.hu
Login weboldala: http://rimelek/meghivos-loginrendszer-r-e-login-v2-0
E-mail: programmer kukac rimelek pont hu 

-----------------------------------------
 R.E. Login v2.0 - Rendszerigény
-----------------------------------------
Mindenképpen szükség van egy webszerverre, amin lehetőség van
PHP 5.0, valamint MySQL 5.0 használatára.
PHP GD támogatás szükséges
SMTP szerver ( Internetszolgáltató, vagy a tárhelyszolgáltató adja )

--------------------------------------------
 PHP 5.0 -val rendelkező ingyenes tárhelyek:
--------------------------------------------
 1. http://www.tarhely.biz/
 2. http://atw.hu
 3. http://freeweb.hu
 4. http://okhost.eu
 
 Ennél persze biztos több is van, de nem sorolom fel mindet. 
 Minden esetre, akinek fontos, hogy tárhelyével minél kevesebb
 probléma legyen, az ruházzon be egy fizetett tárhelybe. 
 Azokon már szinte mindenhol PHP 5 vagy annál magasabb verzió van. 

-----------------------------------------
 R.E. Login v2.0 - Telepítési útmutató
-----------------------------------------

1. Tömörítsd ki az RELogin-v2.0.zip -et.
2. Találsz benne egy relogin2 nevű mappát. 
   Ezt a mappát másold fel a webtárhelyedre. ( Nem csak a tartalmát. A mappát! ) 
3. Nyisd meg böngészőben a "relogin2" mappában levő "install" mappát.  
   Töltsd ki az adatokat, majd kattints alul a küldés gombra. 
   Megjegyzés: UTF8 karakterkódoláson kívül más is megadható a telepítőben, 
               Ekkor viszont minden fájlt külön el kell menteni a megadott 
               karakterkódolással. Így ajánlott hagyni UTF8 -nak, és
               a weboldalon is mindenhol ezt használni. 
4. Ha mindent jól csináltál, a telepítés sikeres volt. 

-----------------------------------------
 R.E. Login v2.0 - Használatba vétel
-----------------------------------------

Hozz létre egy "init.php" nevű fájlt a "relogin2" mappa mellett
Írd bele a következőt:
<?php
ob_start();

require_once('relogin2/classes/System.class.php');
$system = System::getInstance();
header('Content-type: text/html; charset='.Config::DBCHARSET);
?>

A fenti kódrészletet közvetlenül a fájl elejére írd. Se szóköz, 
se újsor ne legyen előtte. 

Az összes olyan fájlban, amiben a login bármely modulját használni szeretnéd, 
ezt az "init.php" -t kell beilleszteni a következő módon szintén a fájl elejére:
<?php
require_once 'init.php';
?>

Ehhez hasonlóan kell a modulokat is beilleszteni, de már bárhova lehet
a fenti kód után. Ezek már a relogin2 mappában lesznek. 
Tehát így fog kinézni a kód:
<?php
require_once 'relogin2/login.php';
?>

A fenti kód hatására megjelenik a beléptető űrlap. 
Minden szükséges modul a relogin2 mappában található. 
Egyik fájl sem érhető el közvetlenül. Csak az imént leírt módon beillesztve 
a fájlokat egy új .php fájlba, ahol előtte be volt illesztve az init.php

További modulok helye, és funkciója:
relogin2/admin.php         - Adminisztrációs felület
relogin2/changeprofile.php - Profil módosítás
relogin2/forgotpass.php    - Elfelejtett jelszó funkció
relogin2/invite.php        - Meghívó küldés
relogin2/login.php         - Bejelentkező űrlap
relogin2/logout.php        - Kijelentkező
relogin2/msginbox.php      - Bejövő üzenetek
relogin2/msgoutbox.php     - Kimenő üzenetek
relogin2/msgread.php       - Üzenet olvasás
relogin2/msgwrite.php      - Új üzenet írása
relogin2/news.php          - Hírek listája
relogin2/onlinelist.php    - Online lista
relogin2/profile.php       - Profil megtekintése
relogin2/register.php      - Regisztrációs űrlap
relogin2/search.php        - Felhasználó kereső ( Még csak usernévre )
relogin2/userlist.php      - Felhasználó lista 

Ha nem szeretnél vesződni ezekkel a beállításokkal, és szeretnéd rögtön kipróbálni a logint, 
akkor ha nem változtattál a telepítésnél a fájlneveken, tömörítsd ki az 
RELogin-v2.0-template.zip fájlt, és másold a tartalmát a relogin2 mappa mellé. 
Ez a csomag tartalmazza az init.php -t is. Valamint egy alap designt, hogy élvezhető legyen
a login használata. 

Az admin csak akkor érhető el, ha már beregisztráltál a telepítés után. 
Első regisztrálóként te leszel a tulajdonos. És elérhetővé válik az admin felület. 

A login támogatja a gravatar és mkavatar használatát. Ha nem tudod mi az,
nézd meg az alábbi két linket:
http://en.gravatar.com/
http://www.mkavatar.hu/

Bármi probléma esetén a http://rimelek.hu és http://phpstudio.hu oldalakon állok
rendelkezésetekre, és megpróbálok segíteni. 

Üdv.
Takács Ákos (Rimelek) 
