<?php

/**************************************************************************
* R.E. login (1.8.1) - list.php                                           *
* ======================================================================= *
* User és online lista                                                    *
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
class UO_lista extends Re_login
{
    //listázott adatok
    var $user;
    var $nem;
    var $website;
    var $mail;
    var $jog;

    //sorok száma, és oldalszám
    var $num_rows;
    var $old_sz;

    //a lapozó linkek változói
    var $prev_link;
    var $next_link;
    var $s;
    var $n_link = array();
    var $max_s;
    var $where_onl;
    var $title;
    //elõkészítjük a listázáshoz az adatokat
    function uo_lista()
    {
        global $list_limit;
        //cím beállítása
        $this->title = (isset($_GET['list']) and $_GET['list'] == "o") ? "Akik online vannak" : "Tagok listája";
        //lekérdezzük az adatokat
        $where = (isset($_GET['list']) and $_GET['list'] == "o") ? "where status = '1'" : "where 1";
		if (!empty($_POST['kit']))
		{
			$_SESSION['kitkeres'] = $_POST['kit'];
		}
        if(!empty($_SESSION['kitkeres']))
        { $where .= " and user like '%$_SESSION[kitkeres]%'"; }

        $lista_sql      = "select * from re_login ". $where;

        $lista_query    = mysql_query($lista_sql);
        $num_rows       = mysql_num_rows($lista_query);
        //beáállítjuk a lekérdezett adatokat
        $this->num_rows = $num_rows;
        $this->old_sz   = ceil($num_rows / $list_limit);
        $max_s          = (int)($list_limit * $this->old_sz - $list_limit);
        $this->max_s = $max_s;
		
        $this->s        = (int)$_GET['s'];
        //lapozáshoz beállítjuk a változót
        eregi("[0-9]+", $_GET['s'], $gets);
        if($_GET['s'] != $gets[0] or $_GET['s'] == NULL)
        {
            $_GET['s'] = "0";
			$qs = "";
            foreach($_GET as $k => $v)
            {
                $qs .= $k."=".$v."&";
            }
            header("Location: $_SERVER[PHP_SELF]?$qs");
        }
        //ha túl nagy $_GET['s'], beállítjuk a max-ra
        if($_GET['s'] > $max_s)
        {
            $qs = eregi_replace("s=[0-9]+","s=$max_s", $_SERVER['QUERY_STRING']);
            if ($this->num_rows > 0) {
            	header("Location: $_SERVER[PHP_SELF]?{$qs}");
            }
        }
        // egyéb sql adatok
        Re_login::re_adatok();
    }
    function userkeres($order_by, $start, $list_limit)
    {
      if(isset($_POST['kit'])) { $_SESSION['kitkeres'] = $_POST['kit']; }
        //keresésbõl kilép
        if(!empty($_GET['search']) and $_GET['search'] == "false"){
            $_SESSION['kitkeres'] = NULL;
            $qs = eregi_replace("(search=false&)|(&search=false)|(search=false)","", $_SERVER['QUERY_STRING']);
            header("Location: $_SERVER[PHP_SELF]?$qs");
        }
        $keres = !empty($_SESSION['kitkeres']) ? $_SESSION['kitkeres'] : '';
        $like = "and re_login.user like '%{$keres}%'";
        
        $where = (isset($_GET['list']) and $_GET['list'] == "o")
        ? "where re_data.id = re_login.id and re_login.status = '1' $like order by $order_by limit $start, $list_limit"
        : "where re_data.id = re_login.id $like order by $order_by limit $start, $list_limit";
        return $where;
    }
    
    //Userkeresõ ûrlap felépítése
    function ukeresform()
    {
        $_GET['s'] = 0;
		$qs = "";
        foreach($_GET as $k => $v){ $qs .= "$k=$v&"; }
        $qs = rtrim($qs, "&");

        $ukeres = "
        <form action=\"$_SERVER[PHP_SELF]?$qs\" method=\"post\">
        A keresett nick:<br />
        <input type=\"text\" name=\"kit\" size=\"15\" />
        <input type=\"submit\" value=\"Keres\" />
        </form>
        ";
        
        if(!empty($_SESSION['kitkeres']))
        {
            $ukeres .= "<a href=\"$_SERVER[PHP_SELF]?$qs&search=false\">Teljes lista</a>";
        }
            return $ukeres;
    }
    
    //honnan kezdõdik a listázás
    function list_start()
    {
        global $list_limit;
        $s = $this->s;
        if($s < 0 ){$s = abs($s); }
        $start = ($s != NULL)
        ? $s : 0;
        return $start;
    }
    //a tovább link értékének beállitása
    function list_next()
    {
        global $list_limit;
        $next = $this->list_start() + $list_limit;
        return $next;
    }
    //a vissza link értékének beállítása
    function list_prev()
    {
        global $list_limit;
        $prev = $_GET['s'] - $list_limit;
        $prev = ($prev < 0) ? 0 : $prev;
        return $prev;
    }
    //a linkek összeállítása
    function prev_next()
    {
        $next = $this->list_next();
        $prev = $this->list_prev();
		$qs = "";
        foreach($_GET as $k => $v)
        {
            $qs .= $k."=".$v."&";
        }
        // lapozó linkek beállítása
        $qs_next = eregi_replace("s=[a-z0-9]+","s=$next", $qs);
        $qs_prev = eregi_replace("s=[0-9]+","s=$prev", $qs);
        global $list_limit;
        $this->prev_link = "&lt;&lt;";
        $this->next_link = "&gt;&gt;";
        if($this->num_rows > $this->s + $list_limit)
            { $this->next_link = "<a href=\"$_SERVER[PHP_SELF]?{$qs_next}\">$this->next_link</a>"; }
        if($this->s != 0)
            { $this->prev_link = "<a href=\"$_SERVER[PHP_SELF]?{$qs_prev}\">$this->prev_link</a>"; }

    }
    //a számlista beállítása
    function szam_list($szam_list)
    {
        global $list_limit;
        //Az oldal listázás beállítása
        $n_link = array();
        for($i = 1; $i <= $this->old_sz; $i++)
        {
            $start = $list_limit * $i - $list_limit;
            $qs_szam = eregi_replace("s=[0-9]+","s=$start", $_SERVER['QUERY_STRING']);
            $n_link[] = "<a href=\"$_SERVER[PHP_SELF]?{$qs_szam}\">$i</a>";
        }

        $this->n_link = $n_link;
        //Épp melyik oldalon vagyunk
        $oldal = ($this->s + $list_limit) / $list_limit;
        
        //Honnan kezdjük az oldal linkek kiiratását
        $tol = ($oldal - $szam_list) + 2;
        if($tol > $this->old_sz - $szam_list)
            { $tol = $this->old_sz - $szam_list; }
        if($tol < 0) { $tol = 0; }
        $n_link = array_splice($n_link, $tol,$szam_list);

        //Az utolsó oldalt is kiírjuk
        if($this->max_s > 2){
        $_GET['s'] = $this->max_s;
        $qs = "";
        foreach($_GET as $k => $v) { $qs .= "$k=$v&"; }
        $n_link[] = "...<a href=\"$_SERVER[PHP_SELF]?$qs\">$this->old_sz</a>";
        }
        $n_link = implode(" ", $n_link);
        return " ".$n_link;
        
    }

    //tag és onlinelista
    function listazo()
    {
        global $list_limit;
        //Beállítjuk a helyes listázást
        //* !!  Jelen verzióban nics fordított listázás. !! *//
		$m = isset($_GET['m']) ? $_GET['m'] : '';
        switch($m)
        {
            case "u1": $order_by = "re_login.user asc"; break;
            case "u2": $order_by = "re_login.user desc"; break;
            default:   $order_by = "re_login.user asc"; break;
        }
        
        
        $start = $this->list_start();
        //eldöntjük, hogy tag, vagy online lista
        $where = (isset($_GET['list']) and $_GET['list'] == "o")
        ? "where re_data.id = re_login.id and re_login.status = '1' order by $order_by limit $start, $list_limit"
        : "where re_data.id = re_login.id order by $order_by limit $start, $list_limit";

        $where = $this->userkeres($order_by, $start, $list_limit);

        //lekérdezzük az adatokat
        $lista_sql   = "select * from re_login, re_data  $where";
        $lista_query = mysql_query($lista_sql);
        
        //kiratjuk
        $kilist  = "<table border=\"1\" align=\"center\" cellspacing=\"0\">";
        $kilist .=
            "<tr>
                <th>Nick</th>
                <th>Nem</th>
                <th>E-mail</th>
                <th>Web</th>
                <th>Jog</th>
            </tr>";
        
        while( $data = mysql_fetch_assoc($lista_query) )
        {

            //beállítjuk az adatok kiiratását
            $data = $this->set_data($data);
            $kilist .=
            "<tr>
                <td class=\"user\">$data[user]</td>
                <td class=\"nem\">$data[nem]</td>
                <td class=\"mail\">$data[mail]</td>
                <td class=\"website\">$data[website]</td>
                <td class=\"jog\">$data[jog]</td>
            </tr>";
            
        }
        
        $kilist .= "</table>";
        return $kilist;
    }
    //adatok beállítása
    function set_data($data = array())
    {
        global $uinfo_olv;
        //A user neme
        switch($data['nem'])
        {
            case "F": $nem = "Fiú/Férfi"; break;
            case "N": $nem = "Lány/Nõ";   break;
            default:  $nem = "Nem tudja"; break;
        }

        //a user joga
        switch($data['jog'])
        {
            case "a": $jog = "Admin";   break;
            case "t": $jog = "Tag";     break;
            case "x": $jog = "Tiltott"; break;
            default:  $jog = "?????";   break;
        }
        //a user mailcíme
        $mail = ($data['mail'] != NULL)
        ? "<a href=\"mailto:$data[mail]\">e-mail</a>"
        : "e-mail";
        if($data['public_mail'] != '1' and $this->jog != "a"){
            $mail = "rejtve";
        }
        //a user weboldala
        $website = ($data['website'] != NULL)
        ? "<a href=\"$data[website]\">Website</a>"
        : "Website";
        $user = "<a href=\"$uinfo_olv?uid=$data[id]\">$data[user]</a>";

        $data['user']    = $user;
        $data['nem']     = $nem;
        $data['website'] = $website;
        $data['mail']    = $mail;
        $data['jog']     = $jog;
            
        return $data;
    }
}

$uo_lista = new UO_lista();
$uo_lista->prev_next();
print "<p align=\"center\"><b>$uo_lista->title</b></p>";
print "<div align=\"center\">".$uo_lista->ukeresform()."</div>";
print "<p align=\"center\">$uo_lista->prev_link ".$uo_lista->szam_list($szam_list)." $uo_lista->next_link</p>";
print $uo_lista->listazo();
print "<p align=\"center\">
$uo_lista->prev_link ".
$uo_lista->szam_list($szam_list).
" $uo_lista->next_link
</p>";

?>
