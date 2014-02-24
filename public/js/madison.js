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

	$('.tooltip').tooltip({});

	$('.init-popover').popover({
		trigger: 'hover'
	});

    replace_markdown();

    // If we have a document name but no slug,
    // create a slug.
    if($('#create-document-form').length){
		$('#title').blur(function(){
			if($('#slug').val().length < 1 && $('#title').val().length > 0){
			$('#slug').val( clean_slug( $('#title').val() ) );
			}
		});
    }
});

function clean_slug(string)
{
	return string.toLowerCase().replace(/[^a-zA-Z0-9\- ]/g, '').replace(/ +/g, '-');
}

function replace_markdown(){
    var converter = new Markdown.Converter();

    $('.markdown').each(function(i, item){
        $(item).html( converter.makeHtml($(item).text()) );
    });
}
