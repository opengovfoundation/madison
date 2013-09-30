$(document).ready(function(){
	$('.note-vote').click(function(){
		
		var note_info = $(this).attr('id').split('-');
		var meta_type = note_info[0];
		var note_id = note_info[1];
		var clicked = $(this);
		var inputs = {'meta_type': meta_type, 'csrf_token': $('input[name="csrf_token"]').val()};
		
		$.ajax({
			url: '/note/' + note_id,
			type: 'PUT',
			contentType: 'application/json',
			data: JSON.stringify(inputs),
			success: function(data){
				data = JSON.parse(data);
				success = data.success;
				
				if(success == true){
					if(clicked.hasClass('selected')){
						clicked.removeClass('selected');
					}else{
						clicked.addClass('selected');
					}
					
					if(meta_type == 'like'){
						$('#dislike-' + note_id).removeClass('selected');
						$('#flag-' + note_id).removeClass('selected');
					}else if(meta_type == 'dislike'){
						$('#like-' + note_id).removeClass('selected');
						$('#flag-' + note_id).removeClass('selected');
					}else{
						$('#like-' + note_id).removeClass('selected');
						$('#dislike-' + note_id).removeClass('selected');
					}
					
					var likes = $('#note-' + note_id + '-likes').html(data.likes);
					var dislikes = $('#note-' + note_id + '-dislikes').html(data.dislikes);
					var flags = $('#note-' + note_id + '-flags').html(data.flags); 
				}else{
					console.log(data.msg);
				}
			}
		});
	});
});