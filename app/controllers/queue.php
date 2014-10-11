<?php
class QueueController extends BaseController
{
	function init($ctx){
		parent::init($ctx);
		$ctx->title = 'LIST';
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
			$ctx->kvs = $this->ssdb->qrlist($s, $e, $size + 1);
			if($ctx->kvs === false){
				$ctx->kvs = $this->ssdb->qlist('', '', $size + 1);
				$ctx->has_more = false;
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
			}else{
				$ctx->has_more = (count($ctx->kvs) == $size + 1);
				$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
				$ctx->kvs = array_reverse($ctx->kvs, true);
			}
		}else{
			$ctx->kvs = $this->ssdb->qlist($s, $e, $size + 1);
			$ctx->has_more = (count($ctx->kvs) == $size + 1);
			$ctx->kvs = array_slice($ctx->kvs, 0, $size, true);
		}

		$tmp = array();
		foreach($ctx->kvs as $k){
			$tmp[$k] = $this->ssdb->qsize($k);
		}
		$ctx->kvs = $tmp;
	}
	
	function qrange($ctx){
		$n = trim($_GET['n']);
		$s = intval($_GET['s']);
		$size = $ctx->size;
		
		$ctx->n = $n;
		$ctx->s = $s;
		$ctx->size = $size;
		
		$ctx->total = $this->ssdb->qsize($n);
		$vs = $this->ssdb->qrange($n, $s, $size);
		$kvs = array();
		foreach($vs as $index=>$v){
			$kvs[$s + $index] = $v;
		}
		$ctx->kvs = $kvs;
	}
	
	function qget($ctx){
		$n = trim($_GET['n']);
		$k = trim($_GET['k']);
		$v = $this->ssdb->qget($n, $k);
		$ctx->n = $n;
		$ctx->k = $k;
		$ctx->v = $v;
	}
	
	function qpush($ctx){
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
				$v = trim($req['v'][$index]);
				if(!strlen($n) || !strlen($v)){
					continue;
				}
				$loc = trim($req['loc'][$index]);
				if($loc == 'back'){
					$this->ssdb->qpush_back($n, $v);
				}else{
					$this->ssdb->qpush_front($n, $v);
				}
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
			$ctx->jump = _action('qrange', array('n'=>$n));
		}
	}
	
	function qpop($ctx){
		$n = trim($_GET['n']);
		$ctx->n = $n;
		if($_POST){
			$req = $_GET + $_POST;
			$n = trim($req['n']);
			$num = intval($req['num']);
			$loc = trim($req['loc']);
			for($i=0; $i<$num; $i++){
				if($loc == 'back'){
					$this->ssdb->qpop_back($n);
				}else{
					$this->ssdb->qpop_front($n);
				}
			}
			_redirect($_POST['jump']);
			return;
		}
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _action('qrange', array('n'=>$n));
		}
	}

	function qclear($ctx){
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
				$this->ssdb->qclear($n);
			}
			_redirect($_POST['jump']);
		}
		
		$ctx->ns = $req['n'];
		$ctx->jump = $_SERVER['HTTP_REFERER'];
		if(!$ctx->jump){
			$ctx->jump = _action('qrange', array('n'=>$n));
		}
	}
}
