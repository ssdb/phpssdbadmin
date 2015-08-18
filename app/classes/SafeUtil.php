<?php
class SafeUtil
{
	const ENCRYPT_FIELD_NAME = '_encrypt_code';

	private static $ssdb_prefix = 'psa_safe_';
	static $salt = 'ssdb_&1234567890*@';

	static function token(){
		return md5(uniqid() . mt_rand() . microtime(1) . self::$salt);
	}

	// 保存一份 token 对应的数据, 有效期为 $ttl 秒
	static function set_data($token, $data, $ttl){
		$token = self::$ssdb_prefix . $token;
		$val = array(
			'data' => $data,
			'expire' => time() + $ttl,
		);
		$_SESSION[$token] = $val;
		//$data = json_encode($data);
		//Util::ssdb()->setx($token, $data, $ttl);
	}
	
	// 根据 token 获取相应的数据, 如果不存在返回 false, 否则返回之前 set_data 时存的数据
	static function get_data($token){
		if(strlen($token) > 128){
			return false;
		}
		$token = self::$ssdb_prefix . $token;
		//$data = Util::ssdb()->get($token);
		$val = $_SESSION[$token];
		if(!is_array($val) || $val['expire'] < time()){
			$data = null;
		}else{
			$data = $val['data'];
		}
		if(!$data){
			return false;
		}
		//$data = json_decode($data, true);
		return $data;
	}
	
	static function del_data($token){
		$token = self::$ssdb_prefix . $token;
		unset($_SESSION[$token]);
		//Util::ssdb()->del($token);
	}

	/*
	使用方式:
	1. 调用 create_encrypt_info() 生成公钥和私钥
	2. 将公钥告诉用户, set_encrypt_info() 保存公钥和私钥
	3. 收到用户的数据后, 调用 safe_decrypt() 解密数据, 返回时会删除公钥和私钥
	*/
	
	static function create_encrypt_info(){
		$config = array(
				"digest_alg" => "sha512",
				"private_key_bits" => 1024,
				"private_key_type" => OPENSSL_KEYTYPE_RSA,
				);
		$res = openssl_pkey_new($config);
		$private_key = '';
		openssl_pkey_export($res, $private_key);
		$details = openssl_pkey_get_details($res);
		$public_key = $details["key"];
		if(!$private_key || !$public_key){
			_throw("get_encrypt_keys failed");
		}
		return array(
			'public_key' => $public_key,
			'private_key' => $private_key,
		);
	}

	static function set_encrypt_info($encrypt, $ttl){
		#$token = SafeUtil::token();
		$token = '123';
		self::set_data('encrypt_' . $token, $encrypt, $ttl);
		setcookie(self::ENCRYPT_FIELD_NAME, $token, time() + $ttl, '/'/*, $domail*/);
		return $token;
	}
	
	// $src: array, string
	static function safe_decrypt($src){
		$token = $_REQUEST[self::ENCRYPT_FIELD_NAME];
		if(!$token){
			return $src;
		}
		$token = 'encrypt_' . $token;
		$info = self::get_data($token);
		if($info){
			$private_key = $info['private_key'];
			if(is_string($src)){
				$out = '';
				$s = openssl_private_decrypt(base64_decode(trim($src)), $out, $private_key);
				if(!$s){
					return false;
				}
				$ret = $out;
			}
			if(is_array($src)){
				$ret = array();
				foreach($src as $k=>$v){
					$out = '';
					$s = openssl_private_decrypt(base64_decode(trim($v)), $out, $private_key);
					if(!$s){
						return false;
					}
					$ret[$k] = $out;
				}
			}
			self::del_data($token);
			return $ret;
		}else{
			Logger::debug("解密失败, 没有对应的密钥: $token");
		}
		return false;
	}
}	
