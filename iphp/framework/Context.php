<?php
class Context
{
	private $data = array();
	
	function as_array(){
		return $this->data;
	}

	function __set($name, $value){
		$this->data[$name] = $value;
	}

	function __get($name){
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		return null;
	}

	/**  PHP 5.1.0之后版本 */
	function __isset($name){
		return isset($this->data[$name]);
	}

	/**  PHP 5.1.0之后版本 */
	function __unset($name){
		unset($this->data[$name]);
	}
}
