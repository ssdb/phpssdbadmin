<?php
class BaseController extends Controller
{
	protected $ssdb;
	
	function init($ctx){
		$conf = App::$config['ssdb'];
		$this->ssdb = new SimpleSSDB($conf['host'], $conf['port']);
	}
}
