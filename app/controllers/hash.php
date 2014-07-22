<?php
class HashController extends BaseController
{
	function index($ctx){
		$s = trim($_GET['s']);
		$e = trim($_GET['e']);
		$size = intval($_GET['size']);
		if($size <= 0){
			$size = 3;
		}
		$dir = trim($_GET['dir']);
		if($dir != 'prev'){
			$dir = 'next';
		}
		
		$ctx->s = $s;
		$ctx->e = $e;
		$ctx->dir = $dir;
		$ctx->size = $size;
		if($dir == 'prev'){
			//$ctx->kvs = $this->ssdb->hrlist($s, $e, $size + 1);
			//$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = $this->ssdb->hlist('', '', $size + 1);
			$ctx->has_more = false;
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			//$ctx->kvs = array_reverse($ctx->kvs, true);
		}else{
			$ctx->kvs = $this->ssdb->hlist($s, $e, $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
		}

		$tmp = array();
		foreach($ctx->kvs as $k){
			$tmp[$k] = $this->ssdb->hsize($k);
		}
		$ctx->kvs = $tmp;
	}
	
	function hscan($ctx){
		$h = trim($_GET['h']);
		$s = trim($_GET['s']);
		$e = trim($_GET['e']);
		$size = intval($_GET['size']);
		if($size <= 0){
			$size = 3;
		}
		$dir = trim($_GET['dir']);
		if($dir != 'prev'){
			$dir = 'next';
		}
		
		$ctx->h = $h;
		$ctx->s = $s;
		$ctx->e = $e;
		$ctx->dir = $dir;
		$ctx->size = $size;
		if($dir == 'prev'){
			$ctx->kvs = $this->ssdb->hrscan($h, $s, $e, $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			$ctx->kvs = array_reverse($ctx->kvs, true);
		}else{
			$ctx->kvs = $this->ssdb->hscan($h, $s, $e, $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
		}
	}
	
	function hget($ctx){
		$h = trim($_GET['h']);
		$k = trim($_GET['k']);
		$v = $this->ssdb->hget($h, $k);
		$ctx->h = $h;
		$ctx->k = $k;
		$ctx->v = $v;
	}
	
	function hset($ctx){
		$req = $_POST + $_GET;
		
		if(!is_array($req['k'])){
			$req['k'] = array(trim($req['k']));
		}
		if(!is_array($req['v'])){
			$req['v'] = array(trim($req['v']));
		}
		
		if($_POST){
			if(!is_array($req['h'])){
				$req['h'] = array(trim($req['h']));
			}
			foreach($req['h'] as $index=>$h){
				$h = trim($h);
				$k = trim($req['k'][$index]);
				$v = trim($req['v'][$index]);
				if(!strlen($h) || !strlen($k) || !strlen($v)){
					continue;
				}
				$this->ssdb->hset($h, $k, $v);
			}
			_redirect($_POST['jump']);
			return;
		}

		$h = trim($req['h']);
		if($req['k'][0] === ''){
			$kvs = array('' => '');
		}else{
			$kvs = $this->ssdb->multi_hget($h, $req['k']);
		}
		$ctx->h = $h;
		$ctx->kvs = $kvs;
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('hash/hscan', array('h'=>$h));
		}
	}
	
	function hdel($ctx){
		$req = $_POST + $_GET;
		$h = trim($req['h']);
		
		if(!is_array($req['k'])){
			$req['k'] = array(trim($req['k']));
		}
		
		if($_POST){
			$this->ssdb->multi_hdel($h, $req['k']);
			_redirect($_POST['jump']);
			return;
		}

		$ctx->h = $h;
		$ctx->ks = $req['k'];
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('hash/hscan', array('h'=>$h));
		}
	}

	function hclear($ctx){
		$req = $_POST + $_GET;
		if(!is_array($req['h'])){
			$req['h'] = array(trim($req['h']));
		}
		if($_POST){
			foreach($req['h'] as $index=>$h){
				$h = trim($h);
				if(!strlen($h)){
					continue;
				}
				$this->ssdb->hclear($h);
			}
			_redirect($_POST['jump']);
		}
		
		$ctx->hs = $req['h'];
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('hash/hscan', array('h'=>$h));
		}
	}
}
