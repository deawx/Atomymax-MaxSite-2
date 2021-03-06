<?php
require_once("../mainfile.php");
require_once("../includes/config.in.php");
require_once("../includes/class.mysql.php");
require_once("../includes/function.in.php");
require_once("../setconf.php");
session_start();
class CaptchaSecurityImages {
//	var $font = 'monofont.ttf';
var $font = WEB_PATH."/capcha/font/angsab.ttf";
	//var $font = WEB_PATH."/capcha/font/angsab.ttf";
	//var $font = '/home/www/htdocs/capcha/monofont.ttf'; //��Ǩ�ͺ path �ͧ�����Ф�Ѻ

	function generateCode($characters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '1234567890zxcvbnmasdfghjkqwertyop';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

	function CaptchaSecurityImages($width='80',$height='40',$characters='4') {
		$code = $this->generateCode($characters);
		/* font size will be 75% of the image height */
		$font_size = $height * 1.00;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 000, 255, 255);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[2])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		$_SESSION['security_code'] = $code;
	}

}

$width = isset($_GET['width']) ? $_GET['width'] : '80';
$height = isset($_GET['height']) ? $_GET['height'] : '40';
$characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '4';
$captcha = new CaptchaSecurityImages($width,$height,$characters);
//$captcha = new CaptchaSecurityImages($width,$height,tis620_to_utf8($characters));
//$captcha = new CaptchaSecurityImages($width,$height,iconv("TIS-620","UTF-8",$characters));
?>
