<div class="row-fluid">
	<div class="span10">
		<h4>Suggestions &amp; Comments</h4>
	</div>
	<div class="span2">
		<a href="" class="disabled white" style="position:relative; top:10px;">View All</a>
	</div>
</div>
<div class="row-fluid">
	<div class="span6 legend legend-comments"><p>Comments</p><div class="legend-color"></div></div>
	<div class="span6 legend legend-suggestions"><p>Suggestions</p><div class="legend-color"></div></div>
</div>
<div class="row-fluid">
	<div class="span12 notes-wrapper">
		@foreach($notes as $note)
			<div class="row-fluid note note-{{ $note->type }}">
				<div class="span1 spacer"></div>
				<div class="span1">
					<img src="http://www.gravatar.com/avatar/{{ md5(strtolower(trim($note->user->email))) }}" alt="" />
				</div>
				<div class="span10">
					<p>{{ $note->content }}</p>
				</div>
			</div>
		@endforeach
	</div>
</div>