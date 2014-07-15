@if($errors->has())
	@foreach($errors->all() as $error)
		<div class="alert alert-danger">
			{{ $error }}
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