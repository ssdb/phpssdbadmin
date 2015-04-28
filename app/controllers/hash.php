<?php
class HashController extends BaseController
{
	function init($ctx){
		parent::init($ctx);
		$ctx->title = 'HASH';
	}
	
	function index($ctx){
		$s = trim($_GET['s']);
		$e = trim($_GET['e']);
		$size = $ctx->size;
		$dir = trim($_GET['dir']);
		if($dir != 'prev'){
			$dir = 'next';
		}
		
		$ctx->s = $s;
		$ctx->e = $e;
		$ctx->dir = $dir;
		$ctx->size = $size;
		if($dir == 'prev'){
			$ctx->kvs = $this->ssdb->hrlist($s, $e, $size + 1);
			if($ctx->kvs === false){
				$ctx->kvs = $this->ssdb->hlist('', '', $size + 1);
				$ctx->has_more = false;
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			}else{
				$ctx->has_more = (count($ctx->kvs) == $size + 1);
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
				$ctx->kvs = array_reverse($ctx->kvs, true);
			}
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
		$n = trim($_GET['n']);
		$s = trim($_GET['s']);
		$e = trim($_GET['e']);
		$size = $ctx->size;
		$dir = trim($_GET['dir']);
		if($dir != 'prev'){
			$dir = 'next';
		}
		$order = trim($_GET['order']);
		if($order != 'asc'){
			$order = 'desc';
		}
		
		$ctx->n = $n;
		$ctx->s = $s;
		$ctx->e = $e;
		$ctx->dir = $dir;
		$ctx->order = $order;
		$ctx->size = $size;
		if($order == 'asc'){
			if($dir == 'prev'){
				$ctx->kvs = $this->ssdb->hrscan($n, $s, $e, $size + 1);
				$ctx->has_more = (count($ctx->kvs) == $size + 1);
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
				$ctx->kvs = array_reverse($ctx->kvs, true);
			}else{
				$ctx->kvs = $this->ssdb->hscan($n, $s, $e, $size + 1);
				$ctx->has_more = (count($ctx->kvs) == $size + 1);
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			}
		}else{
			if($dir == 'prev'){
				$ctx->kvs = $this->ssdb->hscan($n, $s, $e, $size + 1);
				$ctx->has_more = (count($ctx->kvs) == $size + 1);
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
				$ctx->kvs = array_reverse($ctx->kvs, true);
			}else{
				$ctx->kvs = $this->ssdb->hrscan($n, $s, $e, $size + 1);
				$ctx->has_more = (count($ctx->kvs) == $size + 1);
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			}
		}
	}
	
	function hget($ctx){
		$n = trim($_GET['n']);
		$k = trim($_GET['k']);
		$v = $this->ssdb->hget($n, $k);
		$ctx->n = $n;
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
			if(!is_array($req['n'])){
				$req['n'] = array(trim($req['n']));
			}
			foreach($req['n'] as $index=>$n){
				$n = trim($n);
				$k = trim($req['k'][$index]);
				$v = trim($req['v'][$index]);
				if(!strlen($n) || !strlen($k)){
					continue;
				}
				$this->ssdb->hset($n, $k, $v);
			}
			_redirect($_POST['jump']);
			return;
		}

		$n = trim($req['n']);
		if($req['k'][0] === ''){
			$kvs = array('' => '');
		}else{
			$kvs = $this->ssdb->multi_hget($n, $req['k']);
		}
		$ctx->n = $n;
		$ctx->kvs = $kvs;
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('hash/hscan', array('n'=>$n));
		}
	}
	
	function hdel($ctx){
		$req = $_POST + $_GET;
		$n = trim($req['n']);
		
		if(!is_array($req['k'])){
			$req['k'] = array(trim($req['k']));
		}
		
		if($_POST){
			$this->ssdb->multi_hdel($n, $req['k']);
			_redirect($_POST['jump']);
			return;
		}

		$ctx->n = $n;
		$ctx->ks = $req['k'];
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('hash/hscan', array('n'=>$n));
		}
	}

	function hclear($ctx){
		$req = $_POST + $_GET;
		if(!is_array($req['n'])){
			$req['n'] = array(trim($req['n']));
		}
		if($_POST){
			foreach($req['n'] as $index=>$n){
				$n = trim($n);
				if(!strlen($n)){
					continue;
				}
				$this->ssdb->hclear($n);
			}
			_redirect($_POST['jump']);
		}
		
		$ctx->ns = $req['n'];
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('hash/hscan', array('n'=>$n));
		}
	}
}
