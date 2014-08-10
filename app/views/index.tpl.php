<h1>PHP SSDB Admin</h1>

<hr/>

<h3>Server Info - <?php echo $conf['host'] . ':' . $conf['port']?></h3>

<table class="table table-striped">
<?php foreach($info as $k=>$v){ ?>
<tr>
	<td width="140"><?php echo $k?></td>
	<?php if($k == 'leveldb.stats'){ ?>
		<td><pre><?php echo $v?></pre></td>
	<?php }else{ ?>
		<td><?php echo $v?></td>
	<?php } ?>
</tr>
<?php } ?>
</table>
