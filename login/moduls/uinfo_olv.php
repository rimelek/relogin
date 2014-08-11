<?php
/**************************************************************************
* R.E. login (1.8.1) - uinfo_olv.php                                      *
* ======================================================================= *
* Adatlap olvas�sa                                                        *
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
class Uinfo_olv extends Re_login
{

    var $info_array = array();
    var $uid;

    function uinfo_olv()
    {
        eregi("[0-9]+", $_GET['uid'], $gets);
        //ha nem sz�m a $_GET['s'] akkor �tir�nyit�s
        if($_GET['uid'] != $gets[0] or $_GET['uid'] == NULL)
        {
            $_GET['uid'] = "0";
            foreach($_GET as $k => $v) { $qs .= $k."=".$v."&"; }
            header("Location: $_SERVER[PHP_SELF]?$qs");
        }
        $this->uid = $_GET['uid'];
        $this->re_adatok();
    }
    //Be�ll�tjuk a tag nem�t
    function set_nem($nem)
    {
        switch($nem)
        {
            case "F": $nem = "Fi�/F�rfi";  break;
            case "N": $nem = "L�ny/N�";    break;
            case "x": $nem = "Nem tudja";  break;
            default:  $nem = "Ismeretlen"; break;
        }
        return $nem;
    }

    //Az id� form�tum be�ll�t�sa
    function set_times($times)
    {
        if($times < mktime(1,1,1,1,1,2006)) return "---";
        return date("Y.m.d - H:i:s", $times);
    }
    //A b�ng�sz� adatainak form�z�sa
    function set_browser($browser)
    {
        $type = substr($browser, 0, strpos($browser, "(") - 1 );
        $pos = strpos($browser, "(") + 1;
        $egyeb = substr($browser, strpos($browser, "(") + 1, strpos($browser, ")") - ($pos) );


        $brows['type'] = $type;
        $brows['egyeb']   = $egyeb;
        return $brows;
    }
    //Kisz�moljuik az online id�t (nap, �ra, perc, m�sodperc)
    function set_online($online)
    {
        $percszor = 60;
        $oraszor  = 60 * $percszor;
        $napszor  = 24 * $oraszor;

        
        $nap     = floor( $online / $napszor );
        $ora_x   = $nap * $napszor;
        $ora     = floor(($online - $ora_x) / $oraszor);
        $perc_x  = $ora_x + ( $ora * $oraszor );
        $perc    = floor( ($online -  $perc_x) / $percszor);
        $mperc_x = $perc_x + ($perc * $percszor);
        $mperc   = $online - $mperc_x;

        $online  = array(
                  "nap"   => $nap,
                  "ora"   => $ora,
                  "perc"  => $perc,
                  "mperc" => $mperc
                  );
        return $online;
    }

    //Az adatlap kiolvas�sa
    function kiolv()
    {
        $uinf_sql         = "
        select * from re_login, re_data
        where re_login.id = re_data.id and re_login.id = '$this->uid' limit 1";

        $uinf_query       = mysql_query($uinf_sql) or die(mysql_error());
        $uinf             = mysql_fetch_assoc($uinf_query);
        //visszaadjuk az adatokat egy t�mbben
        $this->info_array = $uinf;
    }

    //Inf�k ki�r�sa a visszaadott t�mb alapj�n
    function info_kiir()
    {

        $info = $this->info_array;
        //Az inf�k helyes be�ll�t�sa
        $info['nem']   = $this->set_nem($info['nem']);
        $info['veznev'] = htmlspecialchars($info['veznev']);
        $info['kernev'] = htmlspecialchars($info['kernev']);
        $info['egyeb'] = htmlspecialchars($info['egyeb']);
        $infos = explode("\n",$info['egyeb']);
        foreach($infos as $key => $line)
        {
            $infos[$key] = wordwrap($line,50,"\n");
        }
        $info['egyeb'] = implode("\n",$infos);

        $info['egyeb'] = '<pre><div style="width: 350; max-height: 400; overflow: scroll; font-family: Verdana,Arial; font-size: 10pt;">'.$info['egyeb'].'</div></pre>';
        
        $info['status']    = ($info['status'] == "1") ? "Online" : "Offline";
        $info['regido']    = $this->set_times($info['regido']);
        $info['belepes']   = $this->set_times($info['belepes']);
        $info['frissites'] = $this->set_times($info['frissites']);
        $info['online']    = $this->set_online($info['online']);
        $info['website']   = "<a href=\"$info[website]\">$info[website]</a>";
        $info['mail']      = "<a href=\"mailto:$info[mail]\">$info[mail]</a>";
        if($info['public_mail'] != "1" and $this->jog != "a")
        { $info['mail'] = "Nem publikus"; }
        $info['browser']   = $this->set_browser($info['browser']);
        if($info['mh'] == NULL) { $info['mh'] = 0; }
        
        switch($info['jog'])
        {
            case "a": $jog = "Admin";   break;
            case "t": $jog = "Tag";     break;
            case "x": $jog = "Tiltott"; break;
            default:  $jog = "?????";   break;
        }
        
        $nap   = $info['online']['nap'];
        $ora   = $info['online']['ora'];
        $perc  = $info['online']['perc'];
        $mperc = $info['online']['mperc'];

        $browser_type = $info['browser']['type'];
        $browser_op = $info['browser']['egyeb'];
        //Ha nincs adat, helyette sz�k�z karaktert tesz�nk be
        function nbspx($string) {
			if (is_array($string))
			{
				array_walk($string,'nbspx');
				return $string;
			}
        	return (strlen(trim($string)) > 0) ? $string : $string."&nbsp;" ; 
        }
        $info = array_map("nbspx",$info);

        //Ezeket az adatokat csak admin l�thatja
        $admin_table = "
        <tr>
            <td align=\"center\" colspan=\"2\">
            <b>Adminoknak</b>
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            IP c�m:
            </td>
            <td class=\"uinf\">
            $info[ip]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Egy�b inf�:
            </td>
            <td class=\"uinf\">
            $browser_op
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Megh�v�i
            </td>
            <td class=\"uinf\">
            $info[mh] darab
            </td>
        </tr>
        ";
        //ha nem admin a tag, t�r�lj�k az admin_table tartalm�t
        if($this->jog != "a") { $admin_table = ""; }
        //It ki�rjuk az adatokat egy t�bl�zatban
        $inf_table = "<p align=\"center\"><big>".trim($info['user'])." inf�j�t n�zed</big></p>";
        $inf_table .= "
        <table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\" width=\"300\">
        <tr>
            <td class=\"ucim\">
            N�v:
            </td>
            <td class=\"uinf\">
            $info[veznev] $info[kernev]
            </td>
        </tr>
        <tr>
            <td align=\"center\" colspan=\"2\">
            <b>Szem�lyes inf�ja</b>
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Neme:
            </td>
            <td class=\"uinf\">
            $info[nem]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Kor:
            </td>
            <td class=\"uinf\">
            $info[kor] �ves
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Orsz�g:
            </td>
            <td class=\"uinf\">
            $info[orszag]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            V�ros:
            </td>
            <td class=\"uinf\">
            $info[varos]
            </td>
        </tr>
        <tr>
            <td align=\"center\" colspan=\"2\">
            <b>El�rhet�s�gei</b>
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Weboldala:
            </td>
            <td class=\"uinf\">
            $info[website]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            E-mail c�me:
            </td>
            <td class=\"uinf\">
            $info[mail]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Msn c�me:
            </td>
            <td class=\"uinf\">
            $info[msn]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Skype azono�t�
            </td>
            <td class=\"uinf\">
            $info[skype]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\" align=\"center\" colspan=\"2\">
            <b>Egy�b Megjegyz�sei:</b>
            </td>
        </tr>
        <tr>
            <td class=\"uinf\" colspan=\"2\">
            $info[egyeb]
            </td>
        </tr>
        <tr>
            <td align=\"center\" colspan=\"2\">
            <b>Technikai inf�k</b>
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Joga:
            </td>
            <td class=\"uinf\">
            $jog
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            St�tusz:
            </td>
            <td class=\"uinf\">
            $info[status]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Regisztr�lt:
            </td>
            <td class=\"uinf\">
            $info[regido]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Bel�pett:
            </td>
            <td class=\"uinf\">
            $info[belepes]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Friss�tett:
            </td>
            <td class=\"uinf\">
            $info[frissites]
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            Online ideje:
            </td>
            <td class=\"uinf\">
            $nap nap - $ora �ra - $perc perc
            </td>
        </tr>
        <tr>
            <td class=\"ucim\">
            B�ng�sz�je:
            </td>
            <td class=\"uinf\">
            $browser_type
            </td>
        </tr>
        $admin_table
        </table>";
        
        if($info['id'] == NULL)
        {
            $inf_table = "<p align=\"center\">Nincs ilyen felhaszn�l�</p>";
        }
        
        return $inf_table;
    }
}

$uinf_olv = new Uinfo_olv();

$uinf_olv->kiolv();
print $uinf_olv->info_kiir();

print "
<p align=\"center\">
<a href=\"javascript:history.go(-1)\">Vissza</a>
</p>";

?>
