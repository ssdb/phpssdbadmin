<?php
class IndexController extends BaseController
{
	function index($ctx){
		$info = $this->ssdb->info();
		$info = array_slice($info, 1);
		$tmp = array();
		for($i=0; $i<count($info); $i+=2){
			$tmp[$info[$i]] = $info[$i + 1];
		}
		$ctx->info = $this->parse_info($tmp);
	}
	
	private function parse_info($info){
		$disk_usage = 0;
		$stats = $info['leveldb.stats'];
		$lines = explode("\n", $stats);
		foreach(array_slice($lines, 3) as $line){
			$ps = preg_split('/\s+/', trim($line));
			$disk_usage += $ps[2];
		}
		$info['disk_usage'] = $disk_usage . ' MB';
		return $info;
	}
	
	function logout($ctx){
		unset($_SESSION['login_user']);
		_redirect('login');
	}
}
