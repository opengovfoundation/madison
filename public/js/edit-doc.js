/**
 * 	Madison Javascript Functions for Administrative document editing
 * 
 * 	@copyright Copyright &copy; 2013 by The OpenGov Foundation
 *	@license http://www.gnu.org/licenses/ GNU GPL v.3
 */

function saveDocument(){
	var doc_items = new Array();
	var doc_id = $('#doc_id').val();
	
	$('.doc_item').each(function(){
		var parent = $(this).parent('ol').parent('.doc_item');
		if(parent.length == 0){
			parent_id = 0;
		}
		else{
			parent_id = parent.children('div').children('input[name="content_id"]').val();
		}
		
		var content = $(this).children('div').children('.doc_item_content').children('textarea').val();
		var id = $(this).children('div').children('input[name="content_id"]').val();
		var child_priority =  $(this).parent().find('> .doc_item' ).index(this);
		
		ret = {"id":id, "parent_id":parent_id, "child_priority":"" + child_priority + "", "content":content};
		doc_items.push(ret);
	});
	
	$.post('admin/admin-ajax.php', {"action":"save-doc", "doc_id": doc_id, "doc_items":doc_items}, function(data){
		data = JSON.parse(data);
		
		$('#save_message').html(data.msg).removeClass('hidden');
	});
}

$(document).ready(function(){
	try{
		$('.sortable').nestedSortable({
			handle: 'div',
			items: 'li',
			toleranceElement: 'div',
			maxLevels: 0
		});
		$('.doc_item .dropdown_arrow').click(dropdown_arrow_handler);
		$('.delete_doc_item').click(delete_doc_handler);
		$('.add_doc_item').click(add_doc_handler);
		$('.edit_info_link').click(function(){
			if($(this).hasClass('red')){
				$(this).removeClass('red');
				$('.edit_doc_info').hide();
			}
			else{
				$(this).addClass('red');
				$('.edit_doc_info').show();
			}
		});
		$('#save_doc').click(saveDocument);
	}
	catch(err){
		console.log(err);
	}
});

function add_doc_handler(){
	var doc_id = $('#doc_id').val();
	var parent_id = $(this).siblings('input[name="content_id"]').val();
	var success;
	var new_id = -1;
	var caller = $(this);
	
	//Get new content id
	$.post('admin/admin-ajax.php', {'action':'add-doc-section', 'doc_id':doc_id, 'parent_id':parent_id}, function(data){
		data = JSON.parse(data);
		success = data.success;
		new_id = data.new_id;
		
		if(success == false){
			return;
		}
		
		//Create parent doc item
		var doc_item = $('<li class="doc_item"></li>');
		
		//Create objects to be appended to parent doc item
		var sib1 = $('<span></span>').append($('<img style="cursor:pointer;" src="/assets/i/arrow-down.png" alt="Dropdown Arrow" class="dropdown_arrow" />').click(dropdown_arrow_handler)).append(' New Content');
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
	$('.delete_doc_item').addClass('hidden');
	$('.floatingCirclesG').removeClass('hidden');
	$('.f_circleG').addClass('animated');
	
	var success = true;
	var id = $(this).siblings('input[name="content_id"]').val();
	try{
		$.post('admin/admin-ajax.php', {'action':'delete-doc-section', 'id':id}, function(data){
			data = JSON.parse(data);
			if(data.success == false){
				alert(data.msg);
			}
			else{
				window.location.reload();
			}
		});
	}
	catch(err)
	{
		console.log(err);
	}
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