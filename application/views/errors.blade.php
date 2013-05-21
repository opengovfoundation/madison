@if($errors->messages)
	@foreach($errors->messages as $e)
	<div class="span12">
		<div class="alert alert-error">
			{{ $e[0] }}
		</div>
	</div>
	@endforeach
@endif
<?php
	$error = Session::get('error');
?>
@if($error)
<div class="span12">
	<div class="alert alert-error">
		{{ $error }}
	</div>
</div>
@endif