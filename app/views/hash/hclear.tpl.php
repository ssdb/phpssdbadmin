<form style="text-align: center;" method="post" action="">
	<input type="hidden" name="jump" value="<?=htmlspecialchars($jump)?>" />

	<h2>Clear <?=count($hs)?> hash(s)?</h2>

	<?php foreach($hs as $h){ ?>
		<input type="hidden" name="h[]" value="<?=htmlspecialchars($h)?>" />
		<p style="font-weight: bold;"><?=htmlspecialchars($h)?></p>
	<?php } ?>
	
	<hr/>

	<button type="submit" class="btn btn-sm btn-danger">Clear</button>
	&nbsp; &nbsp;
	<a class="btn btn-sm btn-default" onclick="history.go(-1)">Cancel</a>
</form>
