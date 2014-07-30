<form style="text-align: center;" method="post" action="">
	<input type="hidden" name="jump" value="<?php echo htmlspecialchars($jump)?>" />
	<input type="hidden" name="n" value="<?php echo htmlspecialchars($n)?>" />

	<h2>Remove <?php echo count($ks)?> key(s) in <code><?php echo htmlspecialchars($n)?></code>?</h2>

	<?php foreach($ks as $k){ ?>
		<input type="hidden" name="k[]" value="<?php echo htmlspecialchars($k)?>" />
		<p style="font-weight: bold;"><?php echo $k?></p>
	<?php } ?>
	
	<hr/>

	<button type="submit" class="btn btn-sm btn-danger">Remove</button>
	&nbsp; &nbsp;
	<a class="btn btn-sm btn-default" onclick="history.go(-1)">Cancel</a>
</form>
