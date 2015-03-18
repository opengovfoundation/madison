<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	 <h4 class="modal-title">Annotation Complete!</h4>
</div>
<div class="modal-body">
	<p><b>Congratulations!</b> You have successfully annotated a piece of legislation in progress. The sponsor of this document will be notified.</p>
</div>
<div class="modal-footer">
	<button id="annotationModalCloseBtn" type="button" class="btn btn-default" data-dismiss="modal">Thanks!</button>
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
