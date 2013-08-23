$(document).ready(function(){
	//Focus on first input element
	$('input[type=text]:first').focus();

	//Document select navigation
	$('#doc-nav').change(function(){
		var url = $(this).val();
		window.location.href = window.location.origin + '/' + url;
	});
	
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

