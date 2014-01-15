<!-- div.col-md-3.col-md-offset-1.rightbar.participate -->
<div class="row">
	<div class="bookmark">
		<a href="" class="white disabled coming-feature" style="line-height:100%;">
			<span class="bookmark-label">Bookmark</span>
			<div class="ribbon">
				<div class="ribbon-container">
					<div class="ribbon-base"></div>
					<div class="ribbon-left-corner"></div>
					<div class="ribbon-right-corner"></div>
				</div>
			</div>
		</a>
	</div>
	<div class="col-md-8">
		<h3>PARTICIPATE</h3>
	</div>
</div>
@if(Auth::check())
<div class="row participate-sponsor">
	@include('doc.reader.sponsor')
</div>
<div class="row participate-share">
	@include('doc.reader.share')
</div>
@endif
<div class="row participate-action">
	@include('doc.reader.action')
</div>
<div id="participate-notes" class="row participate-notes">
	<h3>Annotations</h3>
</div>