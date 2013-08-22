<div class="row-fluid">
	<div class="span12"><h4>Edit / Comment</h4></div>
</div>
<div class="row-fluid">
	@if(Auth::check())
	<div class="row-fluid">
		<div class="span6">Edit</div>
		<div class="span6">Comment</div>
	</div>
	@else
	<div class="span12">
		<p>Please {{ HTML::link('login', 'Login') }} to participate</p>
	</div>
	@endif
</div>

