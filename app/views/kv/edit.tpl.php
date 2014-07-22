<form role="form" method="post">
	<div class="form-group">
		<label for="k">Key</label>
		<div><?=$k?></div>
		<input type="hidden" name="k" value="<?=htmlspecialchars($k)?>" />
	</div>
	<div class="form-group">
		<label for="v">Value</label>
		<textarea name="v" id="v" class="form-control" style="height: 200px;"><?=htmlspecialchars($v)?></textarea>
	</div>
	
	<button type="submit" class="btn btn-default">Save</button>
</form>
