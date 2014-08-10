<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PHP SSDB Admin<?php if($title){echo ' - ' . $title;} ?></title>
	<meta name="description" content="iphp framework">
	<meta name="keywords" content="iphp framework">
	<link href="<?php echo _url('/css/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?php echo _url('/css/main.css') ?>" rel="stylesheet">
	<script src="<?php echo _url('/js/jquery-1.9.1.min.js') ?>"></script>
	<script src="<?php echo _url('/js/bootstrap.min.js') ?>"></script>
	<script src="<?php echo _url('/js/PagerView.js') ?>"></script>
</head>
<body>

<!-- Fixed navbar -->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?php echo _url('')?>">PSA</a>
		</div>
		<ul class="nav navbar-nav">
			<li class="divider-vertical"></li>
			<li><a href="<?php echo _url('kv/scan')?>">kv</a></li>
			<li><a href="<?php echo _url('hash')?>">hash</a></li>
			<li><a href="<?php echo _url('zset')?>">zset</a></li>
			<li><a href="<?php echo _url('list')?>">list</a></li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li><a target="_blank" href="http://ssdb.io?psa">ssdb.io</a></li>
			<li><a target="_blank" href="https://github.com/ssdb/phpssdbadmin">psa</a></li>
		</ul>
	</div>
</div>



<div class="container">
	<?php _view(); ?>
		
	<div class="footer">
		<!-- you are licensed to edit this message -->
		Copyright&copy;2014 <a href="http://www.ideawu.net/">ideawu</a>. All rights reserved.
		<?php printf('%.2f', 1000*(microtime(1) - APP_TIME_START)); ?> ms
	</div>

</div>
<!-- /container -->

</body>
</html>
