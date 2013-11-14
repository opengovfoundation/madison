@if(is_object($errors) && isset($errors->messages))
	@foreach($errors->messages as $e)
		<div class="alert alert-danger">
			{{ $e[0] }}
		</div>
	@endforeach
@endif
<?php
	$error = Session::get('error');
?>
@if($error)
	<div class="alert alert-danger">
		{{ $error }}
	</div>
@endif