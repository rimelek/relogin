<?php
/**************************************************************************
* R.E. login (1.8.1) - uinfo.php                                          *
* ======================================================================= *
* Adatlap kitöltés                                                        *
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
class Uinfo extends Re_login
{
    //info változók
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
    
    //hiba tömb
    var $u_hiba      = array();
    
    //program elõkészítése
    function uinfo()
    {
        //szükésges sql adatok beállítása
        Re_login::re_adatok();
        $uinfo_sql   = "select * from re_data where id = '$this->userid' limit 1";
        $uinfo_query = mysql_query($uinfo_sql);
        $uinfo       = mysql_fetch_assoc($uinfo_query);
        $this->old_mail = $uinfo['mail'];

        //sql ben tárolt adatok vagy $_POST adatok

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
        //ha van hibaüzenet, azt is beállítja
        $this->set_errors();
    }
    //karakterek helyes beállítása (html kód levédése)
    function set_chars($string)
    {
        return trim($string);
    }
    //http:// nélkül is helyes a webcím
    function set_website($website)
    {
        $website = trim($website);
        $website = eregi_replace("http://", "", $website);
        $website = trim(str_replace($website, "http://$website", $website));
        return $website;
    }
    //kor mezõben csak számok lehetnek
    function check_kor()
    {
        $kor = trim($_POST['kor']);
        eregi("[0-9]+",$kor,$kimenet);
        
        if($kor != $kimenet[0]) return false;
        return true;
     }
    //a lehetséges hibaüzenetek beállítása
    function set_errors()
    {
        if(!$this->check_mail_msn($_POST['msn']))
            { $this->u_hiba[] = "Érvénytelen msn cím!"; }
        if(!$this->check_mail_msn($_POST['mail']))
            { $this->u_hiba[] = "Érvénytelen e-mail cím"; }
        if($this->old_mail != $_POST['mail']){
        if($this->tiltott_mail($_POST['mail']) and ($_POST['mail'] != ""))
            { $this->u_hiba[] = "Ez az e-mail cím foglalt!"; }
        }
        if($_POST['mail'] == "")
            { $this->u_hiba[] = "Adj meg egy e-mail címet!"; }
        if(!$this->check_kor())
            { $this->u_hiba[] = "A kor mezõ adatai hibásak"; }
        if(strlen($this->egyeb) > 600)
            { $this->u_hiba[] = "Az egyéb információk max 600 karakter lehet"; }
    }
    //adatbázisba visszük az adatokat
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
        
        print "<p align=\"center\">Sikeres adatlap módosítás!</p>";
    }

    
    //userinfó kitöltéséhez szükséges FORM
    function uinfo_form()
    {
        $select['x'] = "Nem tudom";
        $select['N'] = "Lány/Nõ";
        $select['F'] = "Fiú/Férfi";
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
            Vezetéknév:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"veznev\" maxlength=\"30\" size=\"20\" value=\"$this->veznev\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Keresztnév:
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
            Ország:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"orszag\" maxlength=\"50\" size=\"20\" value=\"$this->orszag\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Város:
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
            E-mail cím elrejtése:
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
            Msn cím:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"msn\" maxlength=\"100\" size=\"20\" value=\"$this->msn\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            Skype cím:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"skype\" maxlength=\"100\" size=\"20\" value=\"$this->skype\" />
            </td>
        </tr>
        <tr>
            <td class=\"cim\" colspan=\"2\" align=\"center\">
            Egyéb: (max 600 karakter)
            </td>
        </tr>
        <tr>
            <td class=\"input\" colspan=\"2\">
            <textarea name=\"egyeb\" maxlength=\"600\" cols=\"30\" rows=\"5\" >$this->egyeb</textarea>
            </td>
        </tr>
        <tr>
            <td colspan=\"2\" align=\"center\">
            <input type=\"submit\" name=\"elkuld\" value=\"Elküld\">
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

    //ha nincs hiba, adatbázisba ír
    if(count($uinfo->u_hiba) == 0)
    {
        $uinfo->u_beir();
        if($uinfo->mail != $uinfo->old_mail){
            $uinfo->mail_aktiv_kuld($_POST['mail'],$login->userid);
            print "
            <div align=\"center\" style=\"color: red; font-weight: bold;\">
            Biztonsági okokból meg kell erõsítened az e-mail címed!<br />
            Az aktivációs linket elküldtük a megadott címedre.<br />
            Amenyiben nem lépsz be újra az aktiválás után 24 órával sem,<br />
            az e-mail címed visszaáll a korábbira.
            </div>
            ";
        }
    //ha van hiba, kiirja a hibákat
    }else{
        print "<p align=\"center\"><font color=\"#ff0000\">";
        foreach($uinfo->u_hiba as $k => $error)
        {
            print $k + 1 .". $error<br />";
        }
        print "</font></p>";
    }
}
//infó kitöltés formja
print $uinfo->uinfo_form();


?>
