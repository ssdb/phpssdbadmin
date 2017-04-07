<?php
class CaptchaController extends Controller
{
	function index($ctx){
		$this->is_ajax = false;
		$this->layout = false;
		require_once(APP_PATH . '/classes/captcha/SimpleCaptcha.php');
		$captcha = new SimpleCaptcha();
		$captcha->width = 140;
		$captcha->height = 60;
		$captcha->scale = 4;
		$captcha->blur = true;

		// OPTIONAL Change configuration...
		//$captcha->imageFormat = 'png';
		//$captcha->resourcesPath = "/var/cool-php-captcha/resources";

		$token = null;
		if(isset($_GET['token']) && $_GET['token']){
			$token = $_GET['token'];
		}
		if($token){
			$code = SafeUtil::get_captcha(htmlspecialchars($_GET['token']));
			$captcha->setText($code);
		}else{
			$code = $captcha->getText();
		}
		if(strlen($code)){
			SafeUtil::set_captcha($code, 300, $token);
			$captcha->CreateImage();
		}
	}
}
