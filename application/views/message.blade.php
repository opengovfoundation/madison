<?php
	$message = Session::get('message');
?>
@if($message)
	<div class="alert">
		{{ $message }}
	</div>
@endif