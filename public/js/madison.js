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

    replace_markdown();
});


function replace_markdown(){
    var converter = new Markdown.Converter();

    $('.markdown').each(function(i, item){
        $(item).html( converter.makeHtml($(item).text()) );
    });
}
