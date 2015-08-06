<?php
class LoginController extends Controller
{
	function init($ctx){
		session_start();
		$ctx->user =$_SESSION['login_user'];
		if($ctx->user){
			_redirect('/');
		}
	}

	function index($ctx){
		$conf = App::$config['login'];
		if(strlen($conf['password']) < 6 || $conf['password'] == '12345678'){
			$ctx->errmsg = 'Password is not configured strong enough, you can not login';
			return;
		}
		if($_POST){
			session_start();
			$vcode = strtolower(htmlspecialchars($_POST['verify_code']));
			if(!$vcode || $vcode !== strtolower($_SESSION['verify_code'])){
				$ctx->errmsg = 'Wrong captcha code';
				return;
			}

			$name = htmlspecialchars(trim($_POST['name']));
			$password = htmlspecialchars(trim($_POST['password']));
			if($name === $conf['name'] && $password === $conf['password']){
				$_SESSION['login_user'] = 1;
				_redirect('/');
				return;
			}else{
				$ctx->errmsg = "Wrong username or password!";
			}
		}
	}
}
