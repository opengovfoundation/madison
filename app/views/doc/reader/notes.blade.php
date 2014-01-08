<!-- div.row -->
<div class="col-md-12">
	<div class="row">
		<div class="col-md-12">
			<h4>Suggestions &amp; Comments</h4>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<a href="" class="disabled white">View All</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 legend legend-comments"><p>Comments</p><div class="legend-color"></div></div>
		<div class="col-md-6 legend legend-suggestions"><p>Suggestions</p><div class="legend-color"></div></div>
	</div>
	<div class="row">
		<div class="col-md-12 notes-wrapper">
			@foreach($notes as $note)
				<div class="row note note-{{ $note->type }} note_{{ $note->section_id }}" data-contentitem="{{ $note->section_id }}">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-3">
								<a href="{{ URL::to('user/' . $note->user->id)}}">
									<img src="http://www.gravatar.com/avatar/{{ md5(strtolower(trim($note->user->email))) }}" class="img-rounded img-responsive" alt="" />
								</a>
							</div>
							<div class="col-md-9">
								{{ HTML::link('note/' . $note->id, $note->content, array('class'=>'black')) }}
							</div>
						</div>
						@if(Auth::check())
						<div class="row note-votes">
							<div class="col-md-2 col-md-offset-6 note-vote-wrapper">
								<span id="like-{{ $note->id }}" class="glyphicon glyphicon-thumbs-up note-vote note-vote-{{ $note->id }} vote-up {{ $note->usermeta['like'] == 1 ? 'selected' : ''}}"></span>
								<span id="note-{{ $note->id }}-likes">{{ $note->likes }}</span>
							</div>
							<div class="col-md-2 note-vote-wrapper">
								<span id="dislike-{{ $note->id }}" class="glyphicon glyphicon-thumbs-down note-vote vote-down {{ $note->usermeta['dislike'] == 1 ? 'selected' : ''}}"></span>
								<span id="note-{{ $note->id }}-dislikes">{{ $note->dislikes }}</span>
							</div>
							<div class="col-md-2 note-vote-wrapper">
								<span id="flag-{{ $note->id }}" class="glyphicon glyphicon-flag note-vote vote-flag {{ $note->usermeta['flag'] == 1 ? 'selected' : ''}}"></span>
								<span id="note-{{ $note->id }}-flags">{{ $note->flags }}</span>
							</div>
						</div>
						@endif
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
