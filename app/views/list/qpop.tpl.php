<h2>qpop: <code><?=$n?></code></h2>

<form style="text-align: center;" method="post" action="">
	<input type="hidden" name="jump" value="<?=htmlspecialchars($jump)?>" />
	<input type="hidden" name="n" value="<?=htmlspecialchars($n)?>" />

	Pop
	<input name="num" type="text" size="5" value="1" style="text-align: center;" />
	item(s)

	from at
	<select name="loc">
		<option value="back">Back</option>
		<option value="front">Front</option>
	</select>
	
	<button type="submit" class="btn btn-sm btn-danger">&nbsp;Pop&nbsp;</button>
</form>
