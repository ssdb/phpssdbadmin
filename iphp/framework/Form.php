<?php
class Form{
	private $m;
	private $html = '';
	
	function __construct($m, $action='', $method='POST'){
		$this->m = $m;
		$this->action = $action;
		$this->method = $method;

		$this->html .= <<<HTML
<script type="text/javascript">
  $(document).ready(function(){
	$('input.datetimepicker').datetimepicker({
		timeText: '',
		hourText: '时',
		minuteText: '分',
		//showSecond: true,
		showMinute: true,
		dateFormat: 'yy-mm-dd',
		timeFormat: 'hh:mm:00'
	});
  })
</script>
HTML;
	}
	
	function hidden($name){
		$field = "form[$name]";
		$val = htmlspecialchars($this->m->$name);
		$this->html .= <<<HTML
<input type="hidden" name="$field" value="$val" />\n
HTML;
	}
	
	function text($name, $label=false){
		$id = "input_$name";
		$field = "form[$name]";
		$val = htmlspecialchars($this->m->$name);
		$this->html .= <<<HTML
<div class="form-group">
	<label for="$id" class="col-sm-2 control-label">$label</label>
	<div class="col-sm-10">
		<p class="form-control-static">$val</p>
	</div>
</div>\n
HTML;
	}
	
	function input($name, $label=false, $placeholder='', $tag_attrs=array()){
		$id = "input_$name";
		$field = "form[$name]";
		$val = htmlspecialchars($this->m->$name);
		$this->html .= <<<HTML
<div class="form-group">
	<label for="$id" class="col-sm-2 control-label">$label</label>
	<div class="col-sm-10">
		<input type="text" class="form-control" id="$id"
			name="$field" value="$val" />
			<span class="help-block">$placeholder</span>
	</div>
</div>\n
HTML;
	}
	
	function textarea($name, $label=false, $placeholder='', $tag_attrs=array()){
		$id = "input_$name";
		$field = "form[$name]";
		$val = htmlspecialchars($this->m->$name);
		$tags = '';
		foreach($tag_attrs as $k=>$v){
			$tags .= " $k=\"$v\"";
		}
		$this->html .= <<<HTML
<div class="form-group">
	<label for="$id" class="col-sm-2 control-label">$label</label>
	<div class="col-sm-10">
		<textarea class="form-control" name="$field" id="$id"$tags>$val</textarea>
	</div>
</div>\n
HTML;
	}
	
	function submit($text='Submit'){
		$this->html .= <<<HTML
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		<button type="submit" class="btn btn-primary">$text</button>
	</div>
</div>\n
HTML;
	}
	
	function render(){
		echo <<<HTML
<form action="{$this->action}" method="{$this->method}"
	class="form-horizontal" role="form">\n
HTML;
		echo "\n";
		echo $this->html;
		echo '</form>';
		echo "\n";
	}
	
	function radio($name, $label=false, $options=array()){
		$id = "input_$name";
		$field = "form[$name]";
		$val = htmlspecialchars($this->m->$name);
		$this->html .= <<<HTML
<div class="form-group">
	<label for="$id" class="col-sm-2 control-label">$label</label>
	<div class="col-sm-10">\n
HTML;
		foreach($options as $k=>$v){
			$s = '';
			if($k == $val){
				$s = ' checked="checked"';
			}
		$this->html .= <<<HTML
		<label class="radio-inline">
			<input type="radio" id="$id"
				name="$field" value="$k"$s />
			$v
		</label>\n
HTML;
		}
		$this->html .= <<<HTML
	</div>
</div>\n
HTML;
	}
	
	function select($name, $label, $options=array()){
		$id = "input_$name";
		$field = "form[$name]";
		$val = htmlspecialchars($this->m->$name);
		$this->html .= <<<HTML
<div class="form-group">
	<label for="$id" class="col-sm-2 control-label">$label</label>
	<div class="col-sm-10">
		<select name="$field" class="form-control">\n
HTML;
		foreach($options as $k=>$v){
			$s = '';
			if($k == $val){
				$s = ' selected="selected"';
			}
		$this->html .= <<<HTML
			<option value="$k"$s>$v</option>\n
HTML;
		}
		$this->html .= <<<HTML
		</select>
	</div>
</div>\n
HTML;
	}
	
	function datetime($name, $label){
		$id = "input_$name";
		$field = "form[$name]";
		$val = htmlspecialchars($this->m->$name);
		$this->html .= <<<HTML
<div class="form-group">
	<label for="$id" class="col-sm-2 control-label">$label</label>
	<div class="col-sm-10">
		<input type="text" class="form-control datetimepicker" id="$id"
			name="$field" value="$val" />
			<span class="help-block">$placeholder</span>
	</div>
</div>\n
HTML;
	}
}
