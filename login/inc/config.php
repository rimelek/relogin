<?php
//adatbázis beállításai

/* host név. Általában localhost.
a szolgáltatónál utánanézhetsz*/
$dbhost = "localhost";

/* adatbázis felhasználónév.
Általában a nickneveddel egyezik meg ingyenes tárhelyen*/
$dbuser = "root";

/* Adatbázis jelszó.
Általában a tárhelyed jelszavával egyezik meg */
$dbpass = "";

/* adatbázis neve. Általában a felhasználónévvel azonos */
$dbname = "teszt";

//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\

//url beállítások

/* Az aldomain ingyenes tárhelyeken. pl atw-n (általában nem kell)*/
$aldomain = "";
/*++++ 
Gyökér könyvtár aldomain-es tárhelyen az aldomain-nel indul. 
pl: http://rimelek.atw.hu --> /rimelek/login/login.php 
+++++*/

/*a login mappa elérési útvonala a gyökér könyvtártól Ezt nem kell az aldomain-nal kezdeni.*/
$login_mappa = "login";

/* Gyökér könyvtártól indulva a weboldalad elérése, ahova beilleszted a beléptetést */
$index_url = "index.php";

/* Gyökér könyvtártól indulva a annak a fájlnak az elérési útvonala, ahova beilleszted a regisztrációs ûrlapot. */
$reg_form = "reg.php";

/* Gyökér könyvtártól indulva annak a fájlnak az elérési útvonala, ahova beilleszted a userinfo -t megjelenítõ fájlt */
$uinfo_olv = "uinfo_olv.php";

/* Gyökér könyvtártól indulva annak a fájlnak az elérési útvonala, ahova beilleszted a newpass.php -t megjelenítõ fájlt */
$newpass_url = "newpass.php";

/* Gyökér könyvtártól indulva annak a fájlnak az elérési útvonala, ahova beilleszted az msg.php -t megjelenítõ fájlt */
$message_url = "msg.php";

/* Ezeken az oldalakon nem kér jelszót! 
Új nyilvános oldal hozzáadása:
$nopass[] = 'a fájl neve';
*/
$nopass[] = 'index.php';
$nopass[] = 'newpass.php';

/****************************************/
//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\

//meghívó beállítások

$subject = "Meghívó"; /* A meghívó e-mail tárgya. */

/* Elõre meghatározott szöveg, mely a meghívót küldõ üzenete mellett megjelenik az e-mailben. */
$text_plusz = "A regisztrációhoz kattints az alábbi linkre!";

/* A meghívó e-mail feladója. Bármi lehet. */
$from = "admin@localhost";

/* A regisztrációkor kiosztott meghívók száma.
Ha nullára állítod, nem kapnak meghívót a regisztrációkor.*/
$mh_alap = "5";

//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\

//egyéb beállítások

/* Hány perc után törölje az online listáról a tagokat, ha nem frissitenek. */
$max_ido = 15;

/*Hány nap után törlõdjenek az olvasott üzenetek*/
$msg_max_time = 14;

/*Maximum hány üzenetet fogadhatnak a felhasználók*/
$msg_max_uzi  = 10;

/* Egyszerrre hány felhasználót listázzon ki a userlista, és az online lista */
$list_limit = 3;

/* Egyszerrre hány üzenetet listázzon ki az üzeneteknél. */
$msg_list_limit = 6;

/* Egyszerre hány oldal linkje jelenjen meg a listázásoknál.
pl:  << 1 2 3 4 5 >> vagy << 1 2 3 4 5 6 7 8 9 >> */
$szam_list = 4;

/* A szuperadmin nickje. Vagyis a te nicked. */
$super_admin = "rimelek";


/************ INNENTÕL NE ÍRD ÁT!! **********************/

$varArray = array("index_url","reg_form","uinfo_olv","newpass_url","message_url");

foreach($varArray as $key=>$value)
{
    $$value = ltrim($$value,"/");
    $$value = "/".$$value;
}


$aldomain = trim($aldomain,"/");
$aldomain = ($aldomain) ? $aldomain."/" : "";
$dir_c = substr_count($_SERVER['PHP_SELF'], "/") - 1;
$gyoker = @str_repeat("../", $dir_c);
$login_mappa = trim($login_mappa, "/");
if(end(explode("/",$_SERVER['PHP_SELF'])) != "install.php"){
include("{$gyoker}$login_mappa/login_class.php");
}

define('__RELOGIN__','true');
?>
