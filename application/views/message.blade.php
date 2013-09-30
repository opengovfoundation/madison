<?php
	$message = Session::get('message');
?>
@if($message)
	<div class="alert alert-info">
		{{ $message }}
	</div>
@endif