<?php
class IndexController extends Controller
{
	function init($ctx){
	}
	
	function index($ctx){
		$page = 1;
		$size = 16;
		$where = '';
		#$ctx->page = Post::paginate($page, $size, $where, 'id desc');
		$ctx->var = 'hello! ' . date('Y-m-d H:i:s'); // 在 View 中可以直接使用的变量: $var
	}
}
