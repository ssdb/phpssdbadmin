<h2>Add List type record(s)</h2>

<form role="form" method="post">
	<input type="hidden" name="jump" value="<?php echo htmlspecialchars($jump)?>" />

	<table class="table table-striped" id="new_table">
	<thead>
		<tr>
			<th>List</th>
			<th width="80">Location</th>
			<th>
				Item
				<a class="btn btn-xs btn-success" onclick="add_row()" style="float: right;">
					<i class="glyphicon glyphicon-plus"></i>
				</a>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($kvs as $k=>$v){ ?>
		<tr class="item">
			<?php if($n){ ?>
				<td><input name="n[]" class="form-control" type="text" readonly="readonly" value="<?php echo htmlspecialchars($n)?>" /></td>
			<?php }else{ ?>
				<td><input name="n[]" class="form-control" type="text" /></td>
			<?php } ?>
			<td>
				<select name="loc[]">
					<option value="back">Back</option>
					<option value="front">Front</option>
				</select>
			</td>
			<td><textarea name="v[]" class="form-control"><?php echo htmlspecialchars($v)?></textarea></td>
		</tr>
		<?php } ?>
	</tbody>
	</table>

	
	<p style="text-align: center;">
		<button class="btn btn-sm btn-primary">Save</button>
		&nbsp; &nbsp;
		<a class="btn btn-sm btn-default" onclick="history.go(-1)">Cancel</a>
	</p>
</form>

<script>
function add_row(){
	var t = $('#new_table tr.item:first').clone();
	t.find('.form-control:not([readonly])').val('');
	t.find('select').val($($('#new_table tr.item select')[0]).val());
	$('#new_table').append(t);
}
</script>

