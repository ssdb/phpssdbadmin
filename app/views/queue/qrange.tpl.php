<h2>qrange: <code><?php echo $n?></code></h2>

<div style="float: left;">
	<a class="btn btn-xs btn-primary" href="<?php echo _action('qpush', array('n'=>$n))?>">
		<i class="glyphicon glyphicon-plus"></i>
		Push
	</a>
	&nbsp;
	<a class="btn btn-xs btn-danger" href="<?php echo _action('qpop', array('n'=>$n))?>">
		<i class="glyphicon glyphicon-minus"></i>
		Pop
	</a>
</div>

<div style="float: right;">
<form method="get">
	<input type="hidden" name="n" value="<?php echo htmlspecialchars($n)?>" />
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
		<th>Index</th>
		<th>Item</th>
		<th>Item Length</th>
		<th width="60">Action</th>
	</tr>
</thead>
<tbody>
	<?php
	foreach($kvs as $k=>$v){
		$vlen = strlen($v);
		$v = htmlspecialchars($v);
		if(strlen($v) > 128){
			$v = substr($v, 0, 128) . '...';
		}
	?>
	<tr>
		<td><input type="checkbox" class="cb" /></td>
		<td><a href="<?php echo _action('qget', array('n'=>$n, 'k'=>$k))?>"><?php echo htmlspecialchars($k)?></a></td>
		<td><?php echo $v?></td>
		<td><?php echo $vlen?></td>
		<td>
			<a class="btn btn-xs btn-primary" href="<?php echo _action('qget', array('n'=>$n, 'k'=>$k))?>" title="View">
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
	pager.index = <?php echo $s/$size + 1?>;
	pager.size = <?php echo $size?>;
	pager.itemCount = <?php echo $total?>;
	pager.onclick = function(index){
		var n = <?php echo json_encode($n)?>;
		var s = (pager.index - 1) * pager.size;
		var url = <?php echo json_encode(_action('qrange'))?> + '?' + $.param({n: n, s: s});
		location.href = url;
	}
	
	pager.render();
});
</script>

