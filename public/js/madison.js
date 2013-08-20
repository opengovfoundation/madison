$(document).ready(function(){
	//Focus on first input element
	$('input[type=text]:first').focus();

	//Document select navigation
	$('#doc-nav').change(function(){
		var url = $(this).val();
		window.location.href = url;
	});
	
	//Disable links with the 'disabled' class
	$('a.disabled').click(function(event){
		event.preventDefault();
	});
	
	//Add a tooltip to disabled links - "This function coming soon!"
	$('a.disabled').qtip({
			content: 'This function coming soon!',
			position:{
				corner: {
					target: 'bottomMiddle',
					tooltip: 'topMiddle'
				}
			},
			show: 'mouseover',
			hide: 'mouseout'
	});
	
});

