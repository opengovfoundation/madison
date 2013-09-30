<?php $success_message = Session::get('success_message'); ?>
@if(!empty($success_message))
	<div class="alert alert-success">
		{{ $success_message }}
	</div>
@endif