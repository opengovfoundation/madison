@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<div class="row-fluid well well-large">
	<div class="span12">
		<div class="span6">
			<h2>Documents</h2>
			<ul>
				@if(0 == count($docs))
					<li>No Documents Found</li>
				@else
					@foreach($docs as $doc)
						<li>
							<?php echo HTML::link('dashboard/docs/' . $doc->id, $doc->title); ?>
						</li>
					@endforeach
				@endif
				
			</ul>
		</div>
		<div class="span6">
			<h2>Create Document</h2>
			{{ Form::open('dashboard/docs', 'post') }}
			{{ Form::label('title', 'Title:') . Form::text('title', Input::old('title'), array('placeholder' => 'Document Title')) }}
			{{ Form::label('slug', 'Slug:') . Form::text('slug', Input::old('slug'), array('placeholder' => 'Document Slug', 'class' => 'input-slug')) }}
			<div class="form-actions">
				{{ Form::submit('Create Document', array('class' => 'btn')) }}
			</div>
			{{ Form::token() . Form::close() }}
		</div>
	</div>
</div>
@endsection