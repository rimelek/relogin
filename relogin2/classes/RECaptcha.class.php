<?php
/**
 * Módosítva: 2011.06.05.
 *
 * <b>Szerző weboldala:</b> {@link http://rimelek.hu/}<br />
 * <b>Captcha weblapja:</b> {@link http://rimelek.hu/php-ellenorzo-kod-r-e-captcha-v1-0/ R.E. Captcha v1.0}
 *
 * Két féle üzemmódot felváltva használó Captcha.<br />
 * 1. Random karakterek felismerése<br />
 * 2. Egyszerű matematikai művelet megoldása.<br />
 * Ezen kívül két féle módon jeleníthető meg. Egy részt kép típusú fájlban
 * {@example examples/image.php}
 * Más részt html kódba ágyazva, a kép forrását base64 kódolással
 * az img tag src tulajdonságába helyezve
 * {@example examples/html.php}
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright Copyright (C) 2010, Takács Ákos
 * @version 1.0.1
 * @package RECaptcha
 */


/**
 * Két féle üzemmódot felváltva használó Captcha.
 * 1. Random karakterek felismerése
 * 2. Egyszerű matematikai művelet megoldása
 *
 * @author Takács Ákos (Rimelek), programmer [at] rimelek [dot] hu
 * @copyright 2008
 * @package RECaptcha
 */

class RECaptcha
{
	/**
	 * A kép típusa (jpeg, png, gif)
	 *
	 * @var string
	 */
	protected $type = 'jpeg';

	/**
	 * A kép szélessége
	 *
	 * @var int
	 */
	protected $width = 200;

	/**
	 * A kép magassága
	 *
	 * @var int
	 */
	protected $height = 50;

	/**
	 * Háttérszín RGB összetevői: R,G,B formátumban
	 *
	 * @var string
	 */
	protected $bgcolor = '255,255,255';

	/**
	 * Háttérszín azonosítója
	 *
	 * @var int
	 */
	protected $_bgcolor;

	/**
	 * Betűtípus ttf fájljának útvonala
	 *
	 * @var string
	 */
	protected $fonttype = 'fonts/arial.ttf';

	/**
	 * Háttérzavarás intenzitásának beállítása
	 *
	 * @var int
	 */
	protected $bgintensity = 10;

	/**
	 * A karakterek előttizavaró jelek intenzitása
	 *
	 * @var int
	 */
	protected $fgintensity = 5;

	/**
	 * Betűméret
	 *
	 * @var int
	 */
	protected $fontsize = 17;

	/**
	 * A Captcha által megjelenített képre adandó válasz értéke.
	 * Matematikai művelet esetén annak megoldása, karakterek
	 * esetén a megjelenített karakterek.
	 *
	 * @var string
	 */
	protected $text;

	/**
	 * {@link __toString()} -ben generált html img tag plusz paraméterei<br />
	 * Például:
	 * <code>
	 * $params = array('style'='width: 300px;', 'onclick'=>'eventHandler();');
	 * </code>
	 *
	 * @var array
	 */
	protected $params = array();

	/**
	 * Ezen nevű session változóba teszi be a captcha -ra adandó válasz értékét.
	 * Ezt lehet majd felhasználni az ellenőrzésnél.
	 *
	 * @var string
	 */
	protected $session_name = 'captcha_code';

	/**
	 * Karakterek közti minimális és maximális távolság pixelben
	 * A tömb első és második elemei sorrendben.
	 *
	 * @var array
	 */
	protected $spacerange = array(6,8);

	/**
	 * Captcha létrehozása különböző opciókkal. Az opciókról részletesebben a
	 * {@link setProperties()} metódus dokumentációjánál.
	 *
	 * @param array $options
	 */
	public function __construct($options=array())
	{
		$this->setProperties($options);
	}

	/**
	 * Captcha opcióinak beállítása
	 *
	 * @param array $options Opciók asszociatív tömbje. Az opciók a következők
	 *		lehetnek:<br />
	 *		<ul>
	 *			<li><b>session_name:</b> {@link $session_name} </li>
	 *			<li><b>params:</b> {@link $params}</li>
	 *			<li><b>type:</b> {@link $type}</li>
	 *			<li><b>width:</b> {@link $width}</li>
	 *			<li><b>height:</b> {@link $height}</li>
	 *			<li><b>bgcolor:</b> {@link $bgcolor}</li>
	 *			<li><b>fonttype:</b> {@link $fonttype}</li>
	 *			<li><b>bgintensity:</b> {@link $bgintensity}</li>
	 *			<li><b>fgintensity:</b> {@link $fgintensity}</li>
	 *			<li><b>fontsize:</b> {@link $fontsize}</li>
	 *			<li><b>spacerange:</b> {@link $spacerange}</li>
	 *		</ul>
	 */
	protected function setProperties(&$options)
	{
		if (isset($options['session_name']))
		{
			$this->session_name = $options['session_name'];
		}
		if (isset($options['params']))
		{
			$this->params = $options['params'];
		}
		if (isset($options['type']))
		{
			$this->type = $options['type'];
		}
		if (isset($options['width']))
		{
			$this->width = (int)$options['width'];
		}
		if (isset($options['height']))
		{
			$this->height = $options['height'];
		}
		if (isset($options['bgcolor']))
		{
			$this->bgcolor = $options['bgcolor'];
		}
		if (isset($options['fonttype']))
		{
			$this->fonttype = $options['fonttype'];
		}
		else
		{
			$this->fonttype = dirname(__FILE__).'/fonts/arial.ttf';
		}
		if (isset($options['bgintensity']))
		{
			$this->bgintensity = (int)$options['bgintensity'];
		}
		if (isset($options['fgintensity']))
		{
			$this->fgintensity = (int)$options['fgintensity'];
		}
		if (isset($options['fontsize']))
		{
			$this->fontsize = (int)$options['fontsize'];
		}
		if (isset($options['spacerange']) and
				is_array($options['spacerange']) and count($options['spacerange']))
		{
			$this->spacerange[0] = (int)array_shift($options['spacerange']);
			$this->spacerange[1] = count($options['spacerange'])
				? array_shift($options['spacerange'])
				: $this->spacerange[0];

		}

	}

	/**
	 * Kép kimenetre küldése.
	 *
	 * @param bool $bool ha true, akkor nem küld Content-type header-t.
	 *			Ez a {@link __toString()} metódusnál lényeges.
	 */
	public function flush($bool=false)
	{
		//kép típus beállítása
		$type = strtolower($this->type);
		if($type == 'jpg') { $type = 'jpeg'; }
		if($type != 'jpeg' and $type != 'gif' and $type != 'png') {
			$type = 'jpeg';
		}
		//kép létrehozása
		$this->source = imageCreateTrueColor($this->width,$this->height);
		$this->type = $type;
		$color = explode(',',$this->bgcolor);
		$this->setBackground($color[0],$color[1],$color[2]);

		$this->randomBg($this->bgintensity);

		$_SESSION[$this->session_name] = $this->text = $this->codeGenerator($this->fontsize);

		$this->randomBg($this->fgintensity);

		$create_image = 'image'.$this->type;

		//kép típus fejléce
		if (!$bool)
		{
			header("Content-type: image/$type");
		}
		$create_image($this->source);
	}

	/**
	 *
	 * @param mixed $bgcolor Ha a második két paraméter is meg van adva,
	 *			akkor az RGB színösszetevők vörös komponense. egyébként
	 *			2 formátum engedélyezett.
	 *			<ul>
	 *				<li><b>Decimális:</b> R,G,B</li>
	 *				<li><b>Hexa:</b> #RGB</li>
	 *			</ul>
	 *
	 * @param int $greenc RGB zöld komponense (decimális)
	 * @param int $bluec RGB kék komponense (decimális)
	 */
	protected function setBackground($bgcolor,$greenc=null,$bluec=null)
	{
		//ha mind a három paraméter meg van adva
		if(!is_null($bluec) and !is_null($greenc)) {
			$red = $bgcolor;
			$green = $greenc;
			$blue = $bluec;
		//ha csak az első paraméter van megadva
		} else {
			//akkor ha # jellel kezdődik
			if(substr($bgcolor,0,1) == '#') {
				//hexadecimális formátumnak tekinti.
				//felbontja 3 részre és decimálisba váltja a részeket
				$red_hex = substr($bgcolor,1,2);
				$green_hex = substr($bgcolor,3,2);
				$blue_hex = substr($bgcolor,5,2);
				$red = hexdec($red_hex);
				$green = hexdec($green_hex);
				$blue = hexdec($blue_hex);
			//egyébként
			} else {
				//vesszök mentén 3 részre vágja a színt (rgb)
				$bgcolor = explode(',',$bgcolor);
				$red = $bgcolor[0];
				$green = $bgcolor[1];
				$blue = $bgcolor[2];
			}
		}
		//háttér szín generálása
		$bgcolor = imageColorAllocate($this->source,$red,$green,$blue);
		//szín kitöltés
		imagefill($this->source,1,1,$bgcolor);
		$this->_bgcolor = $bgcolor;
	}

	/**
	 * Random kép torzítás
	 *
	 * @param int $intensity Torzítás erőssége
	 */
	protected function randomBg($intensity)
	{
		//torzítás
		for($i=1;$i<=$intensity;$i++) {
			$func = mt_rand(1,2);
			//ha a $func 1
			if($func == 1) {
				//akkor elipsziseket rajzol a háttérbe
				//elipszis közepének X koordinátája
				$cx = mt_rand(1,$this->width);
				//elipszis közepének Y koordinátája
				$cy = mt_rand(1,$this->height);
				//szélessége
				$width = mt_rand(1,$this->width);
				//magassága
				$height = mt_rand(1,$this->height);
				//elipszis színének random választása
				$ellipse_red = mt_rand(0,255);
				$ellipse_green = mt_rand(0,255);
				$ellipse_blue = mt_rand(0,255);
				$color = imageColorAllocate($this->source,$ellipse_red,$ellipse_green,$ellipse_blue);
				//elipszis kirajzolása
				imageellipse($this->source,$cx,$cy,$width,$height,$color);
			//de ha a $func nem 1
			} else {
				//akkor vonalakat rajzol
				//színek ranfom választása
				$line_blue = mt_rand(0,255);
				$line_green = mt_rand(0,255);
				$line_red = mt_rand(0,255);
				$color = imageColorAllocate($this->source,$line_red,$line_green,$line_blue);
				//koordináták
				$x1 = mt_rand(1,$this->width);
				$x2 = mt_rand(1,$this->width);
				$y1 = mt_rand(1,$this->height);
				$y2 = mt_rand(1,$this->height);
				//vonal rajzolása
				imageline($this->source,$x1,$x2,$y1,$y2,$color);
			}
		}
	}

	/**
	 * Captcha kód generálása a képre
	 *
	 * @param int $fontsize Betűméret
	 * @return string A szükséges válasz {@link $text}
	 */
	protected function codeGenerator($fontsize)
	{
		if (!file_exists($this->fonttype))
		{
			exit('<b>'.$this->fonttype.'</b> not found!');
		}
		
		/*
		 * freeweb.hu szolgáltató miatt került be ez a sor. E nélkül
		 * ott nem jelennek meg a truetype fontok. 
		 */
		imagettftext($this->source, 0, 0, 0, 0, 0, $this->fonttype, '');
		
		//ha a generált szám 0
		if(mt_rand(0,1) == 0) {
			//akkor karaktersorozatot generál

			//generálható karakterek listája
			$chars = array_merge(range('A','Z'),range(0,9));
			shuffle($chars);
			//karakterek generálása
			$keys = array_rand($chars,7);
			foreach($keys as $key=>$value) {
				$text[] = $chars[$value];
			}

			$max_height = 0;

			$maxleftx = 0;
			$rightx = 0;
			$space = 0;
			$textLength = count($text);
			$angles = array_rand(array_fill(0,200,''),$textLength);

			//karakterek generálása ciklusban
			for($i=0;$i< $textLength;$i++) {
				//karakter szögelfordulása
				$angles[$i] = $angles[$i] % 20;//mt_rand(1,20);
				//ha páros, akkor negatívra állítja
				if($angles[$i] % 2 == 0) { $angles[$i] = 0-$angles[$i]; }
				//karakter által elfoglalt terület koordinátái
				$ttfbox[$i] = imagettfbbox($fontsize,$angles[$i],$this->fonttype,$text[$i]);
				//karakter magassága
				$height = abs($ttfbox[$i][1]) + abs($ttfbox[$i][7]);
				$leftx = max(abs($ttfbox[$i][0]),abs($ttfbox[$i][6]));
				$maxleftx += $leftx+$rightx+$space;
				$rightx = max(abs($ttfbox[$i][2]),abs($ttfbox[$i][4]));
				//karakter X koordinátájának megadása
				$x[$i] = $maxleftx;
				//betüköz megadása ha nem az utolsó betűrúl van szó
				$space = (($i < $textLength-1)  ? mt_rand($this->spacerange[0],$this->spacerange[1]) : 0);
				//legmagasabb betű meghatározása
				$max_height = ($height > $max_height) ? $height : $max_height;
			}
			//betük középre igazítása függőlegesen
			$y = (($this->height - $max_height) / 2) + $max_height;
			//karaktersorozat középre igazítása vizszintesen
			$offset = ($this->width - (end($x)+$rightx)) / 2;
			//karakterek kiirása
			for($i=0; $i<$textLength;$i++) {
				//véletlen színgenerálás
				$color = imageColorAllocate($this->source,mt_rand(0,255),255-mt_rand(170,255),255);
				//szöveg kiirása
				imagettftext($this->source,$this->fontsize,$angles[$i],$offset+$x[$i],$y,$color,$this->fonttype,$text[$i]);
			}
			//visszaadja a kiiírt szöveget
			return strtolower(implode('',$text));
		//ha a generált szám nem 0
		} else {
			//akkor számolni kell

			//első operandus
			$operandus1 = mt_rand(40,300);
			//müveletek listája
			$operators = array('x'=>'0','+'=>'1','-'=>'2');
			//művelet meghatározása
			$operator = array_rand($operators,1);
			//ha a művelet szorzás,
			if($operator == 'x') {
				//és az első operandus 100-nál kisebb
				if($operandus1 < 100) {
					//a második operandus max
					$maxop2 = 2;
				//egyébként
				} else {
					//második operandus max
					$maxop2 = 1;
				}
			//de ha összeadás van
			} else if($operator == '+') {
				//a második operandus lehet 10 is
				$maxop2 = 10;
			//kivonásnál
			} else if($operator == '-') {
				//a második operandus max 5 lehet
				$maxop2 = 5;
			}
			//második operandus megadása
			$operandus2 = mt_rand(1,$maxop2);

			//szorzás
			if($operator == 'x') {
				$eredmeny = $operandus1 * $operandus2;
			//összeadás
			} else if($operator == '+') {
				$eredmeny = $operandus1 + $operandus2;
			//kivonás
			} else if($operator == '-') {
				$eredmeny = $operandus1 - $operandus2;
			}

			//a megjelenítendő szöveg összerakása
			$text = $operandus1 .' '. $operator .' '. $operandus2 .' = ? ';
			//befoglaló téglalap koordinátái
			$ttfbox = imagettfbbox($fontsize,0,$this->fonttype,$text);
			//középre állítás vizszintesen
			$x = (($this->width - (abs($ttfbox[0]) + abs($ttfbox[2])) ) / 2 ) + abs($ttfbox[0]);
			//középre állítás függőlegesen
			$y = (($this->height - (abs($ttfbox[1]) + abs($ttfbox[7])) ) / 2) + abs($ttfbox[7]);
			//szín
			$color = imageColorAllocate($this->source,255,0,255);
			//szöveg megjelenítése
			imagettftext($this->source,$this->fontsize,0,$x,$y,$color,$this->fonttype,$text);
			//visszaadja a beirandó eredményt
			return (string)$eredmeny;
		}
	}

	/**
	 * A kép html img tagba helyezve base64 encode-olással.
	 *
	 * @return string
	 */
	public function __toString()
	{
		ob_start();
		$this->flush(true);
		$src = ob_get_clean();
		$params = '';
		foreach ($this->params as $key=>&$param)
		{
			$params .= $key.'="'.$param.'" ';
		}
		return '<img src="data:image/'.$this->type .';base64,'.base64_encode($src).'" '.$params .' />';
	}
}
?>
