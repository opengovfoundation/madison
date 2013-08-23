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
				<div class="row note note-{{ $note->type }} note_{{ $note->section_id }}">
					<div class="col-md-3">
						<img src="http://www.gravatar.com/avatar/{{ md5(strtolower(trim($note->user->email))) }}" class="img-rounded img-responsive" alt="" />
					</div>
					<div class="col-md-9">
						<p>{{ $note->content }}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>
