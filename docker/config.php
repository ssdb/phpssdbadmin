<?php
define('ENV', 'online');
return array(
	'env' => ENV,
	'logger' => array(
		'level' => 'all', // none/off|(LEVEL)
		'dump' => 'file', // none|html|file, 可用'|'组合
		'files' => array( // ALL|(LEVEL)
			#'ALL'	=> dirname(__FILE__) . '/../../logs/' . date('Y-m') . '.log',
		),
	),
	'servers' => array(
		array(
			'host' => 'SSDB_HOST',
			'port' => 'SSDB_PORT',
			//'password' => '22222222',
		),
	),
	'login' => array(
		'name' => 'USERNAME',
		'password' => 'PASSWORD', // at least 6 characters
	),
);