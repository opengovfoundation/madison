@layout('layouts/main')
@section('content')
	<div class="content col-md-12">
		<ul>
			<li>{{ HTML::link('dashboard/docs', 'Create / Edit Documents') }}</li>
			<li>{{ HTML::link('dashboard/verifications', 'Verify Accounts') }}</li>
		</ul>
	</div>
@endsection