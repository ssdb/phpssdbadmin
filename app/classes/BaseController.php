<?php
class BaseController extends Controller
{
	protected $ssdb;
	
	function init($ctx){
		session_start();
		$ctx->user =$_SESSION['login_user'];
		if(!$ctx->user){
			_redirect('login');
			return;
		}
		
		$servers = array();
		if(isset(App::$config['ssdb'])){
			$servers[] = App::$config['ssdb'];
		}
		if(isset(App::$config['servers'])){
			$servers = array_merge($servers, App::$config['servers']);
		}
		if(!$servers){
			_throw("No servers config!");
		}
		$confs = array();
		foreach($servers as $s){
			$k = "{$s['host']}:{$s['port']}";
			$confs[$k] = $s;
		}
		
		if(isset($_GET['PHPSSDBADMIN_SERVER'])){
			$conf_k = $_GET['PHPSSDBADMIN_SERVER'];
			setcookie('PHPSSDBADMIN_SERVER', $conf_k, time()+86400*300, '/'/*, $domail*/);
			$_COOKIE['PHPSSDBADMIN_SERVER'] = $conf_k;
		}
		$conf_k = $_COOKIE['PHPSSDBADMIN_SERVER'];
		if(isset($confs[$conf_k])){
			$conf = $confs[$conf_k];
		}else{
			$conf = $servers[0];
		}
		$ctx->conf = $conf;
		
		$ctx->conf_k = $conf_k;
		$ctx->confs = $confs;

		try{
			$this->ssdb = new SimpleSSDB($conf['host'], $conf['port']);
		}catch(Exception $e){
			_throw("SSDB error: " . $e->getMessage());
		}
		
		if(!empty($conf['password'])) {
			$this->ssdb->auth($conf['password']);
		}
		
		$req = $_GET + $_POST;
		if(isset($req['size'])){
			$ctx->size = intval($req['size']);
			if($ctx->size > 0){
				setcookie('psa_size', $ctx->size, time() + 86400 * 30, '/');
				$_COOKIE['psa_size'] = $ctx->size;
			}
		}
		if(isset($_COOKIE['psa_size'])){
			$ctx->size = intval($_COOKIE['psa_size']);
		}else{
			$ctx->size = 0;
		}
		if($ctx->size <= 0){
			$ctx->size = 10;
		}
	}
}
