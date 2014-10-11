<?php
class App{
	static $env;
	static $context;
	static $controller;
	static $finish = false;
	static $config = array();
	
	static function init(){
		$config_file = APP_PATH . '/config/config.php';
		if(!file_exists($config_file)){
			throw new Exception("No config file");
		}
		$config = include($config_file);

		self::$config = $config;
		self::$env = $config['env'];
		#self::$context = new stdClass();
		self::$context = new Context();

		Logger::init($config['logger']);
		if($config['db']){
			Db::init($config['db']);
		}

		if(get_magic_quotes_gpc()){
			foreach($_GET as $k=>$v){
				$_GET[$k] = Text::stripslashes($v);
			}
			foreach($_POST as $k=>$v){
				$_POST[$k] = Text::stripslashes($v);
			}
			foreach($_COOKIE as $k=>$v){
				$_COOKIE[$k] = Text::stripslashes($v);
			}
		}
		$_REQUEST = $_GET + $_POST + $_COOKIE;
	}
	
	static function run(){
		$code = 1;
		$msg = '';
		$data = null;

		App::init();
		try{
			$data = self::execute();
		}catch(AppBreakException $e){
			return;
		}catch(Exception $e){
			if(App::$controller && App::$controller->is_ajax){
				$code = $e->getCode();
				$msg = $e->getMessage();
				if(!strlen($msg)){
					$msg = 'error';
				}
			}else{
				return self::error_handle($e);
			}
		}
		
		if(App::$controller && App::$controller->is_ajax){
			$resp = array(
				'code' => $code,
				'message' => $msg,
				'data' => $data,
			);
			if(defined('JSON_UNESCAPED_UNICODE')){
				$json = json_encode($resp, JSON_UNESCAPED_UNICODE);
			}else{
				$json = json_encode($resp);
			}
			$jp = App::$controller->jp;
			if(!preg_match('/^[a-z0-9_]+$/i', $jp)){
				$jp = false;
			}
			if($jp){
				echo "$jp($json);";
			}else{
				echo $json;
			}
		}else{
			$layout = find_layout_file();
			if($layout){
				$params = App::$context->as_array();
				extract($params);
				include($layout);
			}else{
				_view();
			}
		}
	}

	private static function execute(){
		$route = route();
		list($base, $controller, $action) = $route;
		App::$controller = $controller;

		$controller->init(App::$context);
		if(self::$finish){
			return null;
		}
		$ret = $controller->$action(App::$context);
		return $ret;
	}
	
	static function _break(){
		self::$finish = true;
		throw new AppBreakException();
	}
	
	private static function find_error_page($code){
		$pages = array($code, 'default');
		if(App::$controller){
			$view_path_list = App::$controller->view_path;
		}else{
			$view_path_list = array('views');
		}

		$path = base_path();
		foreach($view_path_list as $view_path){
			$ps = explode('/', $path);
			while(1){
				$base = join('/', $ps);
				if($ps){
					$dir = APP_PATH . "/$view_path/$base";
				}else{
					$dir = APP_PATH . "/$view_path";
				}

				foreach($pages as $page){
					$file = "$dir/_error/{$page}.tpl.php";
					#echo $file . "\n<br/>";
					if(file_exists($file)){
						return $file;
					}
				}
				
				if(!$ps){
					break;
				}
				array_pop($ps);
			}
		}
		return false;
	}
	
	static function error_handle($e){
		$code = $e->getCode() === 0? 500 : $e->getCode();
		if($code == 404){
			header('Content-Type: text/html; charset=utf-8', true, 404);
		}else if($code == 200){
			//
		}else{
			header('Content-Type: text/html; charset=utf-8', true, 500);
		}
		$error_page = self::find_error_page($code);
		if($error_page !== false){
			$params = App::$context->as_array();
			$params['_e'] = $e;
			extract($params);
			include($error_page);
			return;
		}
		
		$msg = htmlspecialchars($e->getMessage());
		$html = '';
		$html .= '<html><head>';
		$html .= '<meta charset="UTF-8">';
		$html .= "<title>$msg</title>\n";
		$html .= "<style>body{font-size: 14px; font-family: monospace;}</style>\n";
		$html .= "</head><body>\n";
		$html .= "<h1 style=\"text-align: center;\">$msg</h1>";
		if(self::$env == 'dev'){
			$ts = $e->getTrace();
			foreach($ts as $t){
				$html .= "{$t['file']}:{$t['line']} {$t['function']}()<br/>\n";
			}
		}
		$html .= '<p style="
			margin-top: 20px;
			padding-top: 10px;
			border-top: 1px solid #ccc;
			text-align: center;">iphp</p>';
		$html .= '</body></html>';
		echo "$html\n";
	}
}

class AppBreakException extends Exception
{
	function __construct($msg='', $code=1){
		parent::__construct($msg, $code);
	}
}

class App404Exception extends Exception
{
	function __construct($msg='404 Not found'){
		parent::__construct($msg, 404);
	}
}
