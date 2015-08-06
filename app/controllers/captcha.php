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

		$code = $captcha->getText();
		session_start();
		$_SESSION['verify_code'] = $code;
		$captcha->CreateImage();
	}
}
