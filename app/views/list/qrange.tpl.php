<h2>qrange: <code><?=$n?></code></h2>

<div style="float: left;">
	<a class="btn btn-xs btn-primary" href="<?=_url('list/qpush', array('n'=>$n))?>">
		<i class="glyphicon glyphicon-plus"></i>
		Add
	</a>
	&nbsp;
	<a class="btn btn-xs btn-danger" href="<?=_url('list/qpop', array('n'=>$n))?>">
		<i class="glyphicon glyphicon-minus"></i>
		Remove
	</a>
</div>

<div style="float: right;">
<form method="get">
	<input type="hidden" name="n" value="<?=htmlspecialchars($n)?>" />
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
		<th>Index</th>
		<th>Item</th>
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
		<td><a href="<?=_url('list/qget', array('n'=>$n, 'k'=>$k))?>"><?=htmlspecialchars($k)?></a></td>
		<td><?=$v?></td>
		<td>
			<a class="btn btn-xs btn-primary" href="<?=_url('list/qget', array('n'=>$n, 'k'=>$k))?>" title="View">
				<i class="glyphicon glyphicon-search"></i>
			</a>
		</td>
	</tr>
	<?php } ?>
</tbody>
</table>


<div id="pager">
</div>

<script>
$(function(){
	var pager = new PagerView('pager');
	pager.index = <?=$s/$size + 1?>;
	pager.size = <?=$size?>;
	pager.itemCount = <?=$total?>;
	pager.onclick = function(index){
		var n = <?=json_encode($n)?>;
		var s = (pager.index - 1) * pager.size;
		var url = <?=json_encode(_url('list/qrange'))?> + '?' + $.param({n: n, s: s});
		location.href = url;
	}
	
	pager.render();
});
</script>

