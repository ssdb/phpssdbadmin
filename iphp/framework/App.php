<?php
class App{
	static $env;
	static $context;
	static $controller;
	static $finish = false;
	static $config = array();
	
	static function init($config){
		self::$config = $config;
		self::$env = $config['env'];
		self::$context = new stdClass();
	}
	
	static function run(){
		try{
			return self::_run();
		}catch(Exception $e){
			if($e->getCode() == 404){
				header('Content-Type: text/html; charset=utf-8', true, 404);
			}else{
				header('Content-Type: text/html; charset=utf-8', true, 500);
			}
			self::print_error($e);
		}
	}
	
	static function print_error($e){
		echo '<html><head>';
		echo '<meta charset="UTF-8">';
		echo "<title>" . $e->getMessage() . "</title>\n";
		echo "<style>body{font-size: 14px; font-family: monospace;}</style>\n";
		echo "</head><body>\n";
		echo "<h1 style=\"text-align: center;\">" . $e->getMessage() . "</h1>";
		if(self::$env == 'dev'){
			$ts = $e->getTrace();
			foreach($ts as $t){
				echo "{$t['file']}:{$t['line']} {$t['function']}()<br/>\n";
			}
		}
		echo '<p style="
			margin-top: 20px;
			padding-top: 10px;
			border-top: 1px solid #ccc;
			text-align: center;">iphp</p>';
		echo '</body></html>';
	}

	static function _run(){
		$config_file = APP_PATH . '/config/config.php';
		if(!file_exists($config_file)){
			throw new Exception("No config file");
		}
		$config = include($config_file);
		App::init($config);
		Logger::init($config['logger']);
		if($config['db']){
			Db::init($config['db']);
		}

		$route = route();
		list($base, $controller, $action) = $route;
		App::$controller = $controller;

		$controller->init(App::$context);
		if(self::$finish){
			return;
		}
		$ret = $controller->$action(App::$context);
		if(!$ret){
			$layout = find_layout_file();
			if($layout){
				foreach(App::$context as $k=>$v){
					$$k = $v;
				}
				include($layout);
			}else{
				_view();
			}
		}
	}

}
