<h2>Add or Update KV type record(s)</h2>

<form role="form" method="post">
	<table class="table table-striped" id="new_table">
		<tr>
			<th>Key</th>
			<th>
				Value
				<a class="btn btn-xs btn-success" onclick="add_row()" style="float: right;">
					<i class="glyphicon glyphicon-plus"></i>
				</a>
			</th>
		</tr>
		<tr class="item">
			<td><input name="k[]" class="form-control" type="text" /></td>
			<td><textarea name="v[]" class="form-control"></textarea></td>
		</tr>
	</table>

	
	<p style="text-align: center;">
		<button class="btn btn-sm btn-primary">Save</button>
	</p>
</form>

<script>
function add_row(){
	var t = $($('#new_table tr.item')[0]).clone();
	t.find('.form-control').val('');
	$('#new_table').append(t);
}
</script>

