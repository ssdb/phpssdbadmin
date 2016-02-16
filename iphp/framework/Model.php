<?php
class Model
{
	static $table_name = false;

	static function db(){
		return Db::instance();
	}
	
	static function table(){
		if(static::$table_name === false){
			$table_name = get_called_class();
			$table_name{0} = strtolower($table_name{0});
			static::$table_name = $table_name;
		}
		return static::$table_name;
	}

	function __construct($table_name=null){
		if($table_name === null && static::$table_name === false){
			$table_name = self::table();
		}
		if($table_name){
			static::$table_name = $table_name;
		}
	}

	function __get($name){
		if(!property_exists($this, $name) && $this->id && strpos($name, '_id') !== strlen($name) - 3){
			$cls = ucfirst($name);
			if(property_exists($this, $name . '_id')){
				$val = $this->{$name . '_id'};
				$this->$name = $cls::get($val);
			}else{
				$this->$name = null;
			}
		}
		return $this->$name;
	}

	static function get($id){
		$row = self::db()->load(static::table(), $id);
		if(!$row){
			return null;
		}
		return self::_model($row);
	}
	
	// 返回以 id 作为 key, value 是对象的关联数组.
	static function get_by_ids($ids){
		if(is_array($ids) && count($ids) == 0){
			return array();
		}
		$in = Db::build_in_string($ids);
		$where = "id in ($in)";
		$tmp = self::find(0, count($ids), $where);
		$ret = array();
		foreach($tmp as $v){
			$ret[$v->id] = $v;
		}
		return $ret;
	}

	private static function _model($row){
		$m = new static();
		foreach($row as $k=>$v){
			$m->$k = $v;
		}
		return $m;
	}
	
	static function all(){
		$table = self::table();
		$ret = array();
		$sql = "select * from $table order by id";
		$rows = self::db()->find($sql);
		foreach($rows as $k=>$v){
			$ret[] = self::_model($v);
		}
		return $ret;
	}

	static function paginate($page, $size, $where='', $order=''){
		if($page < 1){
			$page = 1;
		}
		$start = ($page - 1) * $size;
		if(strlen($where)){
			$where = "where 1 and $where";
		}
		if(strlen($order)){
			$order = "order by $order";
		}
		$start = intval($start);
		$size = intval($size);
		$limit = "limit $start, $size";
		$table = self::table();

		$ds = array();
		$sql = "select count(*) from $table $where";
		$ds['total'] = self::db()->count($sql);
		$sql = "select * from $table $where $order $limit";
		$ds['items'] = self::db()->find($sql);
		foreach($ds['items'] as $k=>$v){
			$ds['items'][$k] = self::_model($v);
		}
		return $ds;
	}
	
	static function save($attrs){
		Db::save_row(self::table(), $attrs);
		$ret = self::get($attrs['id']);
		if(!$ret){
			throw new Exception("无法写入数据库");
		}
		return $ret;
	}
	
	function update($attrs){
		$tmp = $attrs;
		$ret = Db::update_row(self::table(), $this->id, $attrs);
		foreach($tmp as $k=>$v){
			$this->$k = $v;
		}
		return $ret;
	}
	
	static function delete($id){
		return Db::delete_row(self::table(), $id);
	}

	static function deleteByWhere($where){
		return self::delete_by_where($where);
	}
	
	static function delete_by_where($where){
		$table = self::table();
		$sql = "delete from $table where 1";
		if($where){
			$sql .= " and $where";
		}
		return Db::query($sql);
	}

	static function getBy($field, $val){
		return self::get_by($field, $val);
	}
	
	static function get_by($field, $val){
		$table = self::table();
		$row = self::db()->load($table, $val, $field);
		if(!$row){
			return null;
		}
		return self::_model($row);
	}
	
	static function find($start, $size, $where='', $order=''){
		if(strlen($where)){
			$where = "where 1 and $where";
		}
		if(strlen($order)){
			$order = "order by $order";
		}
		$start = intval($start);
		$size = intval($size);
		$limit = "limit $start, $size";
		$table = self::table();
		$sql = "select * from $table $where $order $limit";
		$ret = self::db()->find($sql);
		foreach($ret as $k=>$v){
			$ret[$k] = self::_model($v);
		}
		return $ret;
	}

	static function findOne($where='', $order=''){
		return self::find_one($where, $order);
	}
	
	static function find_one($where='', $order=''){
		$rs = self::find(0, 1, $where, $order);
		if($rs){
			return $rs[0];
		}
		return null;
	}
}

