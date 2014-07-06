@extends('layouts/main')
@section('content')
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="/">Home</a></li>
			<li class="active">Documents</li>
		</ol>
	</div>
	<div class="row content">
		@if(Auth::user()->hasRole('Independent Sponsor'))
			<div class="col-md-8 admin-document-list">
				<h1>Documents</h1>
				<ul>
					@if(0 == count($docs))
						<li>No Documents Found</li>
					@else
						@foreach($docs as $doc)
							<li>
								<?php echo HTML::link('documents/edit/' . $doc->id, $doc->title); ?>
							</li>
						@endforeach
					@endif
				</ul>
			</div>
			<div class="col-md-4 admin-add-documents">
				<div class="row">
					<h2>Create Document</h2>
					{{ Form::open(array('url' => 'documents/create', 'method' => 'post', 'id' => 'create-document-form')) }}
					<div class="form-group">
						{{ Form::label('title', 'Title:') . Form::text('title', Input::old('title'), array('placeholder' => 'Document Title', 'class'=>'form-control')) }}
					</div>
					{{ Form::submit('Create Document', array('class' => 'btn', 'name' => 'createdoc')) }}
					{{ Form::token() . Form::close() }}
				</div>
			</div>
		@else
			<div class="col-md-12">
				<h1>Documents</h1>
				<p>Want to be a document sponsor? <a href="/documents/sponsor/request">Request to be an Independent Sponsor</a></p>
			</div>
		@endif
	</div>
@endsection
