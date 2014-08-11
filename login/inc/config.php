<?php
//adatb�zis be�ll�t�sai

/* host n�v. �ltal�ban localhost.
a szolg�ltat�n�l ut�nan�zhetsz*/
$dbhost = "localhost";

/* adatb�zis felhaszn�l�n�v.
�ltal�ban a nickneveddel egyezik meg ingyenes t�rhelyen*/
$dbuser = "root";

/* Adatb�zis jelsz�.
�ltal�ban a t�rhelyed jelszav�val egyezik meg */
$dbpass = "";

/* adatb�zis neve. �ltal�ban a felhaszn�l�n�vvel azonos */
$dbname = "teszt";

//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\

//url be�ll�t�sok

/* Az aldomain ingyenes t�rhelyeken. pl atw-n (�ltal�ban nem kell)*/
$aldomain = "";
/*++++ 
Gy�k�r k�nyvt�r aldomain-es t�rhelyen az aldomain-nel indul. 
pl: http://rimelek.atw.hu --> /rimelek/login/login.php 
+++++*/

/*a login mappa el�r�si �tvonala a gy�k�r k�nyvt�rt�l Ezt nem kell az aldomain-nal kezdeni.*/
$login_mappa = "login";

/* Gy�k�r k�nyvt�rt�l indulva a weboldalad el�r�se, ahova beilleszted a bel�ptet�st */
$index_url = "index.php";

/* Gy�k�r k�nyvt�rt�l indulva a annak a f�jlnak az el�r�si �tvonala, ahova beilleszted a regisztr�ci�s �rlapot. */
$reg_form = "reg.php";

/* Gy�k�r k�nyvt�rt�l indulva annak a f�jlnak az el�r�si �tvonala, ahova beilleszted a userinfo -t megjelen�t� f�jlt */
$uinfo_olv = "uinfo_olv.php";

/* Gy�k�r k�nyvt�rt�l indulva annak a f�jlnak az el�r�si �tvonala, ahova beilleszted a newpass.php -t megjelen�t� f�jlt */
$newpass_url = "newpass.php";

/* Gy�k�r k�nyvt�rt�l indulva annak a f�jlnak az el�r�si �tvonala, ahova beilleszted az msg.php -t megjelen�t� f�jlt */
$message_url = "msg.php";

/* Ezeken az oldalakon nem k�r jelsz�t! 
�j nyilv�nos oldal hozz�ad�sa:
$nopass[] = 'a f�jl neve';
*/
$nopass[] = 'index.php';
$nopass[] = 'newpass.php';

/****************************************/
//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\

//megh�v� be�ll�t�sok

$subject = "Megh�v�"; /* A megh�v� e-mail t�rgya. */

/* El�re meghat�rozott sz�veg, mely a megh�v�t k�ld� �zenete mellett megjelenik az e-mailben. */
$text_plusz = "A regisztr�ci�hoz kattints az al�bbi linkre!";

/* A megh�v� e-mail felad�ja. B�rmi lehet. */
$from = "admin@localhost";

/* A regisztr�ci�kor kiosztott megh�v�k sz�ma.
Ha null�ra �ll�tod, nem kapnak megh�v�t a regisztr�ci�kor.*/
$mh_alap = "5";

//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\

//egy�b be�ll�t�sok

/* H�ny perc ut�n t�r�lje az online list�r�l a tagokat, ha nem frissitenek. */
$max_ido = 15;

/*H�ny nap ut�n t�rl�djenek az olvasott �zenetek*/
$msg_max_time = 14;

/*Maximum h�ny �zenetet fogadhatnak a felhaszn�l�k*/
$msg_max_uzi  = 10;

/* Egyszerrre h�ny felhaszn�l�t list�zzon ki a userlista, �s az online lista */
$list_limit = 3;

/* Egyszerrre h�ny �zenetet list�zzon ki az �zenetekn�l. */
$msg_list_limit = 6;

/* Egyszerre h�ny oldal linkje jelenjen meg a list�z�sokn�l.
pl:  << 1 2 3 4 5 >> vagy << 1 2 3 4 5 6 7 8 9 >> */
$szam_list = 4;

/* A szuperadmin nickje. Vagyis a te nicked. */
$super_admin = "rimelek";


/************ INNENT�L NE �RD �T!! **********************/

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
