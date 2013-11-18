$(document).ready(function(){
	//Focus on first input element
	$('input:first').focus();
	
	//Disable links with the 'disabled' class
	$('a.disabled').click(function(event){
		event.preventDefault();
	});

	$('.coming-feature').tooltip({
		'animation': true,
		'placement': 'bottom',
		'title': 'Coming Soon!'
	});
	
});

