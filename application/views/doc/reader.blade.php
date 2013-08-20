@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<div class="row-fluid">
	<div class="span12">
		<h1>{{ $doc->title }}</h1>
	</div>
</div>
<div class="row-fluid">
	<div class="span2 spacer"></div>
	<div class="span6 well well-large doc_content @if(Auth::check())logged_in@endif">
		@foreach($doc->get_root_content() as $root_content)
			<?php Helpers::output_tree($root_content); ?>
		@endforeach
	</div>
	<div class="span3 well well-large">
		<div class="row-fluid">
			<div class="span12">
				<h3>Participate</h3>
				{{ Form::open('note') }}
				@if(Auth::check())
					<div id="noselect-msg" class="row-fluid">
						<div class="span12">
							<p>Select part of the bill to make a suggestion or comment.</p>
						</div>
					</div>
					<div id="note-btn-wrapper" class="row-fluid hidden">
						<div class="span6">
							<input id="suggestion-btn" type="button" class="btn add-note-btn" value="Add Suggestion"/>
						</div>
						<div class="span6">
							<input id="comment-btn" type="button" class="btn add-note-btn" value="Add Comment"/>
						</div>
					</div>
					<div id="note-content-wrapper" class="row-fluid hidden">
						<div class="span12">
							<textarea id="note_content" name="note_content"></textarea>
							<input id="note-submit-btn" type="submit" class="btn" value="Submit"/>
						</div>
					</div>
				@else
					<div class="span12">
						<p>Please {{ HTML::link('login', 'Login') }} to partcipate.</p>
					</div>
				@endif
				<input id="parent_id" name="parent_id" type="hidden" value="0"/>
				<input id="doc_id" name="doc_id" type="hidden" value="{{ $doc->id }}"/>
				<input id="type" name="type" type="hidden" />
				<input id="section_id" name="section_id" type="hidden" />
				<div class="row-fluid hidden" id="participate-msg"></div>
				{{ Form::token() }}
				{{ Form::close() }}
			</div>
			<div class="span12 notes_wrapper">
				<h3>Suggestions</h3>
				<div class="row-fluid notes_section">
					@if(count($suggestions) == 0)
						<div class="span12"><p>No suggestions found.</p></div>
					@else
						<div class="span12">
						@foreach($suggestions as $note)
							<div id="note_{{ $note->id }}" class="row-fluid note note_{{ $note->section_id }}">
								<a href="{{ URL::to('note/' . $note->id) }}">
									<div class="row-fluid">
										<div class="span12">
											<?php $section_content = DocContent::find($note->section_id)->content; ?>
											<p class="suggestion_content">{{ StringDiff::diff($section_content, $note->content) }}</p>
										</div>
									</div>
								</a>
								<div class="row-fluid">
									<div class="span12">
										<a href="{{ URL::to('user/' . $note->user->id) }}">{{ $note->user->fname . ' ' . substr($note->user->lname, 0, 1) }}</a>
										<p class="note_date">{{ date('M j, Y g:iA', strtotime($note->created_at)) }}</p>
									</div>
								</div>
								<div class="row-fluid note_votes">
									<div class="span6">
										<p class="note_likes">{{ $note->likes }} likes</p>
										<p class="note_likes">{{ $note->dislikes }} dislikes</p>
									</div>
									<div class="span6">
										<p class="note_likes">{{ $note->note_children()->count() }} comment(s)</p>
									</div>
								</div>
							</div>
						@endforeach
						</div>
					@endif
				</div>
			</div>
			<div class="span12 notes_wrapper">
				<h3>Comments</h3>
				<div class="row-fluid notes_section">
					@if(count($comments) == 0)
						<div class="span12"><p>No comments found.</p></div>
					@else
						<div class="span12">
						@foreach($comments as $note)
							<div id="note_{{ $note->id }}" class="row-fluid note note_{{ $note->section_id }}">
								<a href="{{ URL::to('note/' . $note->id) }}">
									<div class="row-fluid">
										<div class="span12">
											<p class="comment_content">
												{{ $note->content }}
											</p>
										</div>
									</div>
								</a>
								<div class="row-fluid">
									<div class="span12">
										<a href="{{ URL::to('user/' . $note->user->id) }}">{{ $note->user->fname . ' ' . substr($note->user->lname, 0, 1) }}</a>
										<p class="note_date">{{ date('M j, Y g:iA', strtotime($note->created_at)) }}</p>
									</div>
								</div>
								<div class="row-fluid note_votes">
									<div class="span6">
										<p class="note_likes">{{ $note->likes }} likes</p>
										<p class="note_likes">{{ $note->dislikes }} dislikes</p>
									</div>
									<div class="span6">
										<p class="note_likes">{{ $note->note_children()->count() }} comment(s)</p>
									</div>
								</div>
							</div>
						@endforeach
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class="span1 spacer"></div>
</div>
@endsection