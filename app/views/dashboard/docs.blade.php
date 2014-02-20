@extends('layouts/main')
@section('content')
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="/dashboard">Dashboard</a></li>
			<li class="active">Documents</li>
		</ol>
	</div>
	<div class="row content">
		<div class="col-md-6">
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
		<div class="col-md-6">
			<div class="row">
				<h2>Create Document</h2>
				{{ Form::open(array('url' => 'dashboard/docs', 'method' => 'post', 'id' => 'create-document-form')) }}
				<div class="form-group">
					{{ Form::label('title', 'Title:') . Form::text('title', Input::old('title'), array('placeholder' => 'Document Title', 'class'=>'form-control')) }}
				</div>
				<div class="form-group">
					{{ Form::label('slug', 'Slug:') . Form::text('slug', Input::old('slug'), array('placeholder' => 'Document Slug', 'class' => 'input-slug form-control')) }}
				</div>
				{{ Form::submit('Create Document', array('class' => 'btn')) }}
				{{ Form::token() . Form::close() }}
			</div>
			<div class="row">
				<h2>Import XML Document</h2>
				{{ Form::open(array('url' => 'dashboard/import', 'method' => 'post')) }}
				<div class="form-group">
					<label for="url">URL:</label>
					<input type="url" name="url" id="url" value="{{ Input::old('url') }}" placeholder="Enter URL" class="form-control" />
				</div>
				{{ Form::submit('Import Document', array('class'=>'btn')) }}
				{{ Form::token() . Form::close() }}
			</div>
		</div>
	</div>
@endsection