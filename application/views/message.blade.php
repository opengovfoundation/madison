<?php
	$message = Session::get('message');
?>
@if($message)
<div class="span12">
	<div class="alert">
		{{ $message }}
	</div>
</div>
@endif