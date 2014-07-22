<?php
class KvController extends BaseController
{
	function index($ctx){
		_redirect('kv/scan');
	}
	
	function scan($ctx){
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
			$ctx->kvs = $this->ssdb->rscan($s, $e, $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			$ctx->kvs = array_reverse($ctx->kvs, true);
		}else{
			$ctx->kvs = $this->ssdb->scan($s, $e, $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
		}
	}
		
	function get($ctx){
		$k = trim($_GET['k']);
		$v = $this->ssdb->get($k);
		$ctx->k = $k;
		$ctx->v = $v;
	}
	
	function set($ctx){
		if($_POST){
			$arr = array();
			foreach($_POST['k'] as $index=>$k){
				$k = trim($k);
				if(strlen($k) == 0){
					continue;
				}
				$v = $_POST['v'][$index];
				$arr[$k] = $v;
			}
			if($arr){
				$this->ssdb->multi_set($arr);
			}

			_redirect($_POST['jump']);
			return;
		}
		
		$ks = $_GET['k'];
		if(!is_array($ks)){
			$ks = array(trim($ks));
		}
		if($ks[0] === ''){
			$kvs = array('' => '');
		}else{
			$kvs = $this->ssdb->multi_get($ks);
		}
		$ctx->kvs = $kvs;

		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('kv');
		}
	}
	
	function del($ctx){
		if($_POST){
			$k = $_POST['k'];
			if(is_array($k)){
				$this->ssdb->multi_del($k);
			}else{
				$this->ssdb->del($k);
			}
			_redirect($_POST['jump']);
			return;
		}
		$k = $_GET['k'];
		if(!is_array($k)){
			$k = array($k);
		}
		$ctx->ks = $k;
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('kv');
		}
	}
}
