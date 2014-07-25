<h2>Type: KV</h2>

<div style="float: left;">
	<a class="btn btn-xs btn-primary" href="<?=_url('kv/set')?>">
		<i class="glyphicon glyphicon-plus"></i>
		Add
	</a>
</div>

<div style="float: right;">
<form method="get">
	Start:
	<input type="text" name="s" value="<?=htmlspecialchars($s)?>" />
	Size:
	<select name="size">
		<option value="10" <?=$size==10?'selected="selected"':''?>>10</option>
		<option value="20" <?=$size==20?'selected="selected"':''?>>20</option>
		<option value="50" <?=$size==50?'selected="selected"':''?>>50</option>
		<option value="100" <?=$size==100?'selected="selected"':''?>>100</option>
		<option value="200" <?=$size==200?'selected="selected"':''?>>200</option>
	</select>
	<button type="submit" class="btn btn-xs btn-primary">Query</button>
</form>
</div>

<table class="table table-striped" id="data_list">
<thead>
	<tr>
		<th width="30"><input type="checkbox" onclick="check_all(this)" /></th>
		<th>Key</th>
		<th>Value</th>
		<th width="80">Action</th>
	</tr>
</thead>
<tbody>
	<?php
	foreach($kvs as $k=>$v){
		$v = htmlspecialchars($v);
		if(strlen($v) > 128){
			$v = substr($v, 0, 128) . '...';
		}
	?>
	<tr>
		<td><input type="checkbox" class="cb" /></td>
		<td><a href="<?=_url('kv/get', array('k'=>$k))?>"><?=htmlspecialchars($k)?></a></td>
		<td><?=$v?></td>
		<td>
			<a class="btn btn-xs btn-primary" href="<?=_url('kv/set', array('k'=>$k))?>" title="Edit">
				<i class="glyphicon glyphicon-pencil"></i>
			</a>
			<a class="btn btn-xs btn-danger" href="<?=_url('kv/del', array('k'=>$k))?>" title="Remove">
				<i class="glyphicon glyphicon-remove"></i>
			</a>
		</td>
	</tr>
	<?php } ?>
</tbody>
</table>

With selected:

<a class="btn btn-xs btn-primary" title="Edit" onclick="edit_selected()">
	<i class="glyphicon glyphicon-pencil"></i>
</a>
<a class="btn btn-xs btn-danger" title="Remove" onclick="remove_selected()">
	<i class="glyphicon glyphicon-remove"></i>
</a>


<script>
function check_all(cb){
	$('#data_list input.cb').prop('checked', cb.checked);
	$('#data_list input.cb').each(function(i, e){
		e.checked = cb.checked;
	});
}

function get_selected_keys(){
	var ks = [];
	$('#data_list input.cb').each(function(i, e){
		if(e.checked){
			var k = $($($(e).parents('tr')[0]).find('td')[1]).text();
			ks.push(k);
		}
	});
	return ks;
}

function edit_selected(){
	var ks = get_selected_keys();
	if(!ks.length){
		alert('Select row(s) first!');
		return;
	}
	var url = <?=json_encode(_url('kv/set'))?> + '?' + $.param({k: ks});
	location.href = url;
}

function remove_selected(){
	var ks = get_selected_keys();
	if(!ks.length){
		alert('Select row(s) first!');
		return;
	}
	var url = <?=json_encode(_url('kv/del'))?> + '?' + $.param({k: ks});
	location.href = url;
}
</script>


<p class="pager">
	<?php if(($dir == 'prev' && !$has_more) || ($dir == 'next' && $s == '')){ ?>
		<a class="btn btn-sm btn-default disabled" href="#">
			<i class="glyphicon glyphicon-chevron-left"></i> Prev
		</a>
	<?php
	}else{
		$ks = array_keys($kvs);
		$start = $ks[0];
		$url = _url('kv/scan', array('dir'=>'prev', 's'=>$start, 'e'=>'', 'size'=>$size));
	?>
		<a class="btn btn-sm btn-primary" href="<?=$url?>">
			<i class="glyphicon glyphicon-chevron-left"></i> Prev
		</a>
	<?php } ?>
	&nbsp;
	<?php if($dir == 'next' && !$has_more){ ?>
		<a class="btn btn-sm btn-default disabled" href="#">
			Next <i class="glyphicon glyphicon-chevron-right"></i>
		</a>
	<?php
	}else{
		$ks = array_keys($kvs);
		$start = $ks[count($ks)-1];
		$url = _url('kv/scan', array('dir'=>'next', 's'=>$start, 'e'=>'', 'size'=>$size));
	?>
		<a class="btn btn-sm btn-primary" href="<?=$url?>">
			Next <i class="glyphicon glyphicon-chevron-right"></i>
		</a>
	<?php } ?>
</p>