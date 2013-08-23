<div class="row">
	<div class="col-md-3 col-sm-3">
		<select id="doc-nav">
			<option value="">Select a recent bill</option>
			<option value="docs/">View All</option>
			@foreach($docs as $doc)
				<option value="doc/{{ $doc->slug }}">{{ $doc->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-md-3 col-sm-3">
		<input type="button" class="black disabled coming-feature" value="Request a Bill" />
	</div>
	<div class="col-md-3 col-sm-3">
		<a href="" class="disabled white coming-feature">Advanced Bill Search &gt;&gt;</a>
	</div>
	<div class="col-md-3 col-sm-3">
		<input type="search" class="disabled coming-feature" placeholder="Search"/>
	</div>
</div>
