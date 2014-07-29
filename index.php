<?php
error_reporting(E_ALL & ~E_NOTICE);

define('APP_PATH', dirname(__FILE__) . '/app');
// You can change IPHP_PATH
define('IPHP_PATH', dirname(__FILE__) . '/iphp');
require_once(IPHP_PATH . '/loader.php');

App::run();
