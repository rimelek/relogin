<?php

/**************************************************************************
* R.E. login (1.8.1) - admin.php                                          *
* ======================================================================= *
* Az adminisztr�ci�s fel�let                                              *
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
if (!defined('__RELOGIN__'))
{
	exit('Ezt a f�jlt nem �rheted el k�zvetlen�l.
		Be kell illesztened egy f�jlba, amiben el�tte a config.php-t is beillesztetted m�r.
		Ahogy azt a readme.txt-ben is olvashatod.');
}

class Admin_obj extends Re_login
{
    var $action;
    var $reg_type;
    var $login_type;
    var $SA_ID;
    
    //Adatok be�ll�t�sa
    function admin_obj()
    {
        global $super_admin;
        $this->action = $_SERVER['REQUEST_URI'];
        $this->re_adatok();

        $query = mysql_query("select id from re_login where user = '$super_admin'");
        $fetch = mysql_fetch_assoc($query);
        $this->SA_ID = $fetch['id'];
    }
    //jelenlegi regisztr�ci�, �s bejelentkez�s t�pusa
    function rl_type()
    {
        $sql   = "select * from re_admin limit 1";
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);

        switch($fetch['reg_block'])
        {
            case "0": $this->reg_type = "nyitott";    break;
            case "1": $this->reg_type = "blokkolva";  break;
            case "2": $this->reg_type = "megh�v�sos"; break;
            default:  $this->reg_type = "ismeretlen"; break;
        }
        
        switch($fetch['login_block'])
        {
            case "0": $this->login_type = "nyitott";    break;
            case "1": $this->login_type = "blokkolva";  break;
            default:  $this->login_type = "ismeretlen"; break;
        }
    }

    //Admin �rlap el��ll�t�sa
    function admin_form()
    {
        $form = "
        <table align=\"center\" border=\"1\" cellspacing=\"0\">
        <tr>
            <td class=\"acim\" align=\"center\" colspan=\"2\">
            <b><u>Megh�v�k kioszt�sa, �s elv�tele</u></b>
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
            <b><u>Jogok v�ltoztat�sa, �s tilt�s</u></b>
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
            <b><u>Userek t�rl�se</u></b>
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
            <b><u>Regisztr�ci� t�pusa</u></b>:  $this->reg_type
            </td>
        </tr>
        <tr>
            <form action=\"$this->action\" method=\"post\">
            <td class=\"aform1\">
            <input type=\"radio\" name=\"aregblock\" value=\"meghivas\" /> Megh�v�sos<br />
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
            <b><u>Bel�p�s t�pusa</u></b>: $this->login_type
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
    
    //megh�v�k adminisztr�l�sa
    function mh_admin()
    {
		if(!isset($_POST['mhadmin'])) return;
        //A megfelel� adatb�zis k�d el��ll�t�sa
        //Ha nem adtuk meg, hogy mi legyen a m�velet
        if(empty($_POST['amh']) )
		{
			print "<p align=\"center\">Add meg a m�veletet!</p>";
			return;
		}

        $nev = trim(strtolower($_POST['nev']));
        $where = (empty($_POST['mind'])) ? " and lcase(user) = '$nev'" : "";
        $muvelet = ($_POST['amh'] == "ad") ? "+" : "-";
        $sql = "update re_login set mh = mh $muvelet '$_POST[darab]' where 1 $where";
		mysql_query($sql);
		mysql_query("update re_login set mh = 0 where mh < 0");
		print "<p align=\"center\">";
		//ha nem az �sszes tagra vonatkozik
		if(empty($_POST['mind']))
		{
			$sql2 = "select id from re_login where lcase(user) = '$nev' limit 1";
			$query2 = mysql_query($sql2);
			$fetch = mysql_fetch_assoc($query2);
			if(!$fetch) {print "Nincs ilyen n�v: \"$_POST[nev]\"";
			}else{
			print "M�velet: ($muvelet) $_POST[darab] megh�v� $_POST[nev] r�sz�re";
			}
		}else{
			//ha az �sszes tagra vonatkozik
			print "M�velet: ($muvelet) $_POST[darab] megh�v� minden tagnak";
		}
		print "</p>";
    }
    //jog v�ltoztat�sa
    function jog_admin()
    {
		if (!isset($_POST['jogadmin'])) return;
        $nev = trim(strtolower($_POST['nev']));
        $jogsql = "update re_login set jog = '$_POST[ajog]' where lcase(user) = '$nev'";
        //Mi legyen az �j rang
        switch($_POST['ajog'])
        {
            case "a": $rang = "Admin";      break;
            case "t": $rang = "Tag";        break;
            case "x": $rang = "Tiltott";    break;
            default:  $rang = "ismeretlen"; break;
        }
        global $super_admin;
        print "<p align=\"center\">";
		//Ha nem adtuk meg az �j rangot
		if(empty($_POST['ajog']))
		{
			print "Add meg a jogot!</p>";
			return;
		}
		//ha megadtuk a jogot, megvizsg�ljuk, megadtuk-e a nevet
		$jogsql2 = "select id from re_login where lcase(user) = '$nev'";
		$query2  = mysql_query($jogsql2);
		$fetch2  = mysql_fetch_assoc($query2);
		//Ha nem adtuk meg a nevet
		if(!$fetch2)
		{
			print "Nincs ilyen n�v</p>";
			return;
		}
		//Ha megadtuk a nevet is, megn�zz�k, nem szuperadmin, vagy nem a saj�t nick
		//ha szuperadmin, vagy saj�t nick, ki�rjuk
		if($nev == strtolower($_SESSION['usr'])
		or $nev == strtolower($super_admin))
		{
			print "Saj�t, vagy szuperadmin jog�t nem v�ltoztathatod</p>";
			return;
		}
		//Ha minden stimmel, megadjuk az �j jogot
		mysql_query($jogsql);
		print "$_POST[nev] �j rangja $rang";
		print "</p>";
    }
    //Userek t�rl�se
    function torol_admin()
    {
    	global $super_admin;
        if(isset($_POST['toroladmin']))
        {
			$nev = trim(strtolower($_POST['nev']));
            print "<p align=\"center\">";
            //Ha nem az �sszes tagot t�r�lj�k
            if(empty($_POST['mind']))
            {
                //Megn�zz�k, l�tezik-e a n�v
                $sql2   = "select id from re_login where lcase(user) = '$_POST[nev]'";
                $query2 = mysql_query($sql2);
                $fetch2 = mysql_fetch_assoc($query2);
                //Ha nics ilyen n�v, vagy nem adtuk meg
                if(!$fetch2)
                {
                    print "Nincs ilyen n�v</p>";
					
                }else{
                    //Ha l�tezik a n�v
                    $_GET['del'] = "true";
                    $_GET['id'] = $fetch2['id'];
					$qs = "";
                    foreach($_GET as $k => $v)
                    { $qs .= "$k=$v&"; }
                    $qs = rtrim($qs, "&");
                    //Visszak�rdez�nk, hogy biztosan t�r�lni akarjuk-e
                    print "
                    <b>$_POST[nev]</b> nick t�rl�s�re k�sz�lsz!!<br />
                    Biztisan t�rl�d?
                    <a href=\"$_SERVER[REQUEST_URI]\">Nem</a> |
                    <a href=\"$_SERVER[PHP_SELF]?$qs\">Igen</a>
                    ";
                }
            }else{
                //Ha az �sszes nick t�rl�s�re k�sz�l�nk
                $_GET['del'] = "true";
                $_GET['id'] = "ALL";
				$qs = "";
                foreach($_GET as $k => $v)
                { $qs .= "$k=$v&"; }
                $qs = rtrim($qs, "&");
                //Visszak�rdez�nk, hogy biztosan t�rlj�k-e mindegyik nicket
                print "
                Az <b>�sszes</b> nick t�rl�s�re k�sz�lsz!!!<br />
                J�l meggondoltad?
                <a href=\"$_SERVER[REQUEST_URI]\">Nem</a> |
                <a href=\"$_SERVER[PHP_SELF]?$qs\">Igen</a>
                ";
            }
            print "</p>";
        }else{
            $SAID = $this->SA_ID;
            //Ha minden tagot t�rl�nk
            if(!empty($_GET['and']) and $_GET['id'] == "ALL" and $_GET['del'] == "true")
            {
                //szuper admint nem lehet t�r�lni
                if($this->userid == $SAID){
                mysql_query("delete from re_login where id != '$SAID'");
                mysql_query("delete from re_data where id != '$SAID'");
                mysql_query("delete from re_meghiv");
                mysql_query("delete from re_msg"); print mysql_error();
                }
            }else{
                //Egy tag t�rl�se
                if(!empty($_GET['id']) and $_GET['id'] != $SAID){
                mysql_query("delete from re_login where id = '$_GET[id]'");
                mysql_query("delete from re_msg where to_id = '$_GET[id]'");
                mysql_query("delete from re_data where id = '$_GET[id]'");
                }
                if(isset($_GET['id']) or isset($_GET['del']))
                {
                //T�rl�s ut�n elt�ntetj�k a felesleges url parancsokat
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
    
    //Regisztr�ci� t�pus�nak be�ll�t�sa
    function reg_block()
    {
        if(isset($_POST['rblockadmin']))
        {
            print "<p align=\"center\">";
            if($_POST['aregblock'] != NULL)
            {
                //Ha leblokkoljuk a regisztr�ci�t
                if($_POST['aregblock'] == "block")
                {
                    $block_sql = "update re_admin set reg_block = '1'";
                }elseif($_POST['aregblock'] == "felold"){
                    //Ha megnyitjuk a regisztr�ci�t
                    $block_sql = "update re_admin set reg_block = '0'";
                }elseif($_POST['aregblock'] == "meghivas"){
                    //Ha a regisztr�ci�t megh�v�s alap�ra �ll�tjuk
                    $block_sql = "update re_admin set reg_block = '2'";
                }
                mysql_query($block_sql);
                //Ki�rjuk a megt�rt�nt m�veletet
                if($_POST['aregblock'] == "block")   {print "Regisztr�ci� blokkolva!";}
                if($_POST['aregblock'] == "felold")  {print "Regisztr�ci� enged�lyezve!";}
                if($_POST['aregblock'] == "meghivas"){print "Regisztr�ci� csak megh�v�val!";}
            }else{
                //Ha nem adtuk meg a m�veletet
                print "Add meg a m�veletet";
            }
            print "</p>";
        }
    }

    //Bejelentkez�s blokkol�sa
    function login_block()
    {
        if(isset($_POST['bblockadmin']))
        {
            print "<p align=\"center\">";
            if($_POST['alepblock'] != NULL)
            {
                //Ha blokkoljuk a bejelentkez�st
                if($_POST['alepblock'] == "block")
                {
                    $block_sql = "update re_admin set login_block = '1'";
                }elseif($_POST['alepblock'] == "felold"){
                    //Ha enged�lyezz�k a bejelentkez�st
                    $block_sql = "update re_admin set login_block = '0'";
                }
                mysql_query($block_sql);
                //Ki�rjuk a m�veletet
                if($_POST['alepblock'] == "block"){print "Bel�p�s blokolva!";}
                if($_POST['alepblock'] == "felold"){print "Bel�p�s enged�lyezve";}
            }else{
                //Ha nem adtuk meg a m�veletet
                print "Blokk, vagy felold�s?";
            }
            print "</p>";
        }
    }
}

$admin_obj = new Admin_obj;

//Ha nincs admin joga a pr�b�lkoz�nak,
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
    print "<p align=\"center\">Ide csak admin l�phet be!!!</p>";
}
?>
