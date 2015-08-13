<h2>Add or Update KV type record(s)</h2>

<form role="form" method="post">
	<input type="hidden" name="jump" value="<?php echo htmlspecialchars($jump)?>" />

	<table class="table table-striped" id="new_table">
	<thead>
		<tr>
			<th>Key</th>
			<th>Value</th>
			<th width="80">
				TTL
				<a class="btn btn-xs btn-success" onclick="add_row()" style="float: right;">
					<i class="glyphicon glyphicon-plus"></i>
				</a>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($kvs as $k=>$v){ ?>
		<tr class="item">
			<td><input name="k[]" class="form-control" type="text" value="<?php echo htmlspecialchars($k)?>" /></td>
			<td><textarea name="v[]" class="form-control"><?php echo htmlspecialchars($v)?></textarea></td>
			<td><input name="ttl[]" class="form-control" type="text" value="<?php echo $ttls[$k]?>" /></td>
		</tr>
		<?php } ?>
	</tbody>
	</table>

	
	<p style="text-align: center;">
		<button class="btn btn-sm btn-primary">Save</button>
	</p>
</form>

<script>
function add_row(){
	var t = $('#new_table tr.item:first').clone();
	t.find('.form-control').val('');
	$('#new_table').append(t);
}
</script>

