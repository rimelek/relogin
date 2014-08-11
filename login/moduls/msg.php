<?php
/**************************************************************************
* R.E. login (1.8.1) - msg.php                                            *
* ======================================================================= *
* �zenetek k�ld�se modul                                                  *
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
class Re_msg extends Re_login
{
    //v�ltoz�k
    var $action;
    var $count_new_msg;
    var $count_news;

    //adatok be�ll�t�sa (konstruktor)
    function re_msg()
    {
        $this->action = $_SERVER['REQUEST_URI'];
        $this->re_adatok();
        $this->count_new_msg();
        $this->count_news();
    }
    
    //uj bej�v� �zenetek sz�ma
    function count_new_msg()
    {
        $sql   = "select * from re_msg where msg_status = '0' and to_id = '$this->userid'";
        $query = mysql_query($sql) or die(mysql_error());
        $count = mysql_num_rows($query);
        $this->count_new_msg = $count;
    }

    function count_news()
    {
        $sql   = "select * from re_msg where to_id = '0' and re_msg.date >= (select re_login.regido from re_login where re_login.id = '{$this->userid}')";
        $query = mysql_query($sql) or die(mysql_error());
        $i = 0;
        while($sor = mysql_fetch_assoc($query)){
            if(!in_array($this->userid,explode(",",$sor['news_status']))) {$i++;}
        }
        $this->count_news = $i;
    }

    //url alak�t�sa, get parancs v�lt�sa
    function msg_mylink($get_key, $url = NULL,$new_value = NULL)
    {
        $getm = (array)$_GET;
        static $getm;
        $getm[$get_key] = $new_value;
        if($url == NULL) {$url  = $_SERVER['PHP_SELF'];}

        if($new_value == NULL){ $getm[$get_key] = ""; }
		$qs = "";
        foreach($getm as $k=>$v)
        {
            if($v != ""){
                $qs .= "$k=$v&";
            }

        }
        return "$url?$qs";
    }
    
    //sql adatok lek�r�se
    function pm_sql($id)
    {
        $sql = "
        select * from re_msg
        where msg_id = '$id'
        ";
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);
        return $fetch;
    }
    
    //�zenetk�ld�s �rlap
    function msg_form()
    {
		$fwre = "";
		$re_cimzett = "";
		$re_text = "";
		$re_subject = "";
        if(isset($_GET['fw']) and $_GET['fw'] != NULL) {$fwre = "fw";}
        if(isset($_GET['re']) and $_GET['re'] != NULL) {$fwre = "re";}

        if(!empty($fwre))
        {
            $uzenet = $this->pm_sql($_GET[$fwre]);
            $uzenet['message'] = htmlspecialchars($uzenet['message']);
            $uzenet['subject'] = htmlspecialchars($uzenet['subject']);
            if($uzenet['to_id'] == $this->userid)
            {
                $re_subject2 = explode(":", $uzenet['subject']);
                if($re_subject2[0] == $fwre )
                {$re_subject = "$fwre:$re_subject2[1]";}
                else{$re_subject = "$fwre: $uzenet[subject]";}
                $re_cimzett = $this->msg_from($uzenet['from_id']);
            }
        }
        if($fwre == "fw")
        {
            $re_text = $uzenet['message'];
            $re_subject = "$fwre: $uzenet[subject]";
            $re_cimzett = "";
        }
        
        if($this->jog == "a"){
        $msg_admin = "<b>::</b> �sszes felhaszn�l�nak: <input type=\"checkbox\" name=\"msg_all\" />";
        }else{ $msg_admin = ""; }
        $msg_form = "
        <table align=\"center\" cellspacing=\"0\">
        <form action=\"$this->action\" method=\"post\">
        <tr>
            <td class=\"cim\">
            C�mzett:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"msg_to\" size=\"15\" value=\"$re_cimzett\"/>
            $msg_admin
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            T�rgy:
            </td>
            <td class=\"input\">
            <input type=\"text\" name=\"msg_subject\" size=\"15\" maxlength=\"50\" value=\"$re_subject\"/>
            </td>
        </tr>
        <tr>
            <td class=\"cim\">
            �zenet:
            </td>
            <td class=\"input\">
            <textarea name=\"msg_message\" cols=\"50\" rows=\"6\">$re_text</textarea>
            </td>
        </tr>
        <tr>
            <td>
            &nbsp;
            </td>
            <td>
            <input type=\"submit\" name=\"msg_send\" value=\"Elk�ld\" />
            </td>
        </tr>
        </form>
        </table>

        ";
        return $msg_form;
    }

    //l�tezik-e a megadott nick
    function u_search()
    {
        $to = trim($_POST['msg_to']);
        $sql   = "select id from re_login where user = '$to' limit 1";
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);
        
        return $fetch['id'];
    }

    //ha elk�ldt�k az �zenetet, ne k�ldje ujra friss�t�skor
    function check_flood($flood_msg, $msg, $flood_id, $id)
    {
        if(($flood_msg == $msg) and ($flood_id == $id)) return true;
        return false;
    }

    //�zenetek t�rl�se
    function msg_del($id)
    {
		$id = (int)$id;
        $sql = "
        delete from re_msg
        where msg_id = '$id' and to_id = '$this->userid'
        ";
        if($this->jog == "a") {
            $sql = "
            delete from re_msg
            where msg_id = '$id' and (to_id = '$this->userid' or to_id = 0)
            ";
        } 
        if($this->msg_check($id))
        {
            mysql_query($sql);
            print "<p align=\"center\"><b>Az �zenet t�r�lve!</b></p>";
        }else{
            print "
            <p align=\"center\">
            <b>Az �zenet nem l�tezik, vagy nincs jogod t�r�lni!</b>
            </p>";
        }
    }
    //olvasatlan �zenetek t�rl�se megadott id� ut�n
    function msg_del_time($msg_max_time)
    {
        $time = time();
        $msg_max_time = $time - ($msg_max_time * 24 * 60 * 60);
        $sql = "
        delete from re_msg
        where date < '$msg_max_time' and msg_status = '1'
        ";
        mysql_query($sql);
    }
    //adott user �sszes �zenet�nek sz�ma
    function msg_count($id)
    {
        $sql = "
        select msg_id from re_msg
        where to_id = '$id'
        ";
        $query = mysql_query($sql);
        $count = mysql_num_rows($query);
        return $count;
    }
    
    //ellen�rizz�k, hogy l�tezik �-e az �zenet
    //�s a felhaszn�l� volt a c�mzett
    function msg_check($id)
    {
		if ($this->jog != "a")
		{
			$sql = "
				select * from re_msg
				where msg_id = '$id' and to_id = '$this->userid'
				";
		}
		else
		{
			$sql = "
				select * from re_msg
				where msg_id = '$id' and (to_id = '$this->userid' or to_id = '0')
				";
		}
        $query = mysql_query($sql);
        $fetch = mysql_fetch_assoc($query);
        return $fetch;
    }

    //k�ld� neve
    function msg_from($id)
    {
        $sql = "
        select re_login.user from re_login, re_msg
        where re_login.id = re_msg.from_id and re_login.id = '$id'
        ";
        $query = mysql_query($sql) or die(mysql_error());
        $fetch = mysql_fetch_assoc($query);
        return $fetch['user'];
    }

    //�zenet elk�ld�se
    function msg_send()
    {
        //sz�ks�ges sql adatok megad�sa
        global $msg_max_uzi;
        $from_id    = $this->userid;
        if(isset($_POST['msg_all']) and $this->jog == "a"){
          $to_id = "0";
          $_POST['msg_to'] = "Minden user";
        }else{
          $to_id = $this->u_search();
        }
        $subject    = trim($_POST['msg_subject']);
        if(!$subject){ $subject = "Nincs t�rgy!"; }
        $message    = rtrim($_POST['msg_message']);
        $date       = time();
        $msg_status = "0";
        
        //sql parancs
        $sql = "
        insert into re_msg
        values('', '$from_id', '$to_id', '$subject', '$message', '$date', '$msg_status','')";
        //ha megt�rt�n a k�ld�s k�relem
        if(isset($_POST['msg_send']))
        {
            //ha m�g nem k�ldt�k el azt az �zenetet

            if(!isset($_SESSION['flood_msg']) or !$this->check_flood(
            $_SESSION['flood_msg'], $_POST['msg_message'],$_SESSION['flood_id'], $to_id
            ))
            {
                //akkor ha l�tezik a felhaszn�l�
                if($to_id !== NULL)
                {
                    //�s az a c�mzett nem egyezik meg a k�ld�vel
                    if( strtolower($_SESSION['usr'] != strtolower($_POST['msg_to'])) )
                    {
                        //ha a nem telt meg a psotafi�kja a c�mzettnek
                        if($this->msg_count($to_id) < $msg_max_uzi){
                        //be�rjuk az adatb�zisba az �zenetet
                        mysql_query($sql);
                        $_SESSION['flood_msg'] = $_POST['msg_message'];
                        $_SESSION['flood_id']  = $to_id;
                        print "
                        <p align=\"center\">
                        �zenet elk�ldve <b>$_POST[msg_to]</b> r�sz�re!
                        </p>";
                        }else{
                        //de ha megtelt a postafi�k
                        print "
                        <p align=\"center\">
                        Sajnos megtelt $_POST[msg_to] postafi�kja<br />
                        Pr�b�lkozz k�s�bb!
                        </p>";
                        }
                    }else{
                        //de ha a c�mzett megegyezik a k�ld�vel, hiba�zenet
                        print "
                        <p align=\"center\">
                        Magadnak nem k�ldhetsz �zenetet!
                        </p>";
                    }
                }else{
                    //ha nem is l�tezik a nick, hiba�zenet
                    print "
                    <p align=\"center\">
                    Nincs <b>$_POST[msg_to]</b> nev� felhaszn�l�nk!
                    </p>";
                }
            }else{
                //ha m�r el lett k�ldve egyszer, hiba�zenet
                print "
                <p align=\"center\">
                Ezt az �zenetet m�r elk�ldted!
                </p>";
            }
        }
    }
}
/**********************************************/
$re_msg = new Re_msg;
$re_msg->msg_del_time($msg_max_time);
//f�jln�v ellen�rz�s

$message_url = strtolower($message_url);

//ha az �zenetk�ld�st megjelen�t� f�jlban vagyunk
if($_SERVER['PHP_SELF'] == $message_url)
{

    //akkor megjelen�tj�k a linkeket
    $bejovo = $re_msg->msg_mylink("msg", NULL,"msg_list");
    $bejovo = $re_msg->msg_mylink("sid");
    $bejovo = $re_msg->msg_mylink("re");
    $bejovo = $re_msg->msg_mylink("fw");
    
    $new_msg = $re_msg->msg_mylink("msg");
    $new_msg = $re_msg->msg_mylink("sid");
    $new_msg = $re_msg->msg_mylink("re");
    $new_msg = $re_msg->msg_mylink("fw");
    
    $news = $re_msg->msg_mylink("msg",NULL, "news");
    $news = $re_msg->msg_mylink("sid");
    $news = $re_msg->msg_mylink("re");
    $news = $re_msg->msg_mylink("fw");

	$msg = isset($_GET['msg']) ? $_GET['msg'] : "";
    
    print "<p align=\"center\"><a href=\"$new_msg\">�j k�ld�s</a> |
    <a href=\"$bejovo\">Bej�v� �zenetek(�j: $re_msg->count_new_msg)</a> |
    <a href=\"$news\">H�rek(�j: $re_msg->count_news)</a></p>";
    //�s az �zenetk�ld� �rlapot
    if($msg == "")
    {
		if (isset($_POST['msg_to']))
		{
			$re_msg->msg_send();
		}
        print $re_msg->msg_form();
    }elseif($msg == "msg_list"  or $msg == "news"){
        include("{$gyoker}$login_mappa/moduls/msg/pm_list.php");
    }elseif($msg == "read" or $msg == "news_read"){
        include("{$gyoker}$login_mappa/moduls/msg/pm_read.php");
    }
}else{
    //egy�bk�nt csak az �zenet linket, ami a f�jlra mutat
    if($re_msg->jog != NULL ){
      $num = $re_msg->count_new_msg + $re_msg->count_news;
    print "<a href=\"$message_url\">�zenetek($num)</a>";
    }
}

?>
