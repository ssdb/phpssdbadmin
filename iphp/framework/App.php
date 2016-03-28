<?php
class App{
	static $env;
	static $context;
	static $controller;
	static $finish = false;
	static $config = array();
	static $version = '';
	static $asset_md5 = array();
	static $base_url = null;

	// view 的渲染结果先保存在此变量中
	static $view_content = '';

	static function host(){
		$host = $_SERVER['HTTP_HOST'];
		$port = $_SERVER['SERVER_PORT'];
		if(strpos($host, ':') === false && $port != 80 && $port != 443){
			$host .= ":{$port}";
		}
		return $host;
	}
	
	static function set_base_url($base_url){
		$base_url = rtrim($base_url, '/');
		self::$base_url = $base_url;
	}

	static function init(){
		$md5_file = APP_PATH . '/../assets.json';
		if(file_exists($md5_file)){
			self::$asset_md5 = @json_decode(@file_get_contents($md5_file), true);
			if(!is_array(self::$asset_md5)){
				self::$asset_md5 = array();
			}
		}else{
			$version_file = APP_PATH . '/../version';
			if(file_exists($version_file)){
				self::$version = trim(@file_get_contents($version_file));
			}
		}
		
		$config_file = APP_PATH . '/config/config.php';
		if(!file_exists($config_file)){
			throw new Exception("No config file");
		}
		$config = include($config_file);

		self::$config = $config;
		self::$env = $config['env'];

		Logger::init($config['logger']);
		if(isset($config['db'])){
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
		// before any exception
		self::$context = new Context();

		$code = 1;
		$msg = '';
		$data = null;
		
		try{
			$data = self::_run();
		}catch(AppBreakException $e){
			return;
		}catch(Exception $e){
			ob_clean();
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
			#var_dump(find_view_and_layout());
			list($__view, $__layout) = find_view_and_layout();
			if(!$__view){
				Logger::trace("No view for " . base_path());
			}else{
				Logger::trace("View $__view");
				$params = App::$context->as_array();
				extract($params);
				ob_start();
				include($__view);
				self::$view_content = ob_get_clean();
			}
			
			if($__layout){
				Logger::trace("Layout $__layout");
				$params = App::$context->as_array();
				extract($params);
				include($__layout);
			}else{
				if(App::$controller->layout !== false){
					Logger::error("No layout for " . base_path());
				}
				_view();
			}
		}
	}
	
	static function _run(){
		if(base_path() == 'index.php'){
			_redirect('');
		}

		ob_start();
		App::init();
		ob_clean();

		$data = self::execute();
		return $data;
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
		$code = $e->getCode() === 0? 200 : $e->getCode();
		if($code == 404){
			App::$controller = new Controller();
			header('Content-Type: text/html; charset=utf-8', true, 404);
		}else if($code == 403){
			header('Content-Type: text/html; charset=utf-8', true, 403);
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
			try{
				include($error_page);
				return;
			}catch(Exception $e){
				//
			}
		}
		
		$msg = htmlspecialchars($e->getMessage());
		$html = '';
		$html .= '<html><head>';
		$html .= '<meta charset="UTF-8"/>';
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
