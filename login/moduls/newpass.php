<?php
/**************************************************************************
* R.E. login (1.8.1) - newpass.php                                        *
* ======================================================================= *
* Jelszó emlékeztetõ                                                      *
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
class Newpass extends Re_login
{
    var $action;

    function newpass()
    {
        $this->action = $_SERVER['REQUEST_URI'];
    }

    //emlékeztetõ ûrlapja
    function np_form()
    {
        $form = "<div align=\"center\">
        <form align=\"center\" action=\"$this->action\" method=\"POST\">
        Írd be nicknevet:<br />
        <input type=\"text\" name=\"nick\" size=\"15\" /><br />
        Az új jelszó:<br />
        <input type=\"password\" name=\"newpass\" size=\"15\" /><br />
        Az új jelszó újra:<br />
        <input type=\"password\" name=\"renewpass\" size=\"15\" /><br />
        <input type=\"submit\" value=\"Küld\" />
        </form>
        </div>
        ";
        return $form;
    }


    //megkeressük a megadott mailcímet
    function np_keres($user)
    {
        $user = strtolower($user);
        $sql = "
        select * from re_login,re_data where
        lcase(re_login.user) = '$user' and
        re_login.id = re_data.id
        ";
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);
        return $fetch;
    }

    //elküldjük az új jelszót
    function np_kuld($mail,$pass,$id)
    {
        global $index_url, $from, $newpass_url;
        
        
        $np = md5($_POST['newpass']);
        $code = md5(sha1($mail).$pass).$id;
        if(strpos($newpass_url, "?") !== false)
        {$newpass_url = $newpass_url . "&code=$code&np=$np";}
        else{ $newpass_url = $newpass_url . "?code=$code&np=$np"; }
        $index_url = ltrim($index_url, "/");
        $subject = "=?ISO-8859-2?B?".base64_encode("Jelszó emlékeztetõ")."?=";
        $mail_to = @mail(
                    $mail, $subject,
                    "Rendeszerünkben: http://$_SERVER[HTTP_HOST]/$index_url \n".
                    "valaki jelszó emlékeztetõt kért a nevedben.\n".
                    "Amennyiben jóváhagyod a jelszó változtatást ".
                    "az alábbi linkre kattintva, ".
                    "az új jelszavad \"$_POST[newpass]\" lesz.\n".
                    "http://$_SERVER[HTTP_HOST]{$newpass_url} ",
                    "From: ".$from.PHP_EOL.
					"Content-type: text/plain; charset=iso-8859-2"
            );
        //Ha az e-mail küldése sikertelen volt
        if(!$mail_to)
        {
            print "<p align=\"center\">A küldés sikertelen!</p>";
            unset($_SESSION['npnick']);
        //Ha a mail küldése sikeres volt
        }else{
            print "
            <p align=\"center\">
            A jelszót aktiválhatod a mailben kapott linken<br />
            Megérkezéséhez esetenként hosszabb idõ kell!
            </p>";
        }
    }

    //adatbázisban átírjuk a régi jelszót az újra
    function np_write()
    {
        //ha az új jelszó benne van az url-ben
        if(isset($_GET['np']) and $_GET['np'] != "")
        {
            //ha az elenõrzõ kód benne van az url-ben
            if(isset($_GET['code']) and strlen($_GET['code']) > 32)
            {
                //lekérjük az adatokat az adatbázisból
                $id = substr($_GET['code'],32);
                $sql1 = "
                select * from re_login,re_data where
                re_login.id = re_data.id and
                re_login.id = '$id'
                ";
                $query1 = mysql_query($sql1);
                $fetch = mysql_fetch_assoc($query1);
                //Újabb elenõrzés, hogy az adatok megfelelnek-e.
                if(md5(sha1($fetch['mail']).$fetch['pass']).$id == $_GET['code'])
                {
                    //Ha megfelelnek, akkor átírja a régi jelszót az új jelszóra
                    mysql_query("update re_login set pass = '$_GET[np]' where id = '$id'");
                    print "<p align=\"center\">Sikeres jelszó csere!</p>";
                //Ha nem felelnek meg az adatok
                }else{
                    print "<p align=\"center\">Hibás azonosító!</p>";
                }
            //ha az ellenõrzõ kód hiányzik az url-bõl
            }else{
                print "<p align=\"center\">Hibás azonosító!</p>";
            }
        }
    }

    //Az új jelszó elküldése...
    function np_mail()
    {
        //ha még nem küldtük el
        if(isset($_POST['nick']) and (!isset($_SESSION['npnick']) or $_SESSION['npnick'] != $_POST['nick']))
        {
            //keres, beírás
            $np_keres = $this->np_keres($_POST['nick']);
            //ha nem létezik a nick név
            if($np_keres == NULL)
            {
                print "<p align=\"center\">Nincs ilyen nick név az adatbázisban!</p>";
            }else{
                //ha a két jelszó nem egyezik
                if($_POST['newpass'] != $_POST['renewpass'])
                {
                    print "<p align=\"center\">A két jelszó nem egyezik</p>";
                //ha a két jelszó egyezik
                }else{
                  //Ha az e-mail címe már aktiválva van
                  if($np_keres['mail'] == $np_keres['uj_mail']){
                    //ha az új jelszó nem rövid
                    if(strlen($_POST['newpass']) >= 5){
                        //lekéri a nick-hez tartozó adatokat
                        $this->np_kuld($np_keres['mail'],$np_keres['pass'],$np_keres['id']);
                        if(isset($_POST['nick'])){ $_SESSION['npnick'] = $_POST['nick']; }
                    //ha a jelszó túl rövid
                    }else{
                        print "<p align=\"center\">A jelszó túl rövid<small>(min: 5 karakter)</small></p>";
                    }
                  //Ha az e-mail címe még nincs aktiválva
                  }else{
                    print "<p align=\"center\">Elõbb aktiváld az e-mail címed!</p>";
                  }
                }
            }
            //Ha már elküldtük egyszer arra a címre az emlékeztetõt
        }else{
            if(isset($_POST['nick'])){
                print "<p align=\"center\">Ezt az emlékeztetõt már elküldted</p>";
            }
        }
    }
}

$newpass_obj = new Newpass;
$newpass_obj->np_write();

print $newpass_obj->np_form();

$newpass_obj->np_mail();

?>
