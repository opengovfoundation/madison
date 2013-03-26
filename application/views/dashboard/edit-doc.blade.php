@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<div class="row-fluid well well-large">
	<div class="span12">
		{{ Form::open('dashboard/docs/' . $doc->id, 'put', array('id'=>'doc_content_form')) }}
		<ol id="doc_list" class="sortable doc_list">
			<?php
				foreach($doc->content()->get() as $content){
					if($content->parent_id == null){
						DocContent::print_admin_list($content);
					}	
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
@endsection