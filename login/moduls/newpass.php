<?php
/**************************************************************************
* R.E. login (1.8.1) - newpass.php                                        *
* ======================================================================= *
* Jelsz� eml�keztet�                                                      *
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
class Newpass extends Re_login
{
    var $action;

    function newpass()
    {
        $this->action = $_SERVER['REQUEST_URI'];
    }

    //eml�keztet� �rlapja
    function np_form()
    {
        $form = "<div align=\"center\">
        <form align=\"center\" action=\"$this->action\" method=\"POST\">
        �rd be nicknevet:<br />
        <input type=\"text\" name=\"nick\" size=\"15\" /><br />
        Az �j jelsz�:<br />
        <input type=\"password\" name=\"newpass\" size=\"15\" /><br />
        Az �j jelsz� �jra:<br />
        <input type=\"password\" name=\"renewpass\" size=\"15\" /><br />
        <input type=\"submit\" value=\"K�ld\" />
        </form>
        </div>
        ";
        return $form;
    }


    //megkeress�k a megadott mailc�met
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

    //elk�ldj�k az �j jelsz�t
    function np_kuld($mail,$pass,$id)
    {
        global $index_url, $from, $newpass_url;
        
        
        $np = md5($_POST['newpass']);
        $code = md5(sha1($mail).$pass).$id;
        if(strpos($newpass_url, "?") !== false)
        {$newpass_url = $newpass_url . "&code=$code&np=$np";}
        else{ $newpass_url = $newpass_url . "?code=$code&np=$np"; }
        $index_url = ltrim($index_url, "/");
        $subject = "=?ISO-8859-2?B?".base64_encode("Jelsz� eml�keztet�")."?=";
        $mail_to = @mail(
                    $mail, $subject,
                    "Rendeszer�nkben: http://$_SERVER[HTTP_HOST]/$index_url \n".
                    "valaki jelsz� eml�keztet�t k�rt a nevedben.\n".
                    "Amennyiben j�v�hagyod a jelsz� v�ltoztat�st ".
                    "az al�bbi linkre kattintva, ".
                    "az �j jelszavad \"$_POST[newpass]\" lesz.\n".
                    "http://$_SERVER[HTTP_HOST]{$newpass_url} ",
                    "From: ".$from.PHP_EOL.
					"Content-type: text/plain; charset=iso-8859-2"
            );
        //Ha az e-mail k�ld�se sikertelen volt
        if(!$mail_to)
        {
            print "<p align=\"center\">A k�ld�s sikertelen!</p>";
            unset($_SESSION['npnick']);
        //Ha a mail k�ld�se sikeres volt
        }else{
            print "
            <p align=\"center\">
            A jelsz�t aktiv�lhatod a mailben kapott linken<br />
            Meg�rkez�s�hez esetenk�nt hosszabb id� kell!
            </p>";
        }
    }

    //adatb�zisban �t�rjuk a r�gi jelsz�t az �jra
    function np_write()
    {
        //ha az �j jelsz� benne van az url-ben
        if(isset($_GET['np']) and $_GET['np'] != "")
        {
            //ha az elen�rz� k�d benne van az url-ben
            if(isset($_GET['code']) and strlen($_GET['code']) > 32)
            {
                //lek�rj�k az adatokat az adatb�zisb�l
                $id = substr($_GET['code'],32);
                $sql1 = "
                select * from re_login,re_data where
                re_login.id = re_data.id and
                re_login.id = '$id'
                ";
                $query1 = mysql_query($sql1);
                $fetch = mysql_fetch_assoc($query1);
                //�jabb elen�rz�s, hogy az adatok megfelelnek-e.
                if(md5(sha1($fetch['mail']).$fetch['pass']).$id == $_GET['code'])
                {
                    //Ha megfelelnek, akkor �t�rja a r�gi jelsz�t az �j jelsz�ra
                    mysql_query("update re_login set pass = '$_GET[np]' where id = '$id'");
                    print "<p align=\"center\">Sikeres jelsz� csere!</p>";
                //Ha nem felelnek meg az adatok
                }else{
                    print "<p align=\"center\">Hib�s azonos�t�!</p>";
                }
            //ha az ellen�rz� k�d hi�nyzik az url-b�l
            }else{
                print "<p align=\"center\">Hib�s azonos�t�!</p>";
            }
        }
    }

    //Az �j jelsz� elk�ld�se...
    function np_mail()
    {
        //ha m�g nem k�ldt�k el
        if(isset($_POST['nick']) and (!isset($_SESSION['npnick']) or $_SESSION['npnick'] != $_POST['nick']))
        {
            //keres, be�r�s
            $np_keres = $this->np_keres($_POST['nick']);
            //ha nem l�tezik a nick n�v
            if($np_keres == NULL)
            {
                print "<p align=\"center\">Nincs ilyen nick n�v az adatb�zisban!</p>";
            }else{
                //ha a k�t jelsz� nem egyezik
                if($_POST['newpass'] != $_POST['renewpass'])
                {
                    print "<p align=\"center\">A k�t jelsz� nem egyezik</p>";
                //ha a k�t jelsz� egyezik
                }else{
                  //Ha az e-mail c�me m�r aktiv�lva van
                  if($np_keres['mail'] == $np_keres['uj_mail']){
                    //ha az �j jelsz� nem r�vid
                    if(strlen($_POST['newpass']) >= 5){
                        //lek�ri a nick-hez tartoz� adatokat
                        $this->np_kuld($np_keres['mail'],$np_keres['pass'],$np_keres['id']);
                        if(isset($_POST['nick'])){ $_SESSION['npnick'] = $_POST['nick']; }
                    //ha a jelsz� t�l r�vid
                    }else{
                        print "<p align=\"center\">A jelsz� t�l r�vid<small>(min: 5 karakter)</small></p>";
                    }
                  //Ha az e-mail c�me m�g nincs aktiv�lva
                  }else{
                    print "<p align=\"center\">El�bb aktiv�ld az e-mail c�med!</p>";
                  }
                }
            }
            //Ha m�r elk�ldt�k egyszer arra a c�mre az eml�keztet�t
        }else{
            if(isset($_POST['nick'])){
                print "<p align=\"center\">Ezt az eml�keztet�t m�r elk�ldted</p>";
            }
        }
    }
}

$newpass_obj = new Newpass;
$newpass_obj->np_write();

print $newpass_obj->np_form();

$newpass_obj->np_mail();

?>
