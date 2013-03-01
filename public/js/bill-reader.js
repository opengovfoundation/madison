// JavaScript Document

/* GRAB BILL SECTION FROM SESSION BILL OBJECT
=====================================================================*/
function get_section(sect_id, sel, view)
{
	sect_id = sect_id.replace('rsec-', '');
	$('.reader-section-el').removeClass('reader-section-el-active');
	$('#reader-content').addClass('reader-content-loading');
	$('#section-content').html('');
	
	$.post('inc/jquery.php', {'action':'get-section', 'sect_id':sect_id, 'view':view}, function(res) 
	{
		$('#reader-content').removeClass('reader-content-loading');
		$('#section-content').html(res.data.content);
		$('#rsec-'+sect_id).addClass('reader-section-el-active');
		init_reader();
		if(sel != null)
			$('#'+sel).click();
	}, 'json');
}

/* SET UP PASSAGE CLICK LISTENERS

* NEED TO DO: *
1. UPDATE LINK URL IF POPUP IS UP WHEN USER SWITCHS BETWEEN PASSAGES
2. UPDATE FACEBOOK SHARE AND TWITTER SHARE URL ON PASSAGE SWITCH
=====================================================================*/
function init_reader() {
	$('#reader-content ol li span').click(function() 
	 {
		if($(this).hasClass('selected'))	
		{
			$(this).removeClass('selected');
			selected = 0;
			
			$('#choose-note-type, #suggestion-frm, #comment-frm').hide();
			$('#frm-select').show();
		}
		else
		{
			$('.selected').removeClass('selected');
			
			$(this).addClass('selected');
			selected  = ($(this).attr('id'));
			
			selection = strip_html($(this).html());
			
			$('#suggestion').val(selection);

			$('#choose-note-type').show();
			$('#frm-select, #suggestion-frm, #comment-frm').hide();
			
			$('#suggestion_preview_original').html(selection);
			
			//ITEM 2
			//$('.fb-like').attr('data-href', 'test');
			//FB.XFBML.parse();
		}
		
		load_notes(selected);
	});
}

/* CLEAN STRING OF KNOWN HTML
=====================================================================*/
function strip_html(str)
{
	str = str.replace('<ins>', '');
	str = str.replace('</ins>', '');
			
	if(str.indexOf('<div class="note-count">') > -1)
		str = str.substring(0, str.indexOf('<div class="note-count">')) + str.substring(str.indexOf('</div>') + 6);
	
	while(str.indexOf('<del>') > -1)
		str = str.substring(0, str.indexOf('<del>')) + str.substring(str.indexOf('</del>') + 7);
	 
	return str;
}

/* LOAD ALL NOTES OR ALL THE NOTES FOR A SELECTED PASSAGE
=====================================================================*/
function load_notes(id)
{
	id = id == null || id == undefined ? 0 : id;
	$('#suggestions, #num-suggestions, #comments, #num-comments').html('');
	$('#suggestion-stats, #comment-stats').hide();
	$('#suggestion-loader, #comment-loader').show();
	
	$.post('inc/jquery.php', {'action':'get-notes-by-part', 'part_id':id}, function(res) 
	{	
		query_str = window.location.search;
		base_uri  = window.location.pathname;
		
		/*Fix for using the homepage as inital bill */
		if(base_uri == '/'){
			base_uri = '/' + res.slug;
		}

		tools 	  = '<div class="note-tools">';
		if(loggedin)
			tools += '<div class="note-meta-tools"><div class="flag-btn" title="Flag as Inappropriate"></div><div class="dislike-btn" title="Dislike"></div><div class="like-btn" title="Like!"></div></div>';

		tools 	 += '<div class="right" style="width:100px;">';
		
		n_types = ['comments', 'suggestions'];
		for(i in n_types)
		{
			n_type 	 = n_types[i];
			n_type_s = n_type.substring(0, n_type.length - 1);
			
			html     = '';
			if(res.data[n_type][n_type].length > 0)
			{
				for(j in res.data[n_type][n_type])
				{
					note  = res.data[n_type][n_type][j];
					html += '<div id="'+note.id+'" class="'+n_type_s+'"><div class="'+n_type_s+'-content">'+note.note+'</div>'+
								'<div class="'+n_type_s+'-info"><strong>'+note.user+'</strong> &nbsp;&nbsp; <span style="font-style:italic">'+note.time_stamp+'</span></div>'+
								tools+note.likes+' likes, '+note.dislikes+' dislikes</div></div></div>';
				}
			}
			else
				html = '<div style="padding:10px;">'+(n_type == 'comments' ? 'Add a Comment' : 'Make a Bill Edit')+' Above</div>';
				
			$('#num-'+n_type).html(res.data[n_type]['total']);
			$('#'+n_type).html(html);
		}
		
		$('#suggestion-loader, #comment-loader').hide();
		$('#suggestion-stats, #comment-stats').show();
		
		$('.suggestion-content').click(function() { document.location = "http://"+window.location.host+base_uri+"/suggestion/"+$(this).parent().attr('id');});
		$('.comment-content').click(function() { document.location = "http://"+window.location.host+base_uri+"/comment/"+$(this).parent().attr('id');});
		
		$('.suggestion, .comment').hover(function () {$('.note-tools', this).show();},
			  							 function () {$('.note-tools', this).hide();});
		if(loggedin)
			$('.flag-btn, .dislike-btn, .like-btn').click(function(){ ldf_note($(this)); });

	}, 'json');
}

/* LIKE DISLIKE OR FLAG A SUGGESTION OR COMMENT
=====================================================================*/
function ldf_note(el)
{
	note_id = $(el).parent().parent().parent().attr('id');
	type	= $(el).attr('class');
	type	= type.replace('-btn', '');

	$.post('inc/jquery.php', {'action':'add-ldf-note', 'note':note_id, 'type':type}, function(res) 
	{
		type = type == 'like' ? 'Liked' : (type == 'dislike' ? 'Disliked' : 'Flagged');
		$(el).parent().html('<strong>Note '+type+'!</strong>');
	}, 'json');	
}

/* CHOOSE BETWEEN COMMENTING ON A PASSAGE OR SUGGESTING AN EDIT TO A PASSAGE
=====================================================================*/
function choose_note(n)
{
	$('#suggestion-frm, #comment-frm, #note-submitted').hide();
	$('#'+n+'-frm').show();
	$('#note-type').val(n);
}

/* ADD A NOTE TO A PASSAGE
=====================================================================*/
function add_note(type)
{
	post = {'action':'add-note', 'note':$('#'+type).val(), 'part_id':selected, 'type':type, 'sect_id':section};
	
	if(type == 'suggestion')
		post.why = $('#suggestion-comment').val();
	
	$.post('inc/jquery.php', post, function(res) 
	{
		load_notes(selected);
		$('#note-submitted').show();
		$('#suggestion-frm, #comment-frm').hide();
		
		$('#'+type).val('');
		$('#suggestion').val(strip_html($('#'+selected).html()));
		
		if(type == 'suggestion')
		{
			$('#gbt_lightbox_close').click();
			get_section(section, selection);
		}
		
	}, 'json');
}

/* ADD A NOTE TO A PASSAGE
=====================================================================*/
function preview_suggestion()
{
	$.post('inc/jquery.php', {'action':'preview-suggestion', 'note':$('#'+$('#note-type').val()).val(), 'part_id':selected}, function(res) 
	{
		$('#suggestion_preview_edit').html(res.data);
    	$('#gbt_lightbox, #gbt_lightbox_content').show();
	},'json');
}

/* SHOW LINK OR SHARE POPUP
=====================================================================*/
function show_popup(p)
{
	if(p == 'link')
	{
		$('#share-popup').hide();
		$('#share').removeClass('share-tools-active');
		$('#short-url').html('');
		
		//THIS SHOULD BE CACHED BE SELECTION INTO AN ARRAY TO AVOID MULITPLE CALLS TO GOOGLE FOR DUPLICATE DATA
		$.post('inc/jquery.php', {'action':'get-short-url', 'base_uri':window.location.pathname, 'sect_id':section, 'selected':selected}, function(res) 
		{
			$('#short-url').html(res.data.id);
		}, 'json');
		
		$('#link-popup').toggle();
	}
	else if(p == 'share')
	{
		$('#link-popup').hide();
		$('#link').removeClass('share-tools-active');
		$('#share-popup').toggle();
		
	}
	
	if(!$('#'+p).hasClass('share-tools-active'))
		$('#'+p).addClass('share-tools-active');
	else
		$('#'+p).removeClass('share-tools-active');
}