<?php
/**
 * MySQL 数据库操作工具类, 方便数据库操作.
 * 示例见底部注释.
 * @author: wuzuyang@gmail.com
 */
class Mysql_i{
	var $conn;
	var $query_list = array();
	public $query_count = 0;
	var $readonly = false;

	public function __construct($c){
		if(!isset($c['port'])){
			$c['port'] = '3306';
		}
		$this->conn = new mysqli($c['host'], $c['username'], $c['password'], $c['dbname'], $c['port']);
		if($this->conn->connect_error){
			throw new Exception('connect db error');
		}
		if($c['charset']){
			$this->conn->set_charset($c['charset']);
		}
	}
	
	public static function is_write_query($sql){
		$sql = trim($sql);
		if(strcasecmp($sql, 'begin') == 0){
			return true;
		}
        return (bool) @preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i', $sql);
	}

	/**
	 * 执行 mysqli_query 并返回其结果.
	 */
	public function query($sql){
		if($this->readonly){
			if(self::is_write_query($sql)){
				throw new Exception("write operation is not allowed on readonly db!");
			}
		}
		$stime = microtime(true);

		$result = $this->conn->query($sql);
		$this->query_count ++;
		if($result === false){
			Logger::error(mysqli_error($this->conn)." in SQL: $sql");
			if(defined('ENV') && ENV == 'dev'){
				throw new Exception(mysqli_error($this->conn)." in SQL: $sql");
			}else{
				throw new Exception('db error');
			}
		}

		$etime = microtime(true);
		$time = number_format(($etime - $stime) * 1000, 2);
		$ro = $this->readonly? '[RO]' : '[RW]';
		$log = "{$ro} {$time} $sql";
		if(defined('ENV') && ENV == 'dev'){
			$this->query_list[] = $log;
		}
		#Logger::debug($log);
		return $result;
	}
	
	function affected_rows(){
		return $this->conn->affected_rows;
	}

	/**
	 * 执行 SQL 语句, 返回结果的第一条记录(是一个对象).
	 */
	public function get($sql, $type='object'){
		$result = $this->query($sql);
		if($type == 'object'){
			$row = mysqli_fetch_object($result);
		}else{
			$row = mysqli_fetch_assoc($result);
		}
		if($row){
			return $row;
		}else{
			return null;
		}
	}

	/**
	 * 返回查询结果集, 以 key 为键组织成关联数组, 每一个元素是一个对象.
	 * 如果 key 为空, 则将结果组织成普通的数组.
	 */
	public function find($sql, $key=null, $type='object'){
		$data = array();
		$result = $this->query($sql);
		if($type == 'object'){
			while($row = mysqli_fetch_object($result)){
				if(!empty($key)){
					$data[$row->{$key}] = $row;
				}else{
					$data[] = $row;
				}
			}
		}else{
			while($row = mysqli_fetch_assoc($result)){
				if(!empty($key)){
					$data[$row[$key]] = $row;
				}else{
					$data[] = $row;
				}
			}
		}
		return $data;
	}

	public function last_insert_id(){
		return $this->conn->insert_id;
	}

	/**
	 * 执行一条带有结果集计数的 count SQL 语句, 并返该计数.
	 */
	public function count($sql){
		return $this->get_num($sql);
	}
	
	public function get_num($sql){
		$row = $this->get($sql, 'array');
		if($row){
			$row = array_values($row);
			return (int)$row[0];
		}else{
			return 0;
		}
	}

	/**
	 * 开始一个事务.
	 */
	public function begin(){
		return $this->conn->query("begin");
	}

	/**
	 * 提交一个事务.
	 */
	public function commit(){
		return $this->conn->query("commit");
	}

	/**
	 * 回滚一个事务.
	 */
	public function rollback(){
		return $this->conn->query("rollback");
	}


	/**
	 * 获取指定编号的记录.
	 * @param int $id 要获取的记录的编号.
	 * @param string $field 字段名, 默认为'id'.
	 */
	function load($table, $id, $field='id'){
		$id = $this->escape($id);
		$sql = "select * from `{$table}` where `{$field}`='{$id}'";
		$row = $this->get($sql);
		return $row;
	}

	/**
	 * 保存一条记录, 调用后, id被设置.
	 * @param object $row
	 */
	function save($table, &$row){
		$row = $this->escape($row);
		$sqlA = array();
		foreach($row as $k=>$v){
			if($v === NULL){
				$sqlA[] = "`$k` = NULL";
			}else{
				$sqlA[] = "`$k` = '$v'";
			}
		}
		$sqlA = join(',', $sqlA);

		$sql  = "insert into `{$table}` set $sqlA";
		$ret = $this->query($sql);
		if(is_object($row)){
			if(!$row->id){
				$row->id = $this->last_insert_id();
			}
		}else if(is_array($row)){
			if(!$row['id']){
				$row['id'] = $this->last_insert_id();
			}
		}
		return $ret;
	}

	/**
	 * 保存一条记录, 调用后, id被设置.
	 * @param object $row
	 */
	function replace($table, &$row){
		$row = $this->escape($row);
		$sqlA = array();
		foreach($row as $k=>$v){
			if($v === NULL){
				$sqlA[] = "`$k` = NULL";
			}else{
				$sqlA[] = "`$k` = '$v'";
			}
		}
		$sqlA = join(',', $sqlA);

		$sql  = "replace into `{$table}` set $sqlA";
		$ret = $this->query($sql);
		if(is_object($row)){
			$row->id = $this->last_insert_id();
		}else if(is_array($row)){
			$row['id'] = $this->last_insert_id();
		}
		return $ret;
	}

	/**
	 * 更新$arr[id]所指定的记录.
	 * @param array $row 要更新的记录, 键名为id的数组项的值指示了所要更新的记录.
	 * @return int 影响的行数.
	 * @param string $field 字段名, 默认为'id'.
	 */
	function update($table, &$row, $field='id'){
		$row = $this->escape($row);
		$sqlA = array();
		foreach($row as $k=>$v){
			if($v === NULL){
				$sqlA[] = "`$k` = NULL";
			}else{
				$sqlA[] = "`$k` = '$v'";
			}
		}
		$sqlA = join(',', $sqlA);

		if(is_object($row)){
			$id = $row->{$field};
		}else if(is_array($row)){
			$id = $row[$field];
		}
		$sql  = "update `{$table}` set $sqlA where `{$field}`='$id'";
		return $this->query($sql);
	}

	/**
	 * 删除一条记录.
	 * @param int $id 要删除的记录编号.
	 * @return int 影响的行数.
	 * @param string $field 字段名, 默认为'id'.
	 */
	function remove($table, $id, $field='id'){
		$id = $this->escape($id);
		$sql  = "delete from `{$table}` where `{$field}`='{$id}'";
		return $this->query($sql);
	}

	function escape(&$val){
		if($val === NULL){
			//
		}else if(is_object($val) || is_array($val)){
			$this->escape_row($val);
		}else if(is_string($val)){
			$val = $this->conn->real_escape_string($val);
		}
		return $val;
	}

	function escape_row(&$row){
		if(is_object($row)){
			foreach($row as $k=>$v){
				$row->$k = $this->escape($v);
			}
		}else if(is_array($row)){
			foreach($row as $k=>$v){
				$row[$k] = $this->escape($v);
			}
		}
		return $row;
	}

	function escape_like_string($str){
		$find = array('%', '_');
		$replace = array('\%', '\_');
		$str = str_replace($find, $replace, $str);
		return $str;
	}

}

/*
示例:

// file: inc.php, 所有需要数据库连接的代码都include该文件
include('Mysql_i.php');
$conf= array(
	'host' => 'localhost',
	'dbname' => 'database1',
	'username' => 'test',
	'password' => '123456',
);
$db = new Mysql_i($conf);


// app.php
include('inc.php');

// 把所有的行当做对象返回.
$rows = $db->find("select * from table");
foreach($rows as $r){
	echo $r->id . ' ' . $r->name;
}

// 计数, 注意SQL文句中的count(*)
$count = $db->count("select count(*) from table");

// 插入数据
$db->query("insert into ...");

*/
