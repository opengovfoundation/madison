@layout('layouts/main')
@section('content')
<div class="row-fluid well well-large">
	<div class="span12">
		<ul>
			<li><?php echo HTML::link('dashboard/nav', 'Edit Navigation Bar'); ?></li>
			<li><?php echo HTML::link('dashboard/docs', 'Create / Edit Documents'); ?></li>
			<li><?php echo HTML::link('dashboard/verifications', 'Verify Accounts'); ?></li>
		</ul>
	</div>
</div>
@endsection