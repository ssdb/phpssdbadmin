<?php include(dirname(__FILE__) . '/../_header.tpl.php') ?>
	<h1><?php echo htmlspecialchars($_e->getMessage()); ?></h1>
	<div>
	<?php
		$ts = $_e->getTrace();
		$html = '';
		foreach($ts as $t){
			$html .= "{$t['file']}:{$t['line']} {$t['function']}()<br/>\n";
		}
		echo $html;
	?>
	</div>
<?php include(dirname(__FILE__) . '/../_footer.tpl.php') ?>
