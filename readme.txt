------- R.E.( RimElek ) Login 1.8.1------
+++++++++++
+Telep�t�s+
+++++++++++

1. Nyisd meg a login/inc/config.php f�jlt, �s �ll�tsd be a sz�k�sges adatokat.
2. a login/moduls/login.php f�jlt illeszd be az oldaladon oda,
   ahova a bejelentkez�si �rlapot szeretn�d helyezni.

pl.: <?php include_once("login/moduls/login.php"); ?>

3. A f�jlok elej�re, ahol a login b�rmely modulj�t haszn�lod, illeszd be a config.php-t 
A '<?php' el�tt sem sz�k�z sem html k�d nem lehet!
pl.: <?php include_once("login/inc/config.php"); ?>

4. Ind�tsd el a b�ng�sz�dben az install.php-t.
   Ennek helye a login/install.php

5. Ha a telep�t�s sikeres, j�nnek a tov�bbi be�ll�t�sok

++++++++++++++++++++++++++++++
+Egy�b f�jlok, �s be�ll�t�sok+
++++++++++++++++++++++++++++++

login/inc/config
----------------
A telep�t�s el�tt sz�ks�ges be�ll�t�sok.
Miel�tt b�rmit tenn�l, �ll�tsd be az �sszes sz�ks�ges adatot a f�jlban

login/install.php
-----------------
A telep�t�st v�gz� f�jl.

login/moduls/admin.php.
----------------
Az admin fel�let.
Ezt a f�jlt illeszd be oda, ahol szeretn�d megjelen�teni az admin fel�letet.
pl.: <?php include_once("login/moduls/admin.php"); ?>

login/moduls/list.php
--------------
A user �s online lista.
Ezt a f�jlt illeszd be oda, ahol a userlist�t,
�s ahol az online list�t szeretn�d megjelen�teni.
onlinelista eset�n a r� mutat� link URL-j�t eg�szitsd ki a k�vetkez� m�don:

fajlod.php?list=o

Ez csak egy p�lda. A k�rd�jel el�tt a saj�t f�jlod �tvonala szerepeljen

login/moduls/newpass.php
------------------------
A jelsz� eml�keztet� f�jl. Illeszd be a m�r ismert m�don tetsz�leges helyre az oldaladon, ahol haszn�lni szeretn�d.
Itt t�rt�nik az �j jelsz� k�r�s, �s annak aktiv�l�sa is.

login/moduls/mh.php
------------
A megh�v� �rlap.
Ezt a f�jlt illeszd be arra az oldaladra, ahol a megh�v�s �rlapj�t szeretn�d megjelen�teni.
pl.: <?php include_once("login/moduls/mh.php");  ?>


login/moduls/uinfo.php
---------------
Az adatlap kit�lt�se.
Ezt a f�jlt illeszd be oda, ahol az adatlap kit�lt�s�t szeretn�d megjelen�teni
pl.: <?php include_once("login/moduls/uinfo.php"); ?>


login/moduls/uinfo_olv.php
-------------------
A tagok adatlapj�nak kiolvas�sa
Ezt a f�jlt illeszd be oda, ahol a userinf�t szeretn�d majd megjelen�teni

login/moduls/msg.php
--------------------
Ez a f�jl az �zenetk�ld�s modul. 2 helyre kell beillesztened.
1. Ahol az �zenetk�ld� �rlapot, valamint a list�z�st, �s olvas�st szeretn�d megjelen�teni,
2. �s oda, ahova az �zenetek linket szeretn�d helyezni

A program mag�t�l be�ll�tja, hogy mikor melyiket jelen�tse meg.
Neked csak a config.php -ban kell be�ll�tani annak a f�jlnak
az el�r�si �tj�t, amiben az �zenetk�ld�s van. M�shol csak a link jelenik meg


login/menuconfig/amenu.php
login/menuconfig/vmenu.php
login/menuconfig/umenu.php
------------
Ezek sorrendben az admin, vend�g, �s usermen�k. illeszd �ket tetsz�leges helyre.
pl.: <?php  include_once("login/menu/umenu.php"); ?>

Az egyes men�k tartalm�t be�ll�thatod a login/menu mapp�ban tal�lhat� f�jlokban.
Be�ll�that� hogy oszlopban, vagy sorban jelen�tse meg a men�ket.

A login rendszer be�ll�that� ny�lt regisztr�ci�ra, vagy megh�v�s alap�ra az admin fel�leten.
A regisztr�ci� le is tilthat� sz�ks�g eset�n.
Amennyiben a regisztr�ci� nyilt, megjelenik a regisztr�ci� link a bejelentkez�s �rlap alatt

Blokkolhat� a bel�p�s is.
T�r�lhet�k a userek,
jogok v�ltoztathat�k,
�s a megh�v�kat is az admin osztja ki, vagy veszi el.
Adminok v�gtelen sz�m� megh�v�val rendelkezhetnek, hisz maguknak �ll�thatj�k be
a megh�v�k sz�m�t.
Adminnak nem fogy a megh�v�ja a megh�v� elk�ld�sekor sem.

Alapbe�ll�t�sban a telep�t�s ut�n a regisztr�ci� nyilt, �gy az els� regisztr�l� tag admin jogot kap.
Ez ut�n letilthat�, �t�ll�that� a regisztr�ci� t�pusa is, vagy hagyhat� nyilt regisztr�ci�nak.

1.2 verzi�hoz k�pest �j�t�sok:
 - H�rk�ld�s: Az �zenetk�ld�sen bel�l, az adminok kiv�laszthatj�k,
   hogy minden tagnak c�mzik az �zenetet
 - E-mail aktiv�ci�: Minden regisztr�ci�, �s mail c�m v�ltoztat�s eset�n aktiv�lni kell
   az e-mail c�mre �rkez� linkkel az �j mailc�met
   (Ha 24 �r�n bel�l nincs aktiv�lva: �j regisztr�ci�kor a regisztr�ci� t�rl�dik,
                                      V�ltoztat�s eset�n vissza�ll az eredeti mailc�m )
 - Az e-mail c�m elrejthet� a k�z�ss�g el�l
 - �J jelsz�eml�keztet�: Az �j jelsz�t is aktiv�lni kell a mailben kapott linkkel.
 - Jav�tva az adatlap kit�lt�s, �s az �zenetk�ld�s
 - Valamint a telep�t�s is egyszer�s�d�tt
1.8 -hoz k�pesti v�ltoz�s:
 - Notice szitn� hib�k jav�t�s�t tartalmazza
 - H�rek t�rl�se hib�s volt. Ez is jav�tva
 - A telep�t�ben a felesleges pontosvessz�k t�r�lve az sql k�dokb�l. 
 - A t�bla karakterk�dol�sa most m�r latin2, �s nem latin1, mint kor�bban volt
 - Egy�b apr�bb jav�t�sok 


|----------------------------------------------------------|
|Megjegyz�s:                                               |
|A f�jlok el�r�si �tvonala att�l a f�jlt�l indul,          |
| ahova beilleszted a k�dokat.                             |
|A p�lda f�jlokat, melyek a login mapp�n k�v�l vannak,     |
|elt�vol�thatod. Csak a be�ll�t�shoz szolg�lnak seg�ts�g�l |
|K�rd�s eset�n �rjatok a f�rumba: http://www.phpstudio.hu  |
|----------------------------------------------------------|



