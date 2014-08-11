<?php

/**************************************************************************
* R.E. login (1.8.1) - pm_read.php                                        *
* ======================================================================= *
* �zenetek kiolvas�sa                                                     *
* ======================================================================= *
* Ezt a programot a PHP Studio fejlesztette, a szerz�k: / This program    *
* was developed by the PHP Studio, the authors:                           *
* Rimelek                                                                 *
* ----------------------------------------------------------------------- *
* Weboldalunk / Our webpage: http://www.phpstudio.hu                      *
* Seg�ts�gny�jt�s / HelpDesk: http://forum.phpstudio.hu                   *
* Kapcsolat / Contact: akoss@citromail.hu                                 *
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
class Pm_read extends Re_msg
{
    //�jra be�ll�tjuk a user adatokat
    function pm_read()
    {
        $this->re_adatok();
    }
    //�zenet megjelen�t�se
    function read_message()
    {
        global $uinfo_olv;
        //kiolvassuk az �zenetet
        $uzenet = $this->pm_sql($_GET['sid']);
		if (!$uzenet) return;
        //k�ld� nev�nek be�ll�t�sa
        $kuldo = $this->msg_from($uzenet['from_id']);
        //d�tumform�tum be�ll�t�sa
        $datum = date("Y.m.d H:i:s", $uzenet['date']);
        //t�ma form�z�sa
        $uzenet['subject'] = htmlspecialchars($uzenet['subject']);
        //az �zenet form�z�sa
        $uzenet['message'] = htmlspecialchars($uzenet['message']);
        $uzenetek = explode("\n",$uzenet['message']);
        foreach($uzenetek as $key => $line)
        {
            $uzenetek[$key] = wordwrap($line,70,"\n");
        }
        $uzenet['message'] = implode("\n",$uzenetek);
        $uzenet['message'] = '<pre><div style="overflow: scroll; font-family: Verdana,Arial; font-size: 10pt;">'.$uzenet['message'].'</div></pre>';

        //a v�lasz link
        $valasz = $this->msg_mylink("msg");
        $valasz = $this->msg_mylink("re", NULL,$uzenet['msg_id']);
        $valasz = $this->msg_mylink("fw");
        $valasz = $this->msg_mylink("msgdel");
        $valasz = $this->msg_mylink("uid");
        
        //a tov�bb k�ld�s link
        $tovabb = $this->msg_mylink("msg");
        $tovabb = $this->msg_mylink("fw", NULL,$uzenet['msg_id']);
        $tovabb = $this->msg_mylink("re");
        $tovabb = $this->msg_mylink("msgdel");
        $tovabb = $this->msg_mylink("uid");

        //a kuld� link
        $kuldo_link = $this->msg_mylink("msg");
        $kuldo_link = $this->msg_mylink("re");
        $kuldo_link = $this->msg_mylink("fw");
        $kuldo_link = $this->msg_mylink("msgdel");
        $kuldo_link = $this->msg_mylink("uid", $uinfo_olv, $uzenet['from_id']);

        //a t�rl�s link
        $torol = $this->msg_mylink("msg", NULL, "read");
        $torol = $this->msg_mylink("fw");
        $torol = $this->msg_mylink("re");
        $torol = $this->msg_mylink("uid");
        $torol = $this->msg_mylink("sid", NULL, $uzenet['msg_id']);
        $torol = $this->msg_mylink("msgdel", NULL, $uzenet['msg_id']);
        
        $kuldo = "<a href=\"$kuldo_link\">$kuldo</a>";
        
        $form = "
        <table width=\"500\" align=\"center\" border=\"1\" cellspacing=\"1\">\n
        <tr>
            <td>
            <b>K�ld�:</b> $kuldo
            </td>

            <td>
            <b>D�tum</b> $datum
            </td>
        </tr>
        <tr>
            <td>
            <b>T�rgy</b>
            </td>

            <td class=\"pm_text\">
            $uzenet[subject]
            </td>
        </tr>
        <tr>
            <td colspan=\"2\">
            $uzenet[message]
            </td>
        </tr>";
        if($_GET['msg'] == "read" or $this->jog == "a"){
        $form .= "
        <tr>
            <td colspan=\"2\">
            <table border=\"0\"><tr>
            <td><a href=\"$valasz\">V�lasz</a>&nbsp;&nbsp;<td>
            <td><a href=\"$tovabb\">Tov�bb k�ld</a>&nbsp;&nbsp;</td>
            <td><a href=\"$torol\">T�r�l</a></td>

            </tr>
            </table>
            </td>
        </tr>
        ";
        }
        $form .= "</table>";
        //ha az �zenet c�mzetje a user
        if($_GET['msg'] == "read") { $list_id = $this->userid; }
        elseif($_GET['msg'] == "news_read") { $list_id = 0; }
        if($uzenet['to_id'] == $list_id)
        {
            //olvasottnak jel�lj�k
            $this->olvasva($uzenet['msg_id']);
        }else{
            //de ha nem � a c�mzett hiba�zenetet �runk ki
            $form .= "
            <p align=\"center\">Nem vagy jogosult ennek az �zenetnek a megtekint�s�re</p>
            ";
        }
        //ha t�rl�si k�relem volt, nem irjuk ki az �zenetet
        if(!empty($_GET['msgdel'])){$form = "";}
        
        return $form;
    }
    //�zenet st�tusz megv�ltoztat�sa
    function olvasva($id)
    {
        if($_GET['msg'] == 'news_read') {
            $regi_status = "select news_status from re_msg where msg_id = '$id'";
            $regi_query  = mysql_query($regi_status);
            $regi_sor    = mysql_fetch_assoc($regi_query);
            $status      = $regi_sor['news_status'];
            $stat_tomb   = explode(",",$status);
            $stat_tomb = (array)$stat_tomb;
            if($status != "") {$v = ",";}else{$v = "";}
            if(!in_array($this->userid,$stat_tomb)) {
                $uj_status = $status.$v.$this->userid;
                $sql_news = "update re_msg set news_status = '$uj_status'
                where msg_id = '$id'";
                mysql_query($sql_news);
            }
            
        }else{

        $sql_msg = "
        update re_msg set msg_status = '1'
        where msg_id = '$id'
        ";
        mysql_query($sql_msg);
        }
    }
}

$pm_read = new Pm_read;
if(!empty($_GET['msgdel']))
{
    $pm_read->msg_del($_GET['msgdel']);
}
print $pm_read->read_message();

?>
