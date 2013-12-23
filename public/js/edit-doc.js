/**
 * 	Madison Javascript Functions for Administrative document editing
 *
 * 	@copyright Copyright &copy; 2013 by The OpenGov Foundation
 *	@license http://www.gnu.org/licenses/ GNU GPL v.3
 */

function saveDocument(){
}

$(document).ready(function(){
	try{
		$('.sortable').nestedSortable({
			handle: 'div',
			items: 'li',
			toleranceElement: 'div',
			maxLevels: 0
		});
	}
	catch(err){
		console.log(err);
	}

    $('#doc_content_form').submit(saveDocument);

    $('#save_doc').click(saveDocument);
});

function add_doc_handler(){
	var doc_id = $('input[name="doc_id"]').val();
	var parent_id = $(this).siblings('input[name="content_id"]').val();
	var success;
	var new_id = -1;
	var caller = $(this);
	var token = $('input[name="csrf_token"]').val();
	
	$.post('/dashboard/content', {'content': 'New Content', 'doc_id': doc_id, 'parent_id': parent_id, 'csrf_token': token}, function(data){
		data = JSON.parse(data);
		success = data.success;
		new_id = data.id;
		
		if(success == false){
			return;
		}
		
		//Create parent doc item
		var doc_item = $('<li class="doc_item"></li>');

		//Create objects to be appended to parent doc item
		var sib1 = $('<span></span>').append($('<img style="cursor:pointer;" src="/img/arrow-down.png" alt="Dropdown Arrow" class="dropdown_arrow" />').click(dropdown_arrow_handler)).append(' New Content');
		var sib2 = $('<input type="hidden" name="content_id" value="' + new_id + '" />');
		var sib3 = $('<p class="add_doc_item">+</p>').click(add_doc_handler);
		var sib4 = $('<p class="delete_doc_item">x</p>').click(delete_doc_handler);
		var sib5 = $('<p class="doc_item_content expanded"><textarea>New Content</textarea></p>');

		//Append a div and the child elements to the parent doc item
		doc_item.append($('<div class="sort_handle"></div>').append([sib1, sib2, sib3, sib4, sib5]));

		//Append the parent doc item to the list
		var childList = caller.parent('div').parent('.doc_item').find('> ol');

		//Create the child list if there is none
		if(childList.length == 0){
			caller.parent('div').parent('.doc_item').find('.sort_handle').after('<ol></ol>');
		}

		//Add the child
		caller.parent('div').parent('.doc_item').find('> ol').prepend(doc_item);
		
	});
}
function delete_doc_handler(){
	var toRemove = $(this).parent('.sort_handle').parent('.doc_item');
	var children = toRemove.find('.doc_item');
	var deletedIds = new Array();

	$(children).each(function(){
		deletedIds.push($(this).children('.sort_handle').children('input[name="content_id"]').val());
	});
	deletedIds.push($(this).siblings('input[name="content_id"]').val());

	if($('#deleted_ids').val().length > 0){
		var prevIds = $('#deleted_ids').val().split();
		$('#deleted_ids').val(_.union(prevIds, deletedIds));
	}else{
		$('#deleted_ids').val(deletedIds);
	}

	toRemove.remove();
}
function dropdown_arrow_handler(){
	var sibling_content = $(this).parent('span').siblings('.doc_item_content');

	if(sibling_content.hasClass('expanded')){
		sibling_content.removeClass('expanded');
	}
	else{
		sibling_content.addClass('expanded');
	}
}