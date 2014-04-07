@if(Auth::check())
<div id="participate-comment" class="row participate-comment">
	@include('doc.reader.comment')
</div>
@endif
<div id="participate-activity" class="row participate-activity">
	@include('doc.reader.activity')
</div>

