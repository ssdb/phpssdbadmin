<div class="panel panel-primary" style="max-width: 400px; margin: 10px auto;">
	<div class="panel-heading">
		<h3 class="panel-title">Login</h3>
	</div>
	<div class="panel-body">

	<?php if($errmsg){ ?>
	<div class="alert alert-danger">
        <strong>Error！</strong> <?php echo $errmsg;?>
	</div>
	<?php } ?>

	<form method="post" onsubmit="return false;">
		<div class="form-group">
			<input autofocus="autofocus" class="form-control" name="name" placeholder="User Name" required="required" type="text" value="<?php echo $name; ?>" />
		</div>
		<div class="form-group">
			<input autocomplete="off" class="form-control" name="password" placeholder="Password" required="required" type="password" value="" />
		</div>
		<div class="form-group">
			<img id="captcha" src="<?php echo _url('/captcha'); ?>" />
			<input class="form-control" name="verify_code" id="verify_code" type="text" placeholder="Captcha code in the image" />
		</div>

		<div class="form-group">
			<input class="btn btn-lg btn-success btn-block" type="submit" value="Login" onclick="login()" />
		</div>
	</form>

	</div>
</div>

<script type="text/javascript" src="<?=_url('/js/jsencrypt.min.js')?>"></script>
<script type="text/javascript">
(function(){
	$('#captcha').click(function(){
		var url_base = <?php echo json_encode(_url('/captcha')); ?>;
		var url = url_base + '?' + (new Date()).getTime();
		$('#captcha').attr('src', url);
	});
})();

function login(){
	var encryptData = <?php echo json_encode($encrypt);?>;
	var encrypt = new JSEncrypt();
	encrypt.setPublicKey(encryptData.public_key);

	var data = {};
	data.name = $('input[name=name]').val();
	data.password = $('input[name=password]').val();
	data.verify_code = $('input[name=verify_code]').val();
	data[encryptData.field_name] = encryptData.field_value;
	
	data.name = encrypt.encrypt(data.name);
	data.password = encrypt.encrypt(data.password);
	
	var form = $('<form method="post">'
		+ '<input type="hidden" name="name"/>'
		+ '<input type="hidden" name="password"/>'
		+ '<input type="hidden" name="verify_code"/>'
		+ '</form>');
	
	form.find('input[name=name]').val(data.name);
	form.find('input[name=password]').val(data.password);
	form.find('input[name=verify_code]').val(data.verify_code);
	form.appendTo($('body')).submit();
}
</script>
