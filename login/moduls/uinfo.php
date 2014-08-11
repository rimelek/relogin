<?php
/**************************************************************************
* R.E. login (1.8.1) - uinfo.php                                          *
* ======================================================================= *
* Adatlap kit�lt�s                                                        *
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
class Uinfo extends Re_login
{
    //info v�ltoz�k
    var $veznev;
    var $kernev;
    var $nem;
    var $kor;
    var $orszag;
    var $varos;
    var $mail;
    var $old_mail;
    var $website;
    var $msn;
    var $skype;
    var $egyeb;
    
    //hiba t�mb
    var $u_hiba      = array();
    
    //program el�k�sz�t�se
    function uinfo()
    {
        //sz�k�sges sql adatok be�ll�t�sa
        Re_login::re_adatok();
        $uinfo_sql   = "select * from re_data where id = '$this->userid' limit 1";
        $uinfo_query = mysql_query($uinfo_sql);
        $uinfo       = mysql_fetch_assoc($uinfo_query);
        $this->old_mail = $uinfo['mail'];

        //sql ben t�rolt adatok vagy $_POST adatok

        $tomb = array("veznev","kernev","nem","kor","orszag","varos","mail","public_mail","website","msn","skype","egyeb");
        foreach($tomb as $var)
        {
            if(isset($_POST['elkuld'])){
                $this->$var = htmlspecialchars(stripslashes($_POST[$var]));
            } else {
                $this->$var = htmlspecialchars($uinfo[$var]);
            }
        }
		if (!isset($_POST['elkuld'])) return;
        //ha van hiba�zenet, azt is be�ll�tja
        $this->set_errors();
    }
    //karakterek helyes be�ll�t�sa (html k�d lev�d�se)
    function set_chars($string)
    {
        return trim($string);
    }
    //http:// n�lk�l is helyes a webc�m
    function set_website($website)
    {
        $website = trim($website);
        $website = eregi_replace("http://", "", $website);
        $website = trim(str_replace($website, "http://$website", $website));
        return $website;
    }
    //kor mez�ben csak sz�mok lehetnek
    function check_kor()
    {
        $kor = trim($_POST['kor']);
        eregi("[0-9]+",$kor,$kimenet);
        
        if($kor != $kimenet[0]) return false;
        return true;
     }
    //a lehets�ges hiba�zenetek be�ll�t�sa
    function set_errors()
    {
        if(!$this->check_mail_msn($_POST['msn']))
            { $this->u_hiba[] = "�rv�nytelen msn c�m!"; }
        if(!$this->check_mail_msn($_POST['mail']))
            { $this->u_hiba[] = "�rv�nytelen e-mail c�m"; }
        if($this->old_mail != $_POST['mail']){
        if($this->tiltott_mail($_POST['mail']) and ($_POST['mail'] != ""))
            { $this->u_hiba[] = "Ez az e-mail c�m foglalt!"; }
        }
        if($_POST['mail'] == "")
            { $this->u_hiba[] = "Adj meg egy e-mail c�met!"; }
        if(!$this->check_kor())
            { $this->u_hiba[] = "A kor mez� adatai hib�sak"; }
        if(strlen($this->egyeb) > 600)
            { $this->u_hiba[] = "Az egy�b inform�ci�k max 600 karakter lehet"; }
    }
    //adatb�zisba vissz�k az adatokat
    function u_beir()
    {
        $veznev  = $this->set_chars($_POST['veznev']);
        $kernev  = $this->set_chars($_POST['kernev']);
        $orszag  = $this->set_chars($_POST['orszag']);
        $varos   = $this->set_chars($_POST['varos']);
        $skype   = $this->set_chars($_POST['skype']);
        $egyeb   = $this->set_chars($_POST['egyeb']);
        $website = $this->set_website($this->website);
        $mail    = trim($this->mail);
        $msn     = trim($this->msn);

        $u_beir_sql = "
        update re_data set
            veznev      = '$veznev',
            kernev      = '$kernev',
            orszag      = '$orszag',
            varos       = '$varos',
            skype       = '$skype',
            website     = '$website',
            uj_mail     = '$this->mail',
            public_mail = '$this->public_mail',
            msn         = '$this->msn',
            nem         = '$this->nem',
            kor         = '$this->kor',
            egyeb       = '$egyeb'
        where id = '$this->userid'
        ";
        mysql_query($u_beir_sql);
        
        print "<p align=\"center\">Sikeres adatlap m�dos�t�s!</p>";
    }

    
    //userinf� kit�lt�s�hez sz�ks�ges FORM
    function uinfo_form()
    {
        $select['x'] = "Nem tudom";
        $select['N'] = "L�ny/N�";
        $select['F'] = "Fi�/F�rfi";
        if($this->public_mail != 1){
            $status0 = " checked=\"checked\"";
            $status1 = "";
        }else{
            $status0 = "";
            $status1 = " checked=\"checked\"";
        }

        $uinfo_form = "
        <form action=\"$_SERVER[REQUEST_URI]\" method=\"POST\">
        <table align=\"center\" celpadding=\"0\" cellspacing=\"0\">

        <tr>
            <td class=\"cim\">
            Vezet�kn�v:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"veznev\" maxlength=\"30\" size=\"20\" value=\"$this->veznev\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Keresztn�v:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"kernev\" maxlength=\"30\" size=\"20\" value=\"$this->kernev\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Nem:
            </td>
            <td class=\"input\">
            <select name=\"nem\">";

            foreach($select as $value => $kiir)
            {
                $uinfo_form .= "<option value=\"$value\"";
                if($value == $this->nem) { $uinfo_form .= " selected"; }
                $uinfo_form .=  ">$kiir</option>";
            }

            $uinfo_form .= "</select>
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Kor:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"kor\" maxlength=\"3\" size=\"3\" value=\"$this->kor\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Orsz�g:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"orszag\" maxlength=\"50\" size=\"20\" value=\"$this->orszag\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            V�ros:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"varos\" maxlength=\"50\" size=\"20\" value=\"$this->varos\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            E-mail:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"mail\" maxlength=\"100\" size=\"20\" value=\"$this->mail\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            E-mail c�m elrejt�se:
            </td>
            <td class=\"input\">
            <input type=\"radio\" name=\"public_mail\" value=\"0\" $status0 /> Elrejt
            <input type=\"radio\" name=\"public_mail\" value=\"1\" $status1 /> Publikus
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Weboldal:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"website\" maxlength=\"100\" size=\"20\" value=\"$this->website\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Msn c�m:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"msn\" maxlength=\"100\" size=\"20\" value=\"$this->msn\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Skype c�m:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"skype\" maxlength=\"100\" size=\"20\" value=\"$this->skype\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\" colspan=\"2\" align=\"center\">
            Egy�b: (max 600 karakter)
            </td>
        </tr>
        <tr>
            <td class=\"input\" colspan=\"2\">
            <textarea name=\"egyeb\" maxlength=\"600\" cols=\"30\" rows=\"5\" >$this->egyeb</textarea>
            </td>
        </tr>
        <tr>
            <td colspan=\"2\" align=\"center\">
            <input type=\"submit\" name=\"elkuld\" value=\"Elk�ld\">
            </td>
        </tr>
        </table>
        </form>
        ";
        return $uinfo_form;
    }
}
$uinfo = new Uinfo;
if(isset($_POST['elkuld'])){

    //ha nincs hiba, adatb�zisba �r
    if(count($uinfo->u_hiba) == 0)
    {
        $uinfo->u_beir();
        if($uinfo->mail != $uinfo->old_mail){
            $uinfo->mail_aktiv_kuld($_POST['mail'],$login->userid);
            print "
            <div align=\"center\" style=\"color: red; font-weight: bold;\">
            Biztons�gi okokb�l meg kell er�s�tened az e-mail c�med!<br />
            Az aktiv�ci�s linket elk�ldt�k a megadott c�medre.<br />
            Amenyiben nem l�psz be �jra az aktiv�l�s ut�n 24 �r�val sem,<br />
            az e-mail c�med vissza�ll a kor�bbira.
            </div>
            ";
        }
    //ha van hiba, kiirja a hib�kat
    }else{
        print "<p align=\"center\"><font color=\"#ff0000\">";
        foreach($uinfo->u_hiba as $k => $error)
        {
            print $k + 1 .". $error<br />";
        }
        print "</font></p>";
    }
}
//inf� kit�lt�s formja
print $uinfo->uinfo_form();


?>
