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
	
	static function begin(){
		return self::instance()->begin();
	}
	
	static function commit(){
		return self::instance()->commit();
	}
	
	static function rollback(){
		return self::instance()->rollback();
	}
	
	static function query($sql){
		return self::instance()->query($sql);
	}
	
	static function get_num($sql){
		$result = self::query($sql);
		if($row = mysql_fetch_array($result)){
			return (int)$row[0];
		}else{
			return 0;
		}
	}
	
	static function last_insert_id(){
		return self::instance()->last_insert_id();
	}
}
