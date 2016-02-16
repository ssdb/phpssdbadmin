<?php
class Context
{
	private $lazy_regs = array();
	
	function as_array(){
		$arr = get_object_vars($this);
		unset($arr['lazy_regs']);
		return $arr;
	}

	function __get($name){
		if(!property_exists($this, $name)){
			$this->$name = null;
			if(isset($this->lazy_regs[$name])){
				$callback = $this->lazy_regs[$name];
				$this->$name = call_user_func($callback, $name, $this);
			}
		}
		return $this->$name;
	}
	
	// $value = callback($name, $ctx);
	function lazyload($name, callable $callback_func){
		$this->lazy_regs[$name] = $callback_func;
	}
}
