<?php
class Controller
{
	public $module = '';
	public $action = '';
	public $layout = '';
	public $is_ajax = false;
	public $view_path = array('views');

	function init($ctx){
	}
	
	function index($ctx){
	}
	
	function _view($m){
		_redirect($this->_view_url($m));
	}
	
	function _new_url(){
		return _action('new', null, $this->module);
	}
	
	function _list_url(){
		return _action('list', null, $this->module);
	}
	
	function _save_url(){
		return _action('save', null, $this->module);
	}
	
	function _update_url(){
		return _action('update', null, $this->module);
	}
	
	function _view_url($m){
		return _action('view', $m, $this->module);
	}
	
	function _edit_url($m){
		return _action('edit', $m, $this->module);
	}
}

