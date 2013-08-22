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

	$('.disabled').tooltip({
		'animation': true,
		'placement': 'bottom',
		'title': 'Coming Soon!'
	});
	
});

