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
			$this->on_submit($ctx);
		}

		$ttl = 300;
		$encrypt = SafeUtil::create_encrypt_info();
		$token = SafeUtil::set_encrypt_info($encrypt, $ttl);
		$ctx->encrypt = array(
			'field_name' => SafeUtil::ENCRYPT_FIELD_NAME,
			'field_value' => $token,
			'public_key' => $encrypt['public_key'],
		);
	}
	
	private function on_submit($ctx){
		$conf = App::$config['login'];

		$req = array(
			'name' => $_POST['name'],
			'password' => $_POST['password'],
		);
		$req = SafeUtil::safe_decrypt($req);
		if(!$req){
			_throw("decrypt failed");
		}
		$name = htmlspecialchars(trim($req['name']));
		$password = htmlspecialchars(trim($req['password']));
		$ctx->name = $name;

		if(!SafeUtil::verify_captcha($_POST['verify_code'])){
			$ctx->errmsg = 'Wrong captcha code';
			return;
		}

		if($name === $conf['name'] && $password === $conf['password']){
			$_SESSION['login_user'] = 1;
			_redirect('/');
			return;
		}else{
			$ctx->errmsg = "Wrong username or password!";
		}
	}
}
