<?php
class Text
{
	static function json_decode($str, $assoc=false){
		return json_decode($str, $assoc);
	}
	
	static function json_encode($input, $opt=0){
		if(defined('JSON_UNESCAPED_UNICODE')){
			return json_encode($input, JSON_UNESCAPED_UNICODE | $opt);
		}
		if(is_string($input)){
			$text = $input;
			$text = str_replace('\\', '\\\\', $text);
			$text = str_replace(
				array("\r", "\n", "\t", "\""),
				array('\r', '\n', '\t', '\\"'),
				$text);
			return '"' . $text . '"';
		}else if(is_array($input) || is_object($input)){
			$arr = array();
			$is_obj = is_object($input) || (array_keys($input) !== range(0, count($input) - 1));
			foreach($input as $k=>$v){
				if($is_obj){
					$arr[] = self::json_encode($k) . ':' . self::json_encode($v);
				}else{
					$arr[] = self::json_encode($v);
				}
			}
			if($is_obj){
				return '{' . join(',', $arr) . '}';
			}else{
				return '[' . join(',', $arr) . ']';
			}
		}else{
			return $input . '';
		}
	}
	
	static function xml_to_obj($str){
		$xml = @simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
		if($xml){
			$xml = @json_decode(@json_encode($xml));
		}
		return $xml;
	}
	
	static function xml_to_array($str){
		$xml = @simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
		if($xml){
			$xml = @json_decode(@json_encode($xml), 1);
		}
		return $xml;
	}
}
