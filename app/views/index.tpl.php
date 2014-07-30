<h1>PHP SSDB Admin</h1>

<hr/>

<h3>Server Info</h3>

<table class="table table-striped">
<?php foreach($info as $k=>$v){ ?>
<tr>
	<td width="150"><?php echo $k?></td>
	<?php if($k == 'leveldb.stats'){ ?>
		<td><pre><?php echo $v?></pre></td>
	<?php }else{ ?>
		<td><?php echo $v?></td>
	<?php } ?>
</tr>
<?php } ?>
</table>
