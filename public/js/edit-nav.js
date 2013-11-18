/**
 * 	Madison Javascript Functions for Administrative navigation editing
 * 
 * 	@copyright Copyright &copy; 2013 by The OpenGov Foundation
 *	@license http://www.gnu.org/licenses/ GNU GPL v.3
 */

$(document).ready(function(){
	try{
		$('.sortable').nestedSortable({
			handle: 'div',
			items: 'li',
			toleranceElement: 'div',
			maxLevels: 2
		});
	}
	catch(err){
		console.log(err);
	}
	$('#nav_menu_form').submit(function(){
		var nav = new Array();
		
		//Iterate over each nav item
		$('#nav_list > .nav_item').each(function(){
			//Build the tree for this item
			var navTree = buildNavTree($(this));

			//Push this item's tree to the nav list
			nav.push(navTree);
		});
		
		var csrf_token = $('input[name="csrf_token"]').val();
		
		$.post('/dashboard/nav', {'nav': nav, 'csrf_token' : csrf_token}, function(data){
			
			//Parse json string
			data = JSON.parse(data);

			$('#save_message').html(data.message);
			if(data.success == true){
				$('#save_message').addClass('alert-success');
			}
			else{
				$('#save_message').addClass('alert-error');
			}
			
			$('#save_message').removeClass('hidden');
		});
				
		return false;
	});
	
	//Adding items to Nav menu
	$('#add-docs').click(function(){
		//Add each checked item
		$('.menu_item').each(function(){
			//Retrieve the item's checkbox input
			var checkbox = $(this).find('input[type="checkbox"]');
			

			//Only add the checked items
			if(checkbox.is(':checked')){

				//Get item's data
				var label = $(this).find('.doc-title').text();
				var slug = checkbox.val();
				var type = $(this).find('input[name="type"]').val(); 

				//Append item to nav list
				var listItem = $('<li class="nav_item"></li>');
				var sortHandle = $('<div class="sort_handle"></div>');
				var deleteItem = $('<p class="delete_nav_item">x</p>').click(delete_nav_handler);
				
				sortHandle.append('<span>' + label + '</span>');
				sortHandle.append('<input type="hidden" value="' + slug + '" name="link"/>');
				sortHandle.append('<input type="hidden" value="' + type + '" name="type" />');
				sortHandle.append(deleteItem);
				
				listItem.append(sortHandle);
				
				$('#nav_list').append(listItem);
				//$('#nav_list').append('<li class="nav_item"><div class="sort_handle"><span>' + label + '</span><input type="hidden" value="/' + slug + '" /><p class="delete_nav_item">x</p></div></li>');

				//Uncheck the item
				checkbox.prop('checked', false);
			}
		});
	});
	
	//Saving Navigation
	$('#save_nav').click(function(){
		var csrf_token = $('input[name="csrf_token"]');
		
		//Instantiate nav array
		var nav = new Array();

		//Iterate over each nav item
		$('#nav_list > .nav_item').each(function(){
			//Build the tree for this item
			var navTree = buildNavTree($(this));

			//Push this item's tree to the nav list
			nav.push(navTree);
		});

		//Post the nav menu for saving
		c
	});

	//Delete item from nav menu
	$('.delete_nav_item').click(delete_nav_handler);
});

function delete_nav_handler(){
	var parent = $(this).parent('div').parent('.nav_item');
	parent.remove();
}

//Returns the count of a parent's children
function hasChildren(parent){
	var children = parent.children('ol').children('.nav_item').length;
	
	return children;
}

function buildNavTree(parent){
	var label, link, ret = {};
	
	//get nav item label and link
	label = parent.children('div').children('span').text();
	link = parent.children('div').children('input[name="link"]').val();
	type = parent.children('div').children('input[name="type"]').val();
	
	//Create json object for nav item
	ret = {'label' : label, 'link' : link, 'type' : type};
	
	if(!hasChildren(parent)){
		return ret;
	}
	
	//Instantiate children array
	var childTrees = new Array();
	
	//Iterate through the children items and build a tree for each
	parent.children('ol').children('.nav_item').each(function(){
		var childTree = buildNavTree($(this));
		childTrees.push(childTree);
	});
	
	//Add children to nav item json object
	ret.children = childTrees;
	
	return ret;
}