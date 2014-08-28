<?php
include(dirname(__FILE__) . '/Mysql.php');

class Db{
	private static $config = array();

	function __construct(){
		throw new Exception("Static class");
	}

	static function init($config=array()){
		self::$config = $config;
	}

	static function instance(){
		static $db = null;
		if($db === null){
			$db = new Mysql(self::$config);
		}
		return $db;
	}
	
	static function get_num($sql){
		$result = self::query($sql);
		if($row = mysql_fetch_array($result)){
			return (int)$row[0];
		}else{
			return 0;
		}
	}
	
	static function update($sql){
		self::query($sql);
		return self::instance()->affected_rows();
	}
	
	static function escape($val){
		return self::instance()->escape($val);
	}
	
	static function escape_like_string($val){
		return self::instance()->escape_like_string($val);
	}

	static function __callStatic($cmd, $params=array()){
		return call_user_func_array(array(self::instance(), $cmd), $params);
	}
}
