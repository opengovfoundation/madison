//Page load functions
$(document).ready(function(){
	/**
	*	Selecting line items
	*/
	$('.content_item').click(function(){
		
		//If it's already selected, deselect it
		if($(this).hasClass('selected')){
			$(this).removeClass('selected');
			
			//Hide || show appropriate participate options
			$('#note-btn-wrapper').addClass('hidden');
			$('#note-content-wrapper').addClass('hidden');
				$('#note_content').val('');
			$('#noselect-msg').removeClass('hidden');
			$('#section_id').val('');
			$('#type').val('');
			$('.note').removeClass('hidden');
			return;
		}
		
		//Deselect others and select current item
		$('.selected').removeClass('selected');
		$(this).addClass('selected');
		
		//Add section number to participate hidden input
		var section_id = $(this).attr('id').replace('content_', '');
		$('#section_id').val(section_id);
		
		//Hide || show appropriate participate options
		$('#noselect-msg').addClass('hidden');
		$('#note-btn-wrapper').removeClass('hidden');
		
		//Hide || show relevant notes
		$('.note').addClass('hidden');
		$('.note_' + section_id).removeClass('hidden');
	});
	
	/**
	*	Adding Comment / Suggestion
	*/
	$('.add-note-btn').click(function(){
		//No section selected - shouldn't happen if buttons are shown
		if($('#section_id').val() == ''){
			participate_error('Please select a section');
			return;
		}
		
		//Add correct note type to hidden input
		var type = $(this).attr('id').replace('-btn', '');
		$('#type').val(type);
		
		if(type == 'suggestion'){
			var section_content = $('#content_' + $('#section_id').val()).html();
			$('#note_content').val(section_content);
		}else if(type == 'comment'){
			$('#note_content').val('');
		}else{
			participate_error('Invalid note type');
			return;
		}
		
		$('#note-content-wrapper').removeClass('hidden');
		
	});
});

function participate_error(message){
	$('#note-btn-wrapper').addClass('hidden');
	$('#note-content-wrapper').addClass('hidden');
		$('#note_content').val('');
	$('#noselect-msg').removeClass('hidden');
	$('#section_id').val('');
	$('#type').val('');
	$('#participate-msg').html(message);
	
}