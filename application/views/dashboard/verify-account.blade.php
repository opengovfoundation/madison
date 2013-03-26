@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<div class="row-fluid well well-large">
	<div class="span12">
		<ul>
			<li><?php echo HTML::link('dashboard/edit-nav', 'Edit Navigation Bar'); ?></li>
			<li><?php echo HTML::link('dashboard/edit-doc', 'Create / Edit Documents'); ?></li>
			<li><?php echo HTML::link('dashboard/verify-account', 'Verify Accounts'); ?></li>
		</ul>
	</div>
</div>
@endsection