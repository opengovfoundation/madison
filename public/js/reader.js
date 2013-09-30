//Page load functions
$(document).ready(function(){
	/**
	*	Set has-notes class for .content_items with notes and create note count badges
	*/
	$.each($('.note'), function(){
		var id = $(this).attr('data-contentitem');
		var badge = $('#badge_' + id);
		
		$('#content_' + id).addClass('has-notes');
		
		
		if(badge.html() == ''){
			badge.html('1');
		}else{
			var badgeNum = parseInt(badge.html(), '10')
			badgeNum++;
			badge.html(badgeNum);
		}
	});
	
	/**
	*	Binding for selecting line items
	*/
	$('.content_item').click(function(){
		
		//If it's already selected, deselect it
		if($(this).hasClass('selected')){
			$(this).removeClass('selected');
			
			//Hide || show appropriate participate options
			$('#action-intro').removeClass('hidden');
			$('#note_content').val('');
			$('#note_content').addClass('hidden');
			$('#note-submit-btn').addClass('hidden');
			$('#comment-btn-wrapper').addClass('disabled').removeClass('active');
			$('#suggestion-btn-wrapper').addClass('disabled').removeClass('active');
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
		$('#suggestion-btn-wrapper').removeClass('disabled');
		$('#comment-btn-wrapper').removeClass('disabled');
		$('#action-intro').addClass('hidden');
		
		//Hide || show relevant notes
		$('.note').addClass('hidden');
		$('.note_' + section_id).removeClass('hidden');
	});
	
	/**
	*	Binding for adding comment / suggestion
	*/
	$('.action-btn').click(function(){
		//No section selected - shouldn't happen if buttons are shown
		if($('#section_id').val() == ''){
			participate_error('Please select a section');
			return;
		}
		
		//Add correct note type to hidden input
		var type = $(this).children('input[name="actions"]').attr('id').replace('-btn', '');
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
		
		$('#note_content').removeClass('hidden');
		$('#note-submit-btn').removeClass('hidden');
		
	});
	
	$('.action-btn-wrapper').hover(function(){
		if(!$(this).children('.action-btn').first().hasClass('disabled')){
			$(this).tooltip('distroy');
		}
	});
	
	$('.action-btn-wrapper').tooltip({
		'animation': true,
		'placement': 'bottom',
		'title': 'Please select a section.'
	});
});

function participate_error(message){
	$('#action-intro').removeClass('hidden');
	$('#section_id').val('');
	$('#type').val('');
	$('#note_content').val('');
	$('#note_content').addClass('hidden');
	$('#note-submit-btn').addClass('hidden');
	$('#participate-msg').html(message);
	
}