@extends('layouts/main')
@section('content')
<h1>Annotation</h1>
	<div class="content col-md-12">
		<div class="row">
			
			<h3>Original Passage</h3>
			<div class="col-md-12">
				<blockquote>{{ $quote }}</blockquote>
			</div>
		</div>
		<div class="row">
			<h3>Annotation</h3>
			<div class="col-md-12">
				
				<p>{{ $text }}</p>
				
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<a href="{{ URL::to('user/' . $user_id) }}">{{ $user_name }}</a>
			</div>
			<div class="col-md-3">
				<p><span id="note_{{$note_id}}_likes">{{ $likes }}</span> likes, <span id="note_{{$note_id}}_dislikes">{{ $dislikes }}</span> dislikes</p>
			</div>
			<div class="col-md-3">
				@if(Auth::check())
				<div class="note-tools-large">
					<div id="flag_{{$note_id}}" class="note-tool flag-large-btn" title="Flag as Inappropriate"></div>
					<div id="dislike_{{$note_id}}" class="note-tool dislike-large-btn" title="Dislike"></div>
					<div id="like_{{$note_id}}" class="note-tool like-large-btn"></div>
				</div>
				@endif
			</div>
		</div>
		@if(Auth::check())
		<div class="row">
			<div class="col-md-12">
				{{ Form::open(array('url'=>'note/' . $note_id, 'method'=>'post')) }}
					<textarea name="note_content" id="note_content" ></textarea>
					<input type="hidden" name="parent_id" value="{{$note_id}}" />
					<input type="hidden" name="type" value="comment"/>
					{{ Form::submit('Comment', array('class'=>'btn')) }}
				{{ Form::token() . Form::close() }}
			</div>
		</div>
		@endif
		@if(isset($child_notes))
			<div class="row">
				@foreach($child_notes as $child_note)
					<div class="col-md-12">
						<p>{{ $child_note->content }}</p>
					</div>
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-6">
								<a href="{{ URL::to('user/' . $child_note->user->id)}}">{{ $child_note->user->fname . ' ' . substr($child_note->user->lname, 0, 1) }}</a>
							</div>
							<div class="col-md-3">
								<p><span id="note_{{$child_note->id}}_likes">{{ $child_note->likes }}</span> likes, <span id="note_{{$child_note->id}}_dislikes">{{ $child_note->dislikes }}</span> dislikes</p>
							</div>
							<div class="col-md-3">
								@if(Auth::check())
								<div class="comment-tools">
									<div id="flag_{{$child_note->id}}"class="note-tool flag-btn" title="Flag as Inappropriate"></div>
									<div id="dislike_{{$child_note->id}}" class="note-tool dislike-btn" title="Dislike"></div>
									<div id="like_{{$child_note->id}}" class="note-tool like-btn" title="Like"></div>
								</div>
								@endif
							</div>
						</div>
					</div>
				@endforeach
			</div>
		@endif
	</div>
	{{ HTML::script('js/note.js') }}
<script type="text/javascript">
	$(document).ready(function(){
		$('.note-tool').click(function(){
			
			var note_info = $(this).attr('id').split('_');
			var meta_type = note_info[0];
			var note_id = note_info[1];
			var clicked = $(this);
			var inputs = {'meta_type': meta_type, 'csrf_token': $('input[name="csrf_token"]').val()};
			
			$.ajax({
				url: '{{ URL::to("note/") }}' + note_id,
				type: 'PUT',
				contentType: 'application/json',
				data: JSON.stringify(inputs),
				success: function(data){
					data = JSON.parse(data);
					success = data.success;
					
					if(success == true){
						if(clicked.hasClass('selected')){
							clicked.removeClass('selected');
						}else{
							clicked.addClass('selected');
						}
						clicked.siblings().removeClass('selected');
						
						var likes = $('#note_' + note_id + '_likes').html(data.likes);
						var dislikes = $('#note_' + note_id + '_dislikes').html(data.dislikes);
					}else{
						console.log(data.msg);
					}
				}
			});
		});
	});
</script>
@endsection