<form style="text-align: center;" method="post" action="">
	<h2>确定要删除这条记录吗?</h2>
	
	<input type="hidden" name="k" value="<?=htmlspecialchars($k)?>" />
	<input type="hidden" name="jump" value="<?=htmlspecialchars($jump)?>" />

	<p style="font-weight: bold;"><?=$k?></p>
	
	<hr/>

	<button type="submit" class="btn btn-sm btn-danger">删除</button>
	&nbsp; &nbsp;
	<a class="btn btn-sm btn-default" onclick="history.go(-1)">取消</a>
</form>
