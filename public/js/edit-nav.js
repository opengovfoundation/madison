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
});

//Adding items to Nav menu
$('#add_nav_items').click(function(){
	
	//Add each checked item
	$('.menu_item').each(function(){
		
		//Retrieve the item's checkbox input
		var checkbox = $(this).find('input[type="checkbox"]');
		
		//Only add the checked items
		if(checkbox.is(':checked')){
			
			//Get item's data
			var label = $(this).text();
			var slug = checkbox.val();
			
			//Append item to nav list
			$('#nav_list').append('<li class="nav_item"><div class="sort_handle"><span>' + label + '</span><input type="hidden" value="/' + slug + '" /><p class="delete_nav_item">x</p></div></li>');
			
			//Uncheck the item
			checkbox.prop('checked', false);
		}
	});
});

//Returns the count of a parent's children
function hasChildren(parent){
	var children = parent.children('ol').children('.nav_item').length;
	
	return children;
}

function buildNavTree(parent){
	var label, link, ret = {};
	
	//get nav item label and link
	label = parent.children('div').children('span').text();
	link = parent.children('div').children('input[type="hidden"]').val();
	
	//Create json object for nav item
	ret = {'label' : label, 'link' : link};
	
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

//Saving Navigation
$('#save_nav').click(function(){
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
	$.post('admin-ajax.php', {'action':'save-nav', 'nav-menu': nav}, function(data){
		//Parse json string
		data = JSON.parse(data);
		
		//Check rows updated
		if(data.rows > 0){//Save Successful
			$('#save_message').html('Nav menu updated.');
		}
		else if(data.rows == 0){//No changes
			$('#save_message').html('Nothing to update.');
		}
		else{//Error
			$('#save_message').html('There was an error updating the nav menu.');
		}
		
		$('#save_message').removeClass('hidden');
	});
});

//Delete item from nav menu
$('.delete_nav_item').click(function(){
	var parent = $(this).parent('div').parent('.nav_item');
	parent.remove();
});