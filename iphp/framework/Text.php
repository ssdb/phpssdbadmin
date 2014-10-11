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
		}else if($input === null){
			return 'null';
		}else if($input === true){
			return 'true';
		}else if($input === false){
			return 'false';
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
		if($xml){
			$xml = self::trim_xml_obj($xml);
		}
		return $xml;
	}
	
	private static function trim_xml_obj($obj){
		foreach($obj as $k=>$v){
			if(is_object($v)){
				if(count((array)$v) == 0){
					$v = '';
				}else{
					$v = self::trim_xml_obj($v);
				}
			}
			$obj->$k = $v;
		}
		return $obj;
	}
	
	static function xml_to_array($str){
		$xml = @simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
		if($xml){
			$xml = @json_decode(@json_encode($xml), 1);
		}
		return $xml;
	}

	/**
	 * forbidden_tags 比 allow_tags 优先
	 * @allow_tags, @forbidden_tags: 逗号分隔的标签名字符串.
	 */
	static function clean_html($html, $allow_tags=null, $forbidden_tags=null, $urlbase=''){
		if(!is_array($allow_tags)){
			if(!is_string($allow_tags) || !$allow_tags){
				$allow_tags = 'a,img,br,pre,del,p,h1,h2,h3,h4,table,caption,tbody,tr,th,td,ul,ol,li,b,strong,div,embed,blockquote';
			}
			$ps = explode(',', $allow_tags);
			$allow_tags = array();
			foreach($ps as $p){
				$p = trim($p);
				$allow_tags[$p] = 1;
			}
		}
		if(!is_array($forbidden_tags)){
			if(!is_string($forbidden_tags) || !$forbidden_tags){
				$forbidden_tags = '';
			}
			$ps = explode(',', $forbidden_tags);
			$forbidden_tags = array();
			foreach($ps as $p){
				$p = trim($p);
				$forbidden_tags[$p] = 1;
			}
		}
		if(strpos($urlbase, '/') !== strlen($urlbase) - 1){
			$urlbase .= '/';
		}

		$dom = new DOMDocument();
		@$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>' . $html);
		$root = $dom->documentElement;
		$html = self::clean_html_node($root, $allow_tags, $forbidden_tags, 0, $urlbase);
		return $html;
	}

	private static function clean_html_node($node, $allow_tags, $forbidden_tags, $indent=0, $urlbase=''){
		static $attr_define = array(
			'a' => 'href|title',
			'img' => 'src|alt',
			'td' => 'rowspan|colspan',
			'th' => 'rowspan|colspan',
			'table' => 'border|cellspacing|cellpadding|bordercolor|width',
			'embed' => 'src|type|width|height',
		);

		$tag = strtolower($node->nodeName);
	
		if($node->nodeType == XML_TEXT_NODE){
			return htmlspecialchars(trim($node->nodeValue));
		}
		if($node->nodeType != XML_ELEMENT_NODE){
			return '';
		}
		if($tag == 'pre'){
			return '<pre>' . htmlspecialchars($node->textContent) . '</pre>';
		}

		$ps = array();
		if($node->childNodes == null){
			return '';
		}
		foreach($node->childNodes as $n){
			$ps[] = self::clean_html_node($n, $allow_tags, $forbidden_tags, $indent+1, $urlbase);
		}
		$child_text = join('', $ps);

		// 
		if(!$tag || isset($forbidden_tags[$tag]) || !isset($allow_tags[$tag])){
			return $child_text;
		}

		//$text = str_pad('', $indent, "\t", STR_PAD_LEFT);
		$text = '';
		switch($tag){
			case 'br':
				$text .= "\n<br/>\n";
				break;
			case 'div':
			case 'p':
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'tbody':
			case 'tr':
			case 'ul':
			case 'ol':
			case 'li':
			case 'blockquote':
			case 'strong':
				$text .= "<$tag>$child_text</$tag>\n";
				break;
			case 'del':
			case 'caption':
			case 'b':
			case 'strong':
				$text .= "<$tag>$child_text</$tag>";
				break;
			default:
				if(isset($attr_define[$tag])){
					$attr = '';
					$attr_list = explode('|', $attr_define[$tag]);
					foreach($attr_list as $k){
						$v = trim($node->getAttribute($k));
						if(strlen($v) > 0){
							if(in_array($k, array('src','href'))){
								if(strpos($v, 'http://') === false && strpos($v, 'https://') === false){
									if($v[0] === '/'){
										$v = substr($v, 1);
									}
									$v = $urlbase . $v;
								}
							}
							$v = htmlspecialchars($v);
							$attr .= " $k=\"$v\"";
						}
					}
					$text .= "<{$tag}{$attr}>$child_text</$tag>";
					if(in_array($tag, array('embed','table','td','th'))){
						$text .= "\n";
					}
				}else{
					$text .= $child_text;
				}
				break;
		}
		return $text;
	}
	
	static function stripslashes($mixed){
		if(is_string($mixed)){
			return stripslashes($mixed);
		}else if(is_array($mixed)){
			foreach($mixed as $k=>$v){
				$mixed[$k] = self::stripslashes($v);
			}
			return $mixed;
		}else if(is_array($mixed)){
			foreach($mixed as $k=>$v){
				$mixed->$k = self::stripslashes($v);
			}
			return $mixed;
		}else{
			return $mixed;
		}
	}
}
