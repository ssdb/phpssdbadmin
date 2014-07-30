<form style="text-align: center;" method="post" action="">
	<input type="hidden" name="jump" value="<?php echo htmlspecialchars($jump)?>" />

	<h2>Clear <?php echo count($ns)?> zset(s)?</h2>

	<?php foreach($ns as $n){ ?>
		<input type="hidden" name="n[]" value="<?php echo htmlspecialchars($n)?>" />
		<p style="font-weight: bold;"><?php echo htmlspecialchars($n)?></p>
	<?php } ?>
	
	<hr/>

	<button type="submit" class="btn btn-sm btn-danger">Clear</button>
	&nbsp; &nbsp;
	<a class="btn btn-sm btn-default" onclick="history.go(-1)">Cancel</a>
</form>
