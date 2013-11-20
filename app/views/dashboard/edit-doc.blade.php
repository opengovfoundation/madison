@extends('layouts/main')
@section('content')
<div class="row content">
	<div class="col-md-12">
		{{ Form::open(array('url' => 'dashboard/docs/' . $doc->id, 'method' => 'put', 'id'=>'doc_content_form')) }}
		<ol id="doc_list" class="sortable doc_list">
			<?php
				$contents = $doc->content()->where('parent_id')->get();
				foreach($contents as $content){
					DocContent::print_admin_list($content);
				}
			?>
		</ol>
		{{ Form::hidden('doc_id', $doc->id) }}
		<div class="form_actions">
			{{ Form::submit('Save Doc', array('class'=>'btn')) }}
		</div>
		{{ Form::token() . Form::close() }}
		<div id="save_message" class="alert hidden"></div>
	</div>
</div>
{{ HTML::script('js/edit-doc.js') }}
@endsection