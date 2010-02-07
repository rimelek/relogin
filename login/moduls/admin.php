<?php

/**************************************************************************
* R.E. login (1.8.1) - admin.php                                          *
* ======================================================================= *
* Az adminisztrációs felület                                              *
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
if (!defined('__RELOGIN__'))
{
	exit('Ezt a fájlt nem érheted el közvetlenül.
		Be kell illesztened egy fájlba, amiben elõtte a config.php-t is beillesztetted már.
		Ahogy azt a readme.txt-ben is olvashatod.');
}

class Admin_obj extends Re_login
{
    var $action;
    var $reg_type;
    var $login_type;
    var $SA_ID;
    
    //Adatok beállítása
    function admin_obj()
    {
        global $super_admin;
        $this->action = $_SERVER['REQUEST_URI'];
        $this->re_adatok();

        $query = mysql_query("select id from re_login where user = '$super_admin'");
        $fetch = mysql_fetch_assoc($query);
        $this->SA_ID = $fetch['id'];
    }
    //jelenlegi regisztráció, és bejelentkezés típusa
    function rl_type()
    {
        $sql   = "select * from re_admin limit 1";
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);

        switch($fetch['reg_block'])
        {
            case "0": $this->reg_type = "nyitott";    break;
            case "1": $this->reg_type = "blokkolva";  break;
            case "2": $this->reg_type = "meghívásos"; break;
            default:  $this->reg_type = "ismeretlen"; break;
        }
        
        switch($fetch['login_block'])
        {
            case "0": $this->login_type = "nyitott";    break;
            case "1": $this->login_type = "blokkolva";  break;
            default:  $this->login_type = "ismeretlen"; break;
        }
    }

    //Admin ûrlap elõállítása
    function admin_form()
    {
        $form = "
        <table align=\"center\" border=\"1\" cellspacing=\"0\">
        <tr>
            <td class=\"acim\" align=\"center\" colspan=\"2\">
            <b><u>Meghívók kiosztása, és elvétele</u></b>
            </td>
        </tr>
        <tr>
            <form action=\"$this->action\" method=\"post\">
            <td class=\"aform1\">
            <input type=\"radio\" name=\"amh\" value=\"ad\" /> Ad<br />
            <input type=\"radio\" name=\"amh\" value=\"elvesz\" /> Elvesz<br />
            <input type=\"text\" name=\"darab\" size=\"1\" />db
            </td>
            <td class=\"aform2\" align=\"right\">
            Nick: <input type=\"text\" name=\"nev\" size=\"15\" /><br />
                  <input type=\"checkbox\" name=\"mind\" /> Mind?
            <input type=\"submit\" name=\"mhadmin\" value=\"Mehet\" />
            </td>
            </form>
        </tr>
        <tr>
            <td class=\"acim\" align=\"center\" colspan=\"2\">
            <b><u>Jogok változtatása, és tiltás</u></b>
            </td>
        </tr>
        <tr>
            <form action=\"$this->action\" method=\"post\">
            <td class=\"aform1\">
            <input type=\"radio\" name=\"ajog\" value=\"a\" /> Admin<br />
            <input type=\"radio\" name=\"ajog\" value=\"t\" /> Tag<br />
            <input type=\"radio\" name=\"ajog\" value=\"x\" /> Tilt
            </td>
            <td class=\"aform2\" align=\"right\">
            Nick: <input type=\"text\" name=\"nev\" size=\"15\" /><br />
            <input type=\"submit\" name=\"jogadmin\" value=\"Mehet\" />
            </td>
            </form>
        </tr>
        <tr>
            <td class=\"acim\" align=\"center\" colspan=\"2\">
            <b><u>Userek törlése</u></b>
            </td>
        </tr>
        <tr>
            <form action=\"$this->action\" method=\"post\">
            <td class=\"aform1\">&nbsp;
            </td>
            <td class=\"aform2\" align=\"right\">
            Nick: <input type=\"text\" name=\"nev\" size=\"15\" /><br />".
            (($this->SA_ID == $this->userid) ? "<input type=\"checkbox\" name=\"mind\" /> Mind?" : "").
            "<input type=\"submit\" name=\"toroladmin\" value=\"Mehet\" />
            </td>
            </form>
        </tr>
        <tr>
            <td class=\"acim\" align=\"center\" colspan=\"2\">
            <b><u>Regisztráció típusa</u></b>:  $this->reg_type
            </td>
        </tr>
        <tr>
            <form action=\"$this->action\" method=\"post\">
            <td class=\"aform1\">
            <input type=\"radio\" name=\"aregblock\" value=\"meghivas\" /> Meghívásos<br />
            <input type=\"radio\" name=\"aregblock\" value=\"block\" /> Blokkol<br />
            <input type=\"radio\" name=\"aregblock\" value=\"felold\" /> Felold
            </td>
            <td class=\"aform2\" align=\"center\">
            <input type=\"submit\" name=\"rblockadmin\" value=\"Mehet\" />
            </td>
            </form>
        </tr>
        <tr>
            <td class=\"acim\" align=\"center\" colspan=\"2\">
            <b><u>Belépés típusa</u></b>: $this->login_type
            </td>
        </tr>
        <tr>
            <form action=\"$this->action\" method=\"post\">
            <td class=\"aform1\">
            <input type=\"radio\" name=\"alepblock\" value=\"block\" /> Blokkol<br />
            <input type=\"radio\" name=\"alepblock\" value=\"felold\" /> Felold
            </td>
            <td class=\"aform2\" align=\"center\">
            <input type=\"submit\" name=\"bblockadmin\" value=\"Mehet\" />
            </td>
            </form>
        </tr>
        </table>
        ";
        return $form;
    }
    
    //meghívók adminisztrálása
    function mh_admin()
    {
		if(!isset($_POST['mhadmin'])) return;
        //A megfelelõ adatbázis kód elõállítása
        //Ha nem adtuk meg, hogy mi legyen a mûvelet
        if(empty($_POST['amh']) )
		{
			print "<p align=\"center\">Add meg a mûveletet!</p>";
			return;
		}

        $nev = trim(strtolower($_POST['nev']));
        $where = (empty($_POST['mind'])) ? " and lcase(user) = '$nev'" : "";
        $muvelet = ($_POST['amh'] == "ad") ? "+" : "-";
        $sql = "update re_login set mh = mh $muvelet '$_POST[darab]' where 1 $where";
		mysql_query($sql);
		mysql_query("update re_login set mh = 0 where mh < 0");
		print "<p align=\"center\">";
		//ha nem az összes tagra vonatkozik
		if(empty($_POST['mind']))
		{
			$sql2 = "select id from re_login where lcase(user) = '$nev' limit 1";
			$query2 = mysql_query($sql2);
			$fetch = mysql_fetch_assoc($query2);
			if(!$fetch) {print "Nincs ilyen név: \"$_POST[nev]\"";
			}else{
			print "Mûvelet: ($muvelet) $_POST[darab] meghívó $_POST[nev] részére";
			}
		}else{
			//ha az összes tagra vonatkozik
			print "Mûvelet: ($muvelet) $_POST[darab] meghívó minden tagnak";
		}
		print "</p>";
    }
    //jog változtatása
    function jog_admin()
    {
		if (!isset($_POST['jogadmin'])) return;
        $nev = trim(strtolower($_POST['nev']));
        $jogsql = "update re_login set jog = '$_POST[ajog]' where lcase(user) = '$nev'";
        //Mi legyen az új rang
        switch($_POST['ajog'])
        {
            case "a": $rang = "Admin";      break;
            case "t": $rang = "Tag";        break;
            case "x": $rang = "Tiltott";    break;
            default:  $rang = "ismeretlen"; break;
        }
        global $super_admin;
        print "<p align=\"center\">";
		//Ha nem adtuk meg az új rangot
		if(empty($_POST['ajog']))
		{
			print "Add meg a jogot!</p>";
			return;
		}
		//ha megadtuk a jogot, megvizsgáljuk, megadtuk-e a nevet
		$jogsql2 = "select id from re_login where lcase(user) = '$nev'";
		$query2  = mysql_query($jogsql2);
		$fetch2  = mysql_fetch_assoc($query2);
		//Ha nem adtuk meg a nevet
		if(!$fetch2)
		{
			print "Nincs ilyen név</p>";
			return;
		}
		//Ha megadtuk a nevet is, megnézzük, nem szuperadmin, vagy nem a saját nick
		//ha szuperadmin, vagy saját nick, kiírjuk
		if($nev == strtolower($_SESSION['usr'])
		or $nev == strtolower($super_admin))
		{
			print "Saját, vagy szuperadmin jogát nem változtathatod</p>";
			return;
		}
		//Ha minden stimmel, megadjuk az új jogot
		mysql_query($jogsql);
		print "$_POST[nev] új rangja $rang";
		print "</p>";
    }
    //Userek törlése
    function torol_admin()
    {
    	global $super_admin;
        if(isset($_POST['toroladmin']))
        {
			$nev = trim(strtolower($_POST['nev']));
            print "<p align=\"center\">";
            //Ha nem az összes tagot töröljük
            if(empty($_POST['mind']))
            {
                //Megnézzük, létezik-e a név
                $sql2   = "select id from re_login where lcase(user) = '$_POST[nev]'";
                $query2 = mysql_query($sql2);
                $fetch2 = mysql_fetch_assoc($query2);
                //Ha nics ilyen név, vagy nem adtuk meg
                if(!$fetch2)
                {
                    print "Nincs ilyen név</p>";
					
                }else{
                    //Ha létezik a név
                    $_GET['del'] = "true";
                    $_GET['id'] = $fetch2['id'];
					$qs = "";
                    foreach($_GET as $k => $v)
                    { $qs .= "$k=$v&"; }
                    $qs = rtrim($qs, "&");
                    //Visszakérdezünk, hogy biztosan törölni akarjuk-e
                    print "
                    <b>$_POST[nev]</b> nick törlésére készülsz!!<br />
                    Biztisan törlöd?
                    <a href=\"$_SERVER[REQUEST_URI]\">Nem</a> |
                    <a href=\"$_SERVER[PHP_SELF]?$qs\">Igen</a>
                    ";
                }
            }else{
                //Ha az összes nick törlésére készülünk
                $_GET['del'] = "true";
                $_GET['id'] = "ALL";
				$qs = "";
                foreach($_GET as $k => $v)
                { $qs .= "$k=$v&"; }
                $qs = rtrim($qs, "&");
                //Visszakérdezünk, hogy biztosan törljük-e mindegyik nicket
                print "
                Az <b>összes</b> nick törlésére készülsz!!!<br />
                Jól meggondoltad?
                <a href=\"$_SERVER[REQUEST_URI]\">Nem</a> |
                <a href=\"$_SERVER[PHP_SELF]?$qs\">Igen</a>
                ";
            }
            print "</p>";
        }else{
            $SAID = $this->SA_ID;
            //Ha minden tagot törlünk
            if(!empty($_GET['and']) and $_GET['id'] == "ALL" and $_GET['del'] == "true")
            {
                //szuper admint nem lehet törölni
                if($this->userid == $SAID){
                mysql_query("delete from re_login where id != '$SAID'");
                mysql_query("delete from re_data where id != '$SAID'");
                mysql_query("delete from re_meghiv");
                mysql_query("delete from re_msg"); print mysql_error();
                }
            }else{
                //Egy tag törlése
                if(!empty($_GET['id']) and $_GET['id'] != $SAID){
                mysql_query("delete from re_login where id = '$_GET[id]'");
                mysql_query("delete from re_msg where to_id = '$_GET[id]'");
                mysql_query("delete from re_data where id = '$_GET[id]'");
                }
                if(isset($_GET['id']) or isset($_GET['del']))
                {
                //Törlés után eltüntetjük a felesleges url parancsokat
                unset($_GET['id']);
                unset($_GET['del']);
                foreach($_GET as $k => $v)
                { $qs2 .= "$k=$v&"; }
                $qs2 = rtrim($qs2, "&");
                header("Location: $_SERVER[PHP_SELF]?$qs2");
                }
            }
        }
    }
    
    //Regisztráció típusának beállítása
    function reg_block()
    {
        if(isset($_POST['rblockadmin']))
        {
            print "<p align=\"center\">";
            if($_POST['aregblock'] != NULL)
            {
                //Ha leblokkoljuk a regisztrációt
                if($_POST['aregblock'] == "block")
                {
                    $block_sql = "update re_admin set reg_block = '1'";
                }elseif($_POST['aregblock'] == "felold"){
                    //Ha megnyitjuk a regisztrációt
                    $block_sql = "update re_admin set reg_block = '0'";
                }elseif($_POST['aregblock'] == "meghivas"){
                    //Ha a regisztrációt meghívás alapúra állítjuk
                    $block_sql = "update re_admin set reg_block = '2'";
                }
                mysql_query($block_sql);
                //Kiírjuk a megtörtént mûveletet
                if($_POST['aregblock'] == "block")   {print "Regisztráció blokkolva!";}
                if($_POST['aregblock'] == "felold")  {print "Regisztráció engedélyezve!";}
                if($_POST['aregblock'] == "meghivas"){print "Regisztráció csak meghívóval!";}
            }else{
                //Ha nem adtuk meg a mûveletet
                print "Add meg a mûveletet";
            }
            print "</p>";
        }
    }

    //Bejelentkezés blokkolása
    function login_block()
    {
        if(isset($_POST['bblockadmin']))
        {
            print "<p align=\"center\">";
            if($_POST['alepblock'] != NULL)
            {
                //Ha blokkoljuk a bejelentkezést
                if($_POST['alepblock'] == "block")
                {
                    $block_sql = "update re_admin set login_block = '1'";
                }elseif($_POST['alepblock'] == "felold"){
                    //Ha engedélyezzük a bejelentkezést
                    $block_sql = "update re_admin set login_block = '0'";
                }
                mysql_query($block_sql);
                //Kiírjuk a mûveletet
                if($_POST['alepblock'] == "block"){print "Belépés blokolva!";}
                if($_POST['alepblock'] == "felold"){print "Belépés engedélyezve";}
            }else{
                //Ha nem adtuk meg a mûveletet
                print "Blokk, vagy feloldás?";
            }
            print "</p>";
        }
    }
}

$admin_obj = new Admin_obj;

//Ha nincs admin joga a próbálkozónak,
//nem indul el a program
if($admin_obj->jog == "a")
{

    $admin_obj->mh_admin();
    $admin_obj->jog_admin();
    $admin_obj->torol_admin();
    $admin_obj->reg_block();
    $admin_obj->login_block();
    $admin_obj->rl_type();
    print $admin_obj->admin_form();
}else{
    print "<p align=\"center\">Ide csak admin léphet be!!!</p>";
}
?>
