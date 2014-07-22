
<h3>演示</h3>

<p>从 Controller 传来的变量 <code>$var</code>: <?=$var?> </p>

<table class="table table-striped">
<thead>
	<tr>
		<th>Path</th>
		<th>Controller File</th>
		<th>Controller#action</th>
		<th>View</th>
		<th>Full URL</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td><?=str_replace(_url(''), '', _url('about'))?></td>
		<td>about.php</td>
		<td>AboutController#index</td>
		<td>about.tpl.php</td>
		<td><a target="_blank" href="<?=_url('about')?>"><?=_url('about')?></a></td>
	</tr>
	<tr>
		<td><?=str_replace(_url(''), '', _url('about/us'))?></td>
		<td>about.php</td>
		<td>AboutController#us</td>
		<td>about/us.tpl.php</td>
		<td><a target="_blank" href="<?=_url('about/us')?>"><?=_url('about/us')?></a></td>
	</tr>
	<tr>
		<td><?=str_replace(_url(''), '', _url('about/job'))?></td>
		<td>about/job.php</td>
		<td>JobController#index</td>
		<td>about/job.tpl.php</td>
		<td><a target="_blank" href="<?=_url('about/job')?>"><?=_url('about/job')?></a></td>
	</tr>
	<tr>
		<td><?=str_replace(_url(''), '', _url('api/test'))?></td>
		<td>api/test.php</td>
		<td>TestController#index</td>
		<td>None</td>
		<td><a target="_blank" href="<?=_url('api/test')?>"><?=_url('api/test')?></a></td>
	</tr>
</tbody>
</table>

