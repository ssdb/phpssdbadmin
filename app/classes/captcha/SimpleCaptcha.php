<?php
/**
 * Script para la generación de CAPTCHAS
 *
 * @author  Jose Rodriguez <jose.rodriguez@exec.cl>
 * @license GPLv3
 * @link    http://code.google.com/p/cool-php-captcha
 * @package captcha
 * @version 0.3
 *
 */
/**
 * SimpleCaptcha class
 *
 */
class SimpleCaptcha {

	/** Width of the image */
	public $width  = 200;

	/** Height of the image */
	public $height = 70;

	/**
	 * Path for resource files (fonts, words, etc.)
	 *
	 * "resources" by default. For security reasons, is better move this
	 * directory to another location outise the web server
	 *
	 */
	public $resourcesPath = '';

	/** Min word length (for non-dictionary random text generation) */
	public $minWordLength = 5;

	/**
	 * Max word length (for non-dictionary random text generation)
	 * 
	 * Used for dictionary words indicating the word-length
	 * for font-size modification purposes
	 */
	public $maxWordLength = 8;

	/** Background color in RGB-array */
	public $backgroundColor = array(255, 255, 255);

	/** Foreground colors in RGB-array */
	public $colors = array(
			array(27,78,161), // blue
			array(22,163,35), // green
			array(200,36,7),  // red
			array(100,100,100),  // red
			);

	/** Shadow color in RGB-array or null */
	public $shadowColor = null; //array(0, 0, 0);

	/**
	 * Font configuration
	 *
	 * - font: TTF file
	 * - spacing: relative pixel space between character
	 * - minSize: min font size
	 * - maxSize: max font size
	 */
	public $fonts = array(
			'Antykwa'  => array('spacing' => -3, 'minSize' => 27, 'maxSize' => 30, 'font' => 'AntykwaBold.ttf'),
			'DingDong' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 30, 'font' => 'Ding-DongDaddyO.ttf'),
			'Duality'  => array('spacing' => -2, 'minSize' => 30, 'maxSize' => 38, 'font' => 'Duality.ttf'),
			'Jura'     => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 32, 'font' => 'Jura.ttf'),
			'Times'    => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 34, 'font' => 'TimesNewRomanBold.ttf'),
			'VeraSans' => array('spacing' => -1, 'minSize' => 20, 'maxSize' => 28, 'font' => 'VeraSansBold.ttf'),
			);

	/** Wave configuracion in X and Y axes */
	public $Yperiod    = 12;
	public $Yamplitude = 14;
	public $Xperiod    = 11;
	public $Xamplitude = 5;

	/** letter rotation clockwise */
	public $maxRotation = 8;

	/**
	 * Internal image size factor (for better image quality)
	 * 1: low, 2: medium, 3: high
	 */
	public $scale = 2;

	/** 
	 * Blur effect for better image quality (but slower image processing).
	 * Better image results with scale=3
	 */
	public $blur = false;

	/** Debug? */
	public $debug = false;

	/** Image format: jpeg or png */
	public $imageFormat = 'jpeg';


	/** GD image */
	public $im;


	public function __construct($config = array()) {
		$this->resourcesPath = dirname(__FILE__) . '/resources';
	}
	
	private $text = '';
	
	function setText($text){
		$this->text = $text;
	}
	
	function getText($len=4){
		if($this->text === ''){
			$this->text = $this->GetCaptchaText($len);
		}
		return $this->text;
	}

	public function CreateImage() {
		$ini = microtime(true);

		/** Initialization */
		$this->ImageAllocate();

		/** Text insertion */
		$fontcfg  = $this->fonts[array_rand($this->fonts)];
		$text = $this->getText();
		$this->WriteText($text, $fontcfg);

		/** Transformations */
		#$this->WaveImage();
		if ($this->blur && function_exists('imagefilter')) {
			imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
		}
		$this->ReduceImage();

		
		$width = $this->width;
		$height = $this->height;
		// 画干扰线
		imagesetthickness($this->im, 3);
		for($i = 0;$i < 5;$i++) {
			$color = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
			$color = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
			imagearc($this->im, mt_rand(-$width, $width), mt_rand(- $height, $height), mt_rand(30, $width * 2), mt_rand(20, $height * 2), mt_rand(0, 360), mt_rand(0, 360), $color);
		}
		// 画干扰点
		for($i = 0;$i < 50;$i++) {
			$color = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
			$color = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
			imagesetpixel($this->im, mt_rand(0, $width), mt_rand(0, $height), $color);
		} 

		if ($this->debug) {
			imagestring($this->im, 1, 1, $this->height-8,
					"$text {$fontcfg['font']} ".round((microtime(true)-$ini)*1000)."ms",
					$this->GdFgColor
					);
		}


		/** Output */
		$this->WriteImage();
		$this->Cleanup();
	}

	/**
	 * Creates the image resources
	 */
	protected function ImageAllocate() {
		// Cleanup
		if (!empty($this->im)) {
			imagedestroy($this->im);
		}

		$this->im = imagecreatetruecolor($this->width*$this->scale, $this->height*$this->scale);

		// Background color
		$this->GdBgColor = imagecolorallocate($this->im,
				$this->backgroundColor[0],
				$this->backgroundColor[1],
				$this->backgroundColor[2]
				);
		imagefilledrectangle($this->im, 0, 0, $this->width*$this->scale, $this->height*$this->scale, $this->GdBgColor);

		// Foreground color
		$color           = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
		$this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

		// Shadow color
		if (!empty($this->shadowColor) && is_array($this->shadowColor) && sizeof($this->shadowColor) >= 3) {
			$this->GdShadowColor = imagecolorallocate($this->im,
					$this->shadowColor[0],
					$this->shadowColor[1],
					$this->shadowColor[2]
					);
		}
	}

	/**
	 * Text generation
	 *
	 * @return string Text
	 */
	protected function GetCaptchaText($len) {
		$chars = '123456789ABCdEfGhijKLmNpQrStuvwxY';
		$n = strlen($chars) -  1;
		$text = '';
		for($i=0; $i<$len; $i++){
			$text .= $chars[mt_rand(0, $n)];
		}
		return $text;
	}

	/**
	 * Text insertion
	 */
	protected function WriteText($text, $fontcfg = array()) {
		if (empty($fontcfg)) {
			// Select the font configuration
			$fontcfg  = $this->fonts[array_rand($this->fonts)];
		}

		// Full path of font file
		$fontfile = $this->resourcesPath.'/fonts/'.$fontcfg['font'];


		/** Increase font-size for shortest words: 9% for each glyp missing */
		$lettersMissing = $this->maxWordLength-strlen($text);
		$fontSizefactor = 1+($lettersMissing*0.09);

		// Text generation (char by char)
		$x      = 20*$this->scale;
		$y      = round(($this->height*27/40)*$this->scale);
		$length = strlen($text);
		for ($i=0; $i<$length; $i++) {
			$color = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
			$color = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

			$degree   = rand($this->maxRotation*-1, $this->maxRotation);
			$fontsize = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->scale*$fontSizefactor;
			$letter   = substr($text, $i, 1);

			if ($this->shadowColor) {
				$coords = imagettftext($this->im, $fontsize, $degree,
						$x+$this->scale, $y+$this->scale,
						$this->GdShadowColor, $fontfile, $letter);
			}
			$coords = imagettftext($this->im, $fontsize, $degree,
					$x, $y,
					$color, $fontfile, $letter);
			$x += ($coords[2]-$x) + ($fontcfg['spacing']*$this->scale);
		}
	}

	/**
	 * Wave filter
	 */
	protected function WaveImage() {
		// X-axis wave generation
		$xp = $this->scale*$this->Xperiod*rand(1,3);
		$k = rand(0, 100);
		for ($i = 0; $i < ($this->width*$this->scale); $i++) {
			imagecopy($this->im, $this->im,
					$i-1, sin($k+$i/$xp) * ($this->scale*$this->Xamplitude),
					$i, 0, 1, $this->height*$this->scale);
		}

		// Y-axis wave generation
		$k = rand(0, 100);
		$yp = $this->scale*$this->Yperiod*rand(1,2);
		for ($i = 0; $i < ($this->height*$this->scale); $i++) {
			imagecopy($this->im, $this->im,
					sin($k+$i/$yp) * ($this->scale*$this->Yamplitude), $i-1,
					0, $i, $this->width*$this->scale, 1);
		}
	}

	/**
	 * Reduce the image to the final size
	 */
	protected function ReduceImage() {
		// Reduzco el tamaño de la imagen
		$imResampled = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($imResampled, $this->im,
				0, 0, 0, 0,
				$this->width, $this->height,
				$this->width*$this->scale, $this->height*$this->scale
				);
		imagedestroy($this->im);
		$this->im = $imResampled;
	}

	/**
	 * File generation
	 */
	protected function WriteImage() {
		if ($this->imageFormat == 'png' && function_exists('imagepng')) {
			header("Content-type: image/png");
			imagepng($this->im);
		} else {
			header("Content-type: image/jpeg");
			imagejpeg($this->im, null, 80);
		}
	}

	/**
	 * Cleanup
	 */
	protected function Cleanup() {
		imagedestroy($this->im);
	}
}
