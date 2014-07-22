<?php
class KvController extends BaseController
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
	
	function create($ctx){
		if($_POST){
			$arr = array();
			foreach($_POST['k'] as $index=>$k){
				$v = $_POST['v'][$index];
				$arr[$k] = $v;
			}
			$this->ssdb->multi_set($arr);
			_redirect('kv');
		}
	}
	
	function view($ctx){
		$k = trim($_GET['k']);
		$v = $this->ssdb->get($k);
		$ctx->k = $k;
		$ctx->v = $v;
	}
	
	function edit($ctx){
		if($_POST){
			$k = trim($_POST['k']);
			$v = trim($_POST['v']);
			$this->ssdb->set($k, $v);
			$url = _url('kv/view', array('k'=>$k));
			_redirect($url);
			return;
		}
		$k = trim($_GET['k']);
		$v = $this->ssdb->get($k);
		$ctx->k = $k;
		$ctx->v = $v;
	}
	
	function remove($ctx){
		if($_POST){
			$k = trim($_POST['k']);
			$this->ssdb->del($k);
			_redirect($_POST['jump']);
			return;
		}
		$k = trim($_GET['k']);
		$v = $this->ssdb->get($k);
		$ctx->k = $k;
		$ctx->v = $v;
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _url('kv');
		}
	}
}
