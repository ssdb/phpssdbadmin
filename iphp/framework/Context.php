<?php
class Context
{
	function as_array(){
		$arr = array();
		foreach($this as $k=>$v){
			$arr[$k] = $v;
		}
		return $arr;
	}
}
