@extends('layouts/main')
@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ol class="breadcrumb">
					<li><a href="/"><i class="icon icon-home"></i> {{ trans('messages.home')}}</a></li>
					<li class="active">{{ trans('messages.document') }}s</li>
				</ol>
			</div>
			@if(Auth::user()->hasRole('Independent Sponsor') || Auth::user()->groups()->exists())
				<div class="col-md-8 admin-document-list">
					<h2>{{ trans('messages.document') }}s</h2>
					<ul>
						@if($doc_count == 0)
							<li>{{ trans('messages.nodocuments') }}</li>
						@else
							{{ trans('messages.indiedocs') }}:
							@foreach($documents['independent'] as $doc)
								<li>
									<?php echo HTML::link('documents/edit/' . $doc->id, $doc->title); ?>
								</li>
							@endforeach
							@foreach($documents['group'] as $groupname=>$groupdocuments)
								{{ trans('messages.group') }} '{{ $groupname }}'
								@if(empty($groupdocuments))
								<li>
									{{ trans('messages.nogroupdocs') }}
								</li>
								@endif
								@foreach($groupdocuments as $doc)
									<li>
										<?php echo HTML::link('documents/edit/' . $doc->id, $doc->title); ?>
									</li>
								@endforeach
							@endforeach
						@endif
					</ul>
				</div>
				<div class="col-md-4 admin-add-documents">
					<h3>{{ trans('messages.createdoc') }}</h3>
					{{ Form::open(array('url' => 'documents/create', 'method' => 'post', 'id' => 'create-document-form')) }}
					<div class="form-group">
						{{ Form::label('title', Lang::get('messages.title')) . Form::text('title', Input::old('title'), array('placeholder' =>  Lang::get('messages.doctitle'), 'class'=>'form-control')) }}
					</div>
					{{ Form::submit(Lang::get('messages.createdoc'), array('class' => 'btn', 'name' => 'createdoc')) }}
					{{ Form::token() . Form::close() }}
				</div>
			@else
				<div class="col-md-12">
					<h1>{{ trans('messages.document') }}s</h1>
					<p>{{ trans('messages.besponsor') }} <a href="/documents/sponsor/request">{{ trans('messages.reqindepsponsor') }}</a></p>
				</div>
			@endif
		</div>
	</div>
@endsection
