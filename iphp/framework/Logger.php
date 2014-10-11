<?php
class Logger{
	private static $level = 0;
	private static $logs = array();
	private static $level_map = array(
		1 => 'FATAL',
		2 => 'ERROR',
		3 => 'WARN',
		4 => 'INFO',
		5 => 'DEBUG',
		6 => 'TRACE',
		7 => 'ALL',
	);
	private static $max_level = 7;
	private static $dump = 0;
	private static $config;

	const DUMP_FILE = 1;
	const DUMP_HTML = 2;

	public function __construct(){
		throw new Exception("Static class");
	}

	static function init($config=array()){
		if(!isset($config['level'])){
			self::$level = 0;
		}else if($config['level'] == '*' || strcasecmp($config['level'], 'all') === 0){
			self::$level = self::$max_level;
		}else{
			foreach(self::$level_map as $k=>$v){
				if($v === strtoupper($config['level'])){
					self::$level = $k;
					break;
				}
			}
		}

		if(isset($config['dump'])){
			$ps = explode('|', $config['dump']);
			if(in_array('file', $ps)){
				self::$dump |= self::DUMP_FILE;
			}
			if(in_array('html', $ps)){
				self::$dump |= self::DUMP_HTML;
			}

			if(self::$dump & self::DUMP_FILE){
				if(!isset($config['files'])){
					$config['files'] = array();
				}
				foreach($config['files'] as $k=>$v){
					$k = strtoupper($k);
					$config['files'][$k] = $v;
				}
			}
		}

		self::$config = $config;
	}

	private static function write($level, $msg){
		if($level > self::$level){
			return;
		}

		$log = new stdClass();
		$log->time = microtime(true);
		$log->level = $level;
		$log->msg = $msg;


		if(self::$dump & self::DUMP_HTML){
			self::$logs[] = $log;
		}
		if(self::$dump & self::DUMP_FILE){
			self::write_file($log);
		}
	}

	private static function write_file($log){
		$filename = null;
		foreach(self::$level_map as $level=>$name){
			if($log->level <= $level && isset(self::$config['files'][$name])){
				$filename = self::$config['files'][$name];
				break;
			}
		}
		// TODO: client_ip
		if($filename){
			$level = self::$level_map[$log->level];
			list($sec, $usec) = explode('.', $log->time);
			$usec = substr(sprintf('%03d', $usec), 0, 3);
			$time = date("Y-m-d H:i:s.{$usec}", $sec);
			$msg = $log->msg;
			$msg = preg_replace('/[ \r\n\t]*\n[ \r\n\t]*/', '', $msg);
			$msg = preg_replace('/[ \r\n\t]+/', ' ', $msg);

			if($_SERVER["HTTP_CLIENT_IP"] && $_SERVER["HTTP_CLIENT_IP"]!='unknown'){
				$cip = $_SERVER["HTTP_CLIENT_IP"];
			}else if($_SERVER["HTTP_X_FORWARDED_FOR"] && $_SERVER["HTTP_X_FORWARDED_FOR"]!='unknown'){
				$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}else if($_SERVER["REMOTE_ADDR"] && $_SERVER["REMOTE_ADDR"]!='unknown'){
				$cip = $_SERVER["REMOTE_ADDR"];
			}else{
				$cip = "0.0.0.0";
			}

			$bt = debug_backtrace(false);
			$c_file = basename($bt[2]['file']);
			$c_line = $bt[2]['line'];

			$line = sprintf("%s [%-5s] [%s] [%s:%s] %s\n", $time, $level, $cip, $c_file, $c_line, $msg);
			file_put_contents($filename, $line, FILE_APPEND);
			@chmod($filename, 0666);
		}
	}

	static function trace($msg){
		self::write(6, $msg);
	}

	static function debug($msg){
		self::write(5, $msg);
	}

	static function info($msg){
		self::write(4, $msg);
	}

	static function warn($msg){
		self::write(3, $msg);
	}

	static function error($msg){
		self::write(2, $msg);
	}

	static function fatal($msg){
		self::write(1, $msg);
	}

	static function dump(){
		if(!(self::$dump & self::DUMP_HTML)){
			return;
		}

		echo <<<HTML
		<div style="clear: both;"></div>
		<table width="100%" id="iphp_log_console" border="1"  style="border-collapse: collapse;">
			<tr style="background: #ccc;">
				<th>Timestamp</th>
				<th>Level</th>
				<th>Time</th>
				<th>Message</th>
			</tr>
HTML;

		foreach(self::$logs as $log){
			// APP_TIME_START defined in iphp.php
			$ts = intval(($log->time - APP_TIME_START) * 1000);
			$level = self::$level_map[$log->level];
			list($sec, $usec) = explode('.', $log->time);
			$usec = sprintf('%03d', $usec);
			$time = date("Y-m-d H:i:s.{$usec}", $sec);
			$msg = nl2br(htmlspecialchars($log->msg));

			echo <<<HTML
				<td width="80" style="text-align: center;" class="timestamp">{$ts}</td>
				<td width="60" style="text-align: center;" class="level">{$level}</td>
				<td width="160" style="text-align: center;" class="time">{$time}</td>
				<td class="msg">{$msg}</td>
			</tr>
HTML;
		}
		echo "</table>\n\n";
	}
}
