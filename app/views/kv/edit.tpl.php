
<form role="form" method="post">
	<input type="hidden" name="jump" value="<?=htmlspecialchars($jump)?>" />
	<table class="table table-striped" id="new_table">
		<tr>
			<th>Key</th>
			<th>
				Value
			</th>
		</tr>
		<?php foreach($kvs as $k=>$v){ ?>
		<tr class="item">
			<td><input name="k[]" class="form-control" type="text" value="<?=htmlspecialchars($k)?>" /></td>
			<td><textarea name="v[]" class="form-control"><?=htmlspecialchars($v)?></textarea></td>
		</tr>
		<?php } ?>
	</table>

	
	<p style="text-align: center;">
		<button class="btn btn-sm btn-primary">Save</button>
	</p>
</form>