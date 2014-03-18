@if(Auth::check())
<div id="participate-comment">
	@include('doc.reader.comment')
</div>
@endif
<div id="participate-activity" class="participate-activity">
	@include('doc.reader.activity')
</div>

