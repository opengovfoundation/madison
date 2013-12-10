@extends('layouts/main')
@section('content')
{{ HTML::style('vendor/pagedown/assets/demo.css') }}
{{ HTML::script('vendor/pagedown/assets/Markdown.Converter.js') }}
{{ HTML::script('vendor/pagedown/assets/Markdown.Editor.js') }}
{{ HTML::script('vendor/pagedown/assets/Markdown.Sanitizer.js') }}
<div class="row content">
	<div class="col-md-12">
		{{ Form::open(array('url' => 'dashboard/docs/' . $doc->id, 'method' => 'put', 'id'=>'doc_content_form')) }}
			<input type="hidden" name="content_id" value="{{{ $contentItem->id }}}"/>

			<div class="doc_item_content">
				<div id="wmd-button-bar"></div>
				<textarea class="wmd-input" id="wmd-input" name="content"
					>{{{ $contentItem->content }}}</textarea>
				<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
				<script type="text/javascript">
					$(function () {
						var converter1 = Markdown.getSanitizingConverter();

						var editor1 = new Markdown.Editor(converter1);

						editor1.run();
						console.log('done', editor1);
					});
				</script>
			</div>
		{{ Form::hidden('doc_id', $doc->id) }}
		<div class="form_actions">
			{{ Form::submit('Save Doc', array('name' => 'submit', 'id' => 'submit', 'class'=>'btn')) }}
		</div>
		{{ Form::token() . Form::close() }}
		<div id="save_message" class="alert hidden"></div>
	</div>
</div>
{{ HTML::script('js/edit-doc.js') }}
@endsection