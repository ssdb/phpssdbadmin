<h2>Type: ZSET</h2>

<div style="float: left;">
	<a class="btn btn-xs btn-primary" href="<?php echo _url('zset/zset')?>">
		<i class="glyphicon glyphicon-plus"></i>
		Add
	</a>
</div>

<div style="float: right;">
<form method="get">
	Start:
	<input type="text" name="s" value="<?php echo htmlspecialchars($s)?>" />
	Size:
	<select name="size">
		<option value="10" <?php echo $size==10?'selected="selected"':''?>>10</option>
		<option value="20" <?php echo $size==20?'selected="selected"':''?>>20</option>
		<option value="50" <?php echo $size==50?'selected="selected"':''?>>50</option>
		<option value="100" <?php echo $size==100?'selected="selected"':''?>>100</option>
		<option value="200" <?php echo $size==200?'selected="selected"':''?>>200</option>
	</select>
	<button type="submit" class="btn btn-xs btn-primary">Query</button>
</form>
</div>

<div style="clear: both; line-height: 0px; height: 0px;"></div>


<table class="table table-striped table-hover" id="data_list">
<thead>
	<tr>
		<th width="30"><input type="checkbox" onclick="check_all(this)" /></th>
		<th>Zset</th>
		<th>Size</th>
		<th width="60">Action</th>
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
		<td><a href="<?php echo _url('zset/zscan', array('n'=>$k))?>"><?php echo htmlspecialchars($k)?></a></td>
		<td><?php echo $v?></td>
		<td>
			<a class="btn btn-xs btn-danger" href="<?php echo _url('zset/zclear', array('n'=>$k))?>" title="Remove">
				<i class="glyphicon glyphicon-remove"></i>
			</a>
		</td>
	</tr>
	<?php } ?>
</tbody>
</table>

With selected:

<a class="btn btn-xs btn-danger" title="Remove" onclick="remove_selected()">
	<i class="glyphicon glyphicon-remove"></i>
</a>


<script>
function check_all(cb){
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

function remove_selected(){
	var ks = get_selected_keys();
	if(!ks.length){
		alert('Select row(s) first!');
		return;
	}
	var url = <?php echo json_encode(_url('zset/zclear'))?> + '?' + $.param({n: ks});
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
		$url = _url('zset', array('dir'=>'prev', 's'=>$start, 'e'=>'', 'size'=>$size));
	?>
		<a class="btn btn-sm btn-primary" href="<?php echo $url?>">
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
		$url = _url('zset', array('dir'=>'next', 's'=>$start, 'e'=>'', 'size'=>$size));
	?>
		<a class="btn btn-sm btn-primary" href="<?php echo $url?>">
			Next <i class="glyphicon glyphicon-chevron-right"></i>
		</a>
	<?php } ?>
</p>