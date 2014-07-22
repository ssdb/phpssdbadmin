<form style="text-align: center;" method="post" action="">
	<input type="hidden" name="jump" value="<?=htmlspecialchars($jump)?>" />
	<h2>Are you sure remove <?=count($ks)?> record(s)?</h2>

	<?php foreach($ks as $k){ ?>
		<input type="hidden" name="k[]" value="<?=htmlspecialchars($k)?>" />
		<p style="font-weight: bold;"><?=$k?></p>
	<?php } ?>
	
	<hr/>

	<button type="submit" class="btn btn-sm btn-danger">Remove</button>
	&nbsp; &nbsp;
	<a class="btn btn-sm btn-default" onclick="history.go(-1)">Cancel</a>
</form>
