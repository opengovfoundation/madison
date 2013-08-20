<div class="span4">
	<select id="doc-nav" style="margin-left:10px;">
		<option value="">Select a recent bill</option>
		@foreach($docs as $doc)
			<option value="doc/{{ $doc->slug }}">{{ $doc->title }}</option>
		@endforeach
	</select>
</div>
<div class="span2">
	<input type="button" class="black disabled" value="Request a Bill" style="margin-top:10px;"/>
</div>
<div class="span2">
	<a href="" class="disabled" style="margin-top:15px; display:block; color:white;">Advanced Bill Search &gt;&gt;</a>
</div>
<div class="span4">
	<input type="search" class="disabled" placeholder="Search"/>
</div>