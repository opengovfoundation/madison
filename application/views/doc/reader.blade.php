@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<h1>{{ $doc->title }}</h1>
<div class="row-fluid">
	<div class="span8 well well-large">
		{{-- This is messy.  This should be separated into a partial view. --}}
		<?php function output_tree($parent){
			?>
			<ol>
				<li id="content_<?php echo $parent->id; ?>" class="content_item">
					{{ $parent->content }}
					<?php 
						$children = $parent->content_children()->get();
						if(count($children) > 0){
							foreach($children as $child){
								output_tree($child);
							}
						}
					?>
				</li>
			</ol>
			<?php
		}?>
		@foreach($doc->get_root_content() as $root_content)
			<?php output_tree($root_content); ?>
		@endforeach
	</div>
	<div class="span4 well well-large">
		<div class="row-fluid">
			<div class="span12 notes_wrapper">
				<h3>Suggestions</h3>
				<div class="row-fluid notes_section">
					@foreach($doc->get_all_suggestions() as $note)
					<div id="note_{{ $suggestion->id }}" class="span12 note">
						{{ $note->content }}
						<div class="row-fluid note_votes">
							<div class="span6 note_likes">
								{{ $note->likes }}
							</div>
							<div class="span6 note_likes">
								{{ $note->dislikes }}
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
			<div class="span12 notes_wrapper">
				<h3>Comments</h3>
				<div class="row-fluid notes_section">
					@foreach($doc->get_all_comments() as $note)
					<div id="note_{{ $suggestion->id }}" class="span12 note">
						{{ $note->content }}
						<div class="row-fluid note_votes">
							<div class="span6 note_likes">
								{{ $note->likes }}
							</div>
							<div class="span6 note_likes">
								{{ $note->dislikes }}
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
@endsection