<form style="text-align: center;" method="post" action="">
	<input type="hidden" name="jump" value="<?=htmlspecialchars($jump)?>" />
	<input type="hidden" name="h" value="<?=htmlspecialchars($h)?>" />

	<h2>Remove <?=count($ks)?> key(s) in <code><?=htmlspecialchars($h)?></code>?</h2>

	<?php foreach($ks as $k){ ?>
		<input type="hidden" name="k[]" value="<?=htmlspecialchars($k)?>" />
		<p style="font-weight: bold;"><?=$k?></p>
	<?php } ?>
	
	<hr/>

	<button type="submit" class="btn btn-sm btn-danger">Remove</button>
	&nbsp; &nbsp;
	<a class="btn btn-sm btn-default" onclick="history.go(-1)">Cancel</a>
</form>
