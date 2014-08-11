<?php
/**************************************************************************
* R.E. login (1.8.1) - pm_list.php                                        *
* ======================================================================= *
* �zenetek list�z�sa                                                      *
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
class Pm_list extends Re_msg
{
    var $num_rows;
    var $news_rows;
    var $old_sz;
    var $max_s;
    var $s;
    
    var $next_link;
    var $prev_link;
    
    //el�k�sz�tj�k a list�z�s adatait
    function pm_list()
    {
        global $msg_list_limit;
        Re_msg::re_msg();
        $pm_sql      = "
        select * from re_msg
        where to_id = '$this->userid'
        ";
        $news_sql     = "
        select * from re_msg
        where to_id = '0'
        ";
        $lista_sql      = ($_GET['msg'] == 'msg_list') ? $pm_sql : $news_sql;
        $lista_query    = mysql_query($lista_sql);
        $num_rows       = mysql_num_rows($lista_query);
        //be��ll�tjuk a lek�rdezett adatokat
        $this->num_rows = $num_rows;
        $this->old_sz   = ceil($num_rows / $msg_list_limit);
        $max_s          = (int)($msg_list_limit * $this->old_sz - $msg_list_limit);
        $this->max_s = $max_s;
		$s = isset($_GET['s']) ? $_GET['s'] : NULL;
        $this->s        = (int)$s;
        //lapoz�shoz be�ll�tjuk a v�ltoz�t
        if($num_rows != 0){
        eregi("[0-9]+", $s, $gets);
        if($s === NULL or $_GET['s'] != $gets[0])
        {
            $_GET['s'] = "0";
            foreach($_GET as $k => $v)
            {
                $qs .= $k."=".$v."&";
            }
            header("Location: $_SERVER[PHP_SELF]?$qs");
        }
        //ha t�l nagy $_GET['s'], be�ll�tjuk a max-ra
        if($_GET['s'] > $max_s)
        {
            $qs = eregi_replace("s=[0-9]+","s=$max_s", $_SERVER['QUERY_STRING']);
            header("Location: $_SERVER[PHP_SELF]?{$qs}");
        }
        }
    }

    //honnan kezd�dik a list�z�s
    function list_start()
    {
        global $msg_list_limit;
        $s = $this->s;
        if($s < 0 ){$s = abs($s); }
        $start = ($s != NULL)
        ? $s : 0;
        return $start;
    }

    //a tov�bb link �rt�k�nek be�llit�sa
    function list_next()
    {
        global $msg_list_limit;
        $next = $this->list_start() + $msg_list_limit;
        return $next;
    }

    //a vissza link �rt�k�nek be�ll�t�sa
    function list_prev()
    {
        global $msg_list_limit;
		$s = isset($_GET['s']) ? $_GET['s'] : 0;
        $prev = $s - $msg_list_limit;
        $prev = ($prev < 0) ? 0 : $prev;
        return $prev;
    }
    //a linkek �ssze�ll�t�sa
    function prev_next()
    {
        $next = $this->list_next();
        $prev = $this->list_prev();
		$qs = "";
        foreach($_GET as $k => $v)
        {
            $qs .= $k."=".$v."&";
        }
        // lapoz� linkek be�ll�t�sa
        $qs_next = eregi_replace("s=[a-z0-9]+","s=$next", $qs);
        $qs_prev = eregi_replace("s=[0-9]+","s=$prev", $qs);
        global $msg_list_limit;
        $this->prev_link = "&lt;&lt;";
        $this->next_link = "&gt;&gt;";
        if($this->num_rows > $this->s + $msg_list_limit)
            { $this->next_link = "<a href=\"$_SERVER[PHP_SELF]?{$qs_next}\">$this->next_link</a>"; }
        if($this->s != 0)
            { $this->prev_link = "<a href=\"$_SERVER[PHP_SELF]?{$qs_prev}\">$this->prev_link</a>"; }

    }

    function szam_list($szam_list)
    {
        global $msg_list_limit;
        //Az oldal list�z�s be�ll�t�sa
        for($i = 1; $i <= $this->old_sz; $i++)
        {
            $start = $msg_list_limit * $i - $msg_list_limit;
            $qs_szam = eregi_replace("s=[0-9]+","s=$start", $_SERVER['QUERY_STRING']);
            $n_link[] = "<a href=\"$_SERVER[PHP_SELF]?{$qs_szam}\">$i</a>";
        }

        $this->n_link = $n_link;
        //�pp melyik oldalon vagyunk
        $oldal = ($this->s + $msg_list_limit) / $msg_list_limit;

        //Honnan kezdj�k az oldal linkek kiirat�s�t
        $tol = ($oldal - $szam_list) + 2;
        if($tol > $this->old_sz - $szam_list)
            { $tol = $this->old_sz - $szam_list; }
        if($tol < 0) { $tol = 0; }
        $n_link = array_splice($n_link, $tol,$szam_list);

        //Az utols� oldalt is ki�rjuk
        if($this->max_s > 2){
        $_GET['s'] = $this->max_s;
        $qs = "";
        foreach($_GET as $k => $v) { $qs .= "$k=$v&"; }
        $n_link[] = "...<a href=\"$_SERVER[PHP_SELF]?$qs\">$this->old_sz</a>";
        }
        $n_link = implode(" ", $n_link);
        return $n_link;

    }

    //az adatb�zisadataok felhaszn�l� sz�m�ra �rthet�v� t�tele
    function msg_list_conv($message = array())
    {
        if(strlen($message['subject']) > 20)
        {
            $message['subject'] = substr($message['subject'], 0, 20)."...";
        }
        $message['date'] = date("Y.m.d H.i.s", $message['date']);

        if($_GET['msg'] == 'msg_list'){
            switch($message['msg_status'])
            {
                case "0": $message['msg_status'] ="�j"; break;
                case "1": $message['msg_status'] ="X";  break;
                default:  $message['msg_status'] ="??"; break;
            }
        }else{
            if(!in_array($this->userid,explode(",",$message['news_status']))){
                $message['msg_status'] = "�j";
            }else{
                $message['msg_status'] = "X";
            }
        }
        $message['subject'] = htmlspecialchars($message['subject']);
        $message['from'] = $this->msg_from($message['from_id']);
        return $message;
    }
    //�zenetek csoportos t�rl�se
    function csop_del()
    {
        $_POST['uzidel'] = (array)$_POST['uzidel'];
        $_POST['uzidel'] = array_map("sqlor", $_POST['uzidel']);
        $imp = implode(" or ", $_POST['uzidel']);
        $sql = "delete from re_msg where to_id = $this->userid and ($imp)";
        if($_GET['msg'] == "news" and $this->jog == "a"){
        $sql = "delete from re_msg where to_id = 0 and ($imp)";
        }
        if(count($_POST['uzidel']) > 0)
        {
            mysql_query($sql);
            print "<p align=\"center\"><b>
            ". count($_POST['uzidel']) . "
             </b>�zenet sikeresen t�r�lve</p>";
        }
    }


    //az �zenetek kilist�z�sa
    function msg_list()
    {
        global $msg_list_limit, $uinfo_olv;
        $start = $this->list_start();
        if($_GET['msg'] == "msg_list")
        {
            $list_id = $this->userid;
            $list_id2 = "re_login.id";
            $select = ",re_login";
        }else{
            $list_id = 0;
            $list_id2 = 0;
            $select = "";
        }
        $sql = "
        select * from re_msg $select
        where re_msg.to_id = '$list_id' and re_msg.to_id = $list_id2
        order by re_msg.date desc
        limit $start, $msg_list_limit";

        $sql = "
        select * from re_msg $select
        where re_msg.to_id = '$list_id' and re_msg.to_id = $list_id2 and re_msg.date >= (select re_login.regido from re_login where re_login.id = {$this->userid})
        order by re_msg.date desc
        limit $start, $msg_list_limit";

        $query = mysql_query($sql); print mysql_error();

		print "<form name=\"form1\" action=\"$_SERVER[REQUEST_URI]\" method=\"post\">";
        print "
        <table align=\"center\" border=\"1\" cellspacing=\"1\">
        <tr>
            <th>St�tusz</th>
            <th>K�ld�</th>
            <th>T�rgy</th>
            <th>D�tum</th>";
            if($_GET['msg'] == "msg_list" or $this->jog == "a"){
            print "<th><input type=\"checkbox\" id=\"delall\"/></th>";
            }
        print "
        </tr>
        ";
        
        while($uzenet = mysql_fetch_assoc($query))
        {
            $uzenet = $this->msg_list_conv($uzenet);
            if($_GET['msg'] == "msg_list"){
                $subject_link = $this->msg_mylink("msg",NULL, "read");
            }else{
                $subject_link = $this->msg_mylink("msg",NULL, "news_read");
            }
            $subject_link = $this->msg_mylink("sid", NULL,$uzenet['msg_id']);
            $subject_link = $this->msg_mylink("re");
            $subject_link = $this->msg_mylink("uid");

            $user_link = $this->msg_mylink("msg");
            $user_link = $this->msg_mylink("sid");
            $user_link = $this->msg_mylink("re");
            $user_link = $this->msg_mylink("uid", $uinfo_olv,$uzenet['from_id']);

            $uzenet['subject'] = "<a href=\"$subject_link\">$uzenet[subject]</a>";
            $uzenet['user']    = "<a href=\"$user_link\">$uzenet[from]</a>";
            print "<tr>
            <td class=\"status\" align=\"center\">$uzenet[msg_status]</td>
            <td class=\"from\">$uzenet[user]</td>
            <td class=\"subject\">$uzenet[subject]</td>
            <td class=\"date\">$uzenet[date]</td>";
            if($_GET['msg'] == "msg_list" or $this->jog == "a"){
                print "
                <td class=\"delform\" align=\"center\">
                <input type=\"checkbox\" name=\"uzidel[]\" value=\"$uzenet[msg_id]\" />
                </td>
                ";
            }
            print "</tr>";
        }
        if($_GET['msg'] == "msg_list" or $this->jog == "a"){
        print "
        <tr>
            <td colspan=\"5\" align=\"center\">
            <input type=\"submit\" name=\"deljelolt\" value=\"T�rl�s\"/>
            </td>
        </tr>";
        }
        print "</table></form>";

    }
}
        function sqlor($elem)
        {
            return "msg_id = '$elem'";
        }
$pm_list = new Pm_list;
if (isset($_POST['uzidel']))
{
	$pm_list->csop_del();
}
$pm_list->prev_next();

if($pm_list->num_rows != 0){
print "<p align=\"center\">$pm_list->prev_link ".$pm_list->szam_list($szam_list)." $pm_list->next_link</p>";
$pm_list->msg_list();
print "<p align=\"center\">$pm_list->prev_link ".$pm_list->szam_list($szam_list)." $pm_list->next_link</p>";
}else{
  if($_GET['msg'] == "msg_list"){
    print "<p align=\"center\">Nincs egy �zenet sem.</p>";
  }elseif($_GET['msg'] == "news"){
    print "<p align=\"center\">Nincs egy h�r sem.</p>";
  }
}
if($_GET['msg'] == "msg_list" or $pm_list->jog == "a" )
{
    print "
    <script langauge=\"javascript\" type=\"text/javascript\" src=\"{$gyoker}$login_mappa/moduls/msg/msgdel.js\"></script>
    ";
}
?>

