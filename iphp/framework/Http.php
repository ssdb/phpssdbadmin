<?php
class Http
{
	static function post($url, $data){
		if(is_array($data)){
			$data = http_build_query($data);
		}
		$ch = curl_init($url) ;
		curl_setopt($ch, CURLOPT_POST, 1) ;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = @curl_exec($ch) ;
		curl_close($ch) ;
		return $result;
	}

	static function get($url, $data=null){
		if(is_array($data)){
			$data = http_build_query($data);
			if(strpos($url, '?') === false){
				$url .= '?' . $data;
			}else{
				$url .= '&' . $data;
			}
		}
		$ch = curl_init($url) ;
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = @curl_exec($ch) ;
		curl_close($ch) ;
		return $result;
	}
}
