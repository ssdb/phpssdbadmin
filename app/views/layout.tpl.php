<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PHP SSDB Admin</title>
	<meta name="description" content="iphp framework">
	<meta name="keywords" content="iphp framework">
	<link href="<?= _url('/css/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?= _url('/css/main.css') ?>" rel="stylesheet">
	<script src="<?= _url('/js/jquery-1.9.1.min.js') ?>"></script>
	<script src="<?= _url('/js/bootstrap.min.js') ?>"></script>
</head>
<body>

<!-- Fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?=_url('')?>">PSA</a>
		</div>
		<ul class="nav navbar-nav">
			<li class="divider-vertical"></li>
			<li class="active">
				<a href="<?=_url('/')?>">
					<i class="glyphicon glyphicon-home"></i> Home
				</a>
			</li>
			<li><a href="<?=_url('kv')?>">kv</a></li>
			<li><a href="<?=_url('hash')?>">hash</a></li>
			<li><a href="<?=_url('zset')?>">zset</a></li>
			<li><a href="<?=_url('list')?>">list</a></li>
			<li><a href="<?=_url('about')?>">About</a></li>
		</ul>
	</div>
</div>



<div class="container">
	<?php _view(); ?>
		
	<div class="footer">
		Copyright &copy; 2014 <a href="http://www.ideawu.net/">ideawu</a>. All rights reserved.
		<?php printf('%.2f', 1000*(microtime(1) - APP_TIME_START)); ?> ms
	</div>

</div>
<!-- /container -->

</body>
</html>
