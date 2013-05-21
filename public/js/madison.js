$(document).ready(function(){
	$('input[type=text]:first').focus();

	$('#doc-nav').change(function(){
		var url = $(this).val();
		window.location.href = url;
	});
});

