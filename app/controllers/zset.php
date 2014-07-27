<?php
class ZsetController extends BaseController
{
	function init($ctx){
		parent::init($ctx);
		$ctx->title = 'ZSET';
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
			$ctx->kvs = $this->ssdb->zrlist($s, $e, $size + 1);
			if($ctx->kvs === false){
				$ctx->kvs = $this->ssdb->zlist('', '', $size + 1);
				$ctx->has_more = false;
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			}else{
				$ctx->has_more = (count($ctx->kvs) == $size + 1);
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
				$ctx->kvs = array_reverse($ctx->kvs, true);
			}
		}else{
			$ctx->kvs = $this->ssdb->zlist($s, $e, $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
		}

		$tmp = array();
		foreach($ctx->kvs as $k){
			$tmp[$k] = $this->ssdb->zsize($k);
		}
		$ctx->kvs = $tmp;
	}
	
	function zscan($ctx){
		$n = trim($_GET['n']);
		$s = trim($_GET['s']);
		$e = trim($_GET['e']);
		$size = $ctx->size;
		$dir = trim($_GET['dir']);
		if($dir != 'prev'){
			$dir = 'next';
		}
		
		$ctx->n = $n;
		$ctx->s = $s;
		$ctx->e = $e;
		$ctx->dir = $dir;
		$ctx->size = $size;
		if($dir == 'prev'){
			$ctx->kvs = $this->ssdb->zrscan($n, $s, '', '', $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			$ctx->kvs = array_reverse($ctx->kvs, true);
		}else{
			$ctx->kvs = $this->ssdb->zscan($n, $s, '', '', $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
		}
	}
	
	function zget($ctx){
		$n = trim($_GET['n']);
		$k = trim($_GET['k']);
		$v = $this->ssdb->zget($n, $k);
		$ctx->n = $n;
		$ctx->k = $k;
		$ctx->v = $v;
	}
	
	function zset($ctx){
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
				$v = intval($req['v'][$index]);
				if(!strlen($n) || !strlen($k)){
					continue;
				}
				$this->ssdb->zset($n, $k, $v);
			}
			_redirect($_POST['jump']);
			return;
		}

		$n = trim($req['n']);
		if($req['k'][0] === ''){
			$kvs = array('' => '');
		}else{
			$kvs = $this->ssdb->multi_zget($n, $req['k']);
		}
		$ctx->n = $n;
		$ctx->kvs = $kvs;
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('zset/zscan', array('n'=>$n));
		}
	}
	
	function zdel($ctx){
		$req = $_POST + $_GET;
		$n = trim($req['n']);
		
		if(!is_array($req['k'])){
			$req['k'] = array(trim($req['k']));
		}
		
		if($_POST){
			$this->ssdb->multi_zdel($n, $req['k']);
			_redirect($_POST['jump']);
			return;
		}

		$ctx->n = $n;
		$ctx->ks = $req['k'];
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('zset/zscan', array('n'=>$n));
		}
	}

	function zclear($ctx){
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
				$this->ssdb->zclear($n);
			}
			_redirect($_POST['jump']);
		}
		
		$ctx->ns = $req['n'];
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('zset/zscan', array('n'=>$n));
		}
	}
}
