<?php
define('APP_TIME_START', microtime(true));

include_once(dirname(__FILE__) . '/functions.php');

$AUTOLOAD_PATH =  array(
	dirname(__FILE__) . '/framework',
	APP_PATH . '/models',
	APP_PATH . '/classes',
);

function __autoload($class_name){
	global $AUTOLOAD_PATH;
	foreach($AUTOLOAD_PATH as $dir){
		$file = $dir . '/' . $class_name . '.php';
		if(file_exists($file)){
			require_once($file);
		}
	}
}

function include_paths(){
	static $paths = array();
	if(!$paths){
		$path = base_path();
		if(strlen($path) == 0){
			$ps = array('index');
		}else{
			$ps = explode('/', $path);
		}
		$act = $ps[count($ps) - 1];
		if($act == 'new'){
			$act = 'create';
		}
		$paths[] = array(
			'base' => join('/', array_slice($ps, 0, -1)),
			'action' => $act,
		);
		$paths[] = array(
			'base' => join('/', $ps),
			'action' => 'index',
		);
		if($act != 'index'){
			$paths[] = array(
				'base' => join('/', $ps) . '/index',
				'action' => 'index',
			);
			$paths[] = array(
				'base' => join('/', array_slice($ps, 0, -1)) . '/index',
				'action' => $act,
			);
		}
	}
	return $paths;
}

function load_controller($base, $action){
	$dir = APP_PATH . '/controllers/' . $base;
	$file = $dir . '.php';
	#echo join(', ', array($base, $action, $file)) . "\n";
	if(file_exists($file)){
		include($file);
		$ps = explode('/', $base);
		$controller = ucfirst($ps[count($ps) - 1]);
		$cls = "{$controller}Controller";
		$ins = new $cls();
		
		$found = false;
		if(method_exists($ins, $action)){
			$found = true;
		}
		if($found){
			Logger::trace("Controller: $file");
			return $ins;
		}
	}
	return false;
}

function route(){
	foreach(include_paths() as $path){
		$base = $path['base'];
		$action = $path['action'];
		$controller = load_controller($base, $action);
		if($controller){
			$controller->module = ($base == 'index')? '' : $base;
			break;
		}
	}
	if(!$controller){
		$path = base_path();
		Logger::trace("No route for $path!");
		throw new Exception("No route for $path!", 404);
	}
	return array($base, $controller, $action);
}

function find_layout_file(){
	$layout = 'layout';
	$path = base_path();
	if(App::$controller->layout === false){
		return false;
	}
	if(App::$controller->layout){
		$layout = App::$controller->layout;
	}
	foreach(App::$controller->view_path as $view_path){
		$ps = explode('/', $path);
		while(1){
			$base = join('/', $ps);
			$file = APP_PATH . "/$view_path/$base/$layout.tpl.php";
			if(file_exists($file)){
				Logger::trace("Layout: $file");
				return $file;
			}
			if(!$ps){
				break;
			}
			array_pop($ps);
		}
	}
	return false;
}

function find_view_file($base, $action){
	foreach(App::$controller->view_path as $view_path){
		$dir = rtrim(APP_PATH . "/$view_path/$base", '/');
		if($action == 'index'){
			$file = $dir . '.tpl.php';
		}else{
			$file = $dir . "/$action.tpl.php";
		}
		if(file_exists($file)){
			return $file;
		}
	}
	return false;
}


