<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	 <h4 class="modal-title">{{ trans('messages.annotationcomplete') }}</h4>
</div>
<div class="modal-body">
	<p><b>{{ trans('messages.congratulations') }}</b> {{ trans('messages.successfullyannotated') }}</p>
</div>
<div class="modal-footer">
	<button id="annotationModalCloseBtn" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('messages.thanks') }}</button>
</div>
<script>
	$('#annotationModalCloseBtn').on('click', function() {

		$.ajax({
			type : "POST",
			url : "/modals/annotation_thanks",
			error : function(xhr, status) {
				alert("There was a small error - you may see the thank you message again");
			}
		});
		
	});
</script>
