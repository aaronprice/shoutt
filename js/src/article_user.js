	// Delete comment.
	function del(eid) {
		// Split the id.
		var id = eid.split('_');
		
		// Confirm delete.
		if(!confirm('Are you sure you want to delete this '+id[1]+'?')) 
			return false;
		// Do Delete.
		$.post('/delete', {'id': eid}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the comment
						$('#'+id[1]+'_'+id[2]).fadeOut("slow", function(){ $(this).remove(); });
						$('#num_comments span').html(parseInt($('#num_comments span').html()) - 1);
					}
				});
	}

	

	
	
	
	// Edit comment.
	function edit_comment(id){
		$.post('/edit_comment', {
				'ci': id,
				'comment': $('textarea[name=edit]').val()
			}, function(data) {
				// Callback.
				if(data.message == ''){
					
					// Replace the form with the updated comment.
					$('form[name=edit]').before(data.html).remove();
					
					$('#edit_comment_'+id).unbind('click');
					$('#edit_comment_'+id).html('Edit');
					$('#edit_comment_'+id).click(function() {
						show_hide_edit_form(this.id);
						return false;
					});
					
					$('form[name=edit]').unbind('submit');
					$('#del_comment_'+id).unbind('click');
				} else {
					$('#edit_err').html(data.message);
				}
			}, 
			"json"
		);
	}











	
	
	
	
	
	// Favorite story
	function fav(id) {
	
		// Split the id.
		var id = id.split('_');
		
		// Do Delete.
		$.post('/favorite', 
				{'id': id[1]}, function(data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the story
						$('#fav_'+id[1]).html('Added to Favorites');
						$('#fav_'+id[1]).unbind('click');
					}
				});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Get Participants
	function get_participants(id) {
		var id_arr = id.split('_');
		$('#part_container').css('display', 'block');
		$('#part_container').html('<img src="/img/load.gif" alt="Loading..."/>');
		$.get('/participants', {'sid': id_arr[1]}, function(data){
			$('#part_container').html(data);
			$('#part_holder').fadeOut("slow", function(){ $(this).remove(); });
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Post Comment
	function post_comment(si, type, rt) {
		var id_arr = (rt) ? rt.split('_') : '';
		var id_val = (id_arr == '') ? '' : id_arr[1];
		$('input[type=submit]').parent().append(' <img id="loading" src="/img/load.gif" alt="Loading..."/>');
		$.post('/comment', {
				'si': si,
				'rt': id_val,
				'comment': $('textarea[name='+type+']').val()
			}, function(data) {
				// Remove the loading img.
				$('form img').remove();
				
				// Callback.
				if(data.message == ''){
					if(type == 'reply') {
						if($('#replies_to_'+id_val).length > 0) {
							// Replies exists.
							$('#replies_to_'+id_val).append(data.html);
						} else {
							// display the comment.
							$('#'+rt).parent().parent().parent().parent().append(
												'<ul id="replies_to_'+id_val+'">'+
													data.html+
												'</ul>');
						}
						remove_form(si, rt);
					} else {
						$('#comment_err div').remove();
						$('#no_comment').remove();
						$('textarea[name=comment]').attr('value', '');
						$('#comments').append(data.html);
						// Activate reply
						$('#r_'+data.id).click(function() {
							show_hide_reply_form(si, this.id);
							return false;
						});
					}
					
					switch($('#num_comments span').html()){
						case '0': $('#num_comments').html('<span>1</span> Comment'); break;
						case '1': $('#num_comments').html('<span>2</span> Comments'); break;
						default: $('#num_comments span').html(parseInt($('#num_comments span').html()) + 1); break;
					}
					
					// Activate timestamp.
					$('#comment_'+data.id+' abbr.timeago').timeago();					
					
					$('#promote_comment_'+data.id).click(function(){ vote(this.id); return false; });
					$('#demote_comment_'+data.id).click(function(){ vote(this.id); return false; });
					$('#edit_comment_'+data.id).click(function(){ show_hide_edit_form(this.id); return false; });
				} else {
					$('#'+type+'_err').html(data.message);
				}
			}, 
			"json"
		);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Removing the edit form on cancel or submittion.
	function remove_edit_form(id) {
		var id_arr = id.split('_');
		
		// Remove the form
		$('form[name=edit]').unbind('submit');
		$('form[name=edit]').unbind('growfield');
		$('form[name=edit]').unbind('spellcheck');
		$('form[name=edit]').before(
			'<div id="comment_detail_'+id_arr[2]+'">'+$('#edit_old_'+id_arr[2]+'').html()+'</div>'
		).remove();
		// Change the text back to Reply.
		$('#'+id).html('Edit');
		// Unbind the previous click event.
		$('#'+id).unbind('click');
		// Unbind the previous click event.
		$('#del_comment_'+id_arr[2]).unbind('click');
		
		// Reset the click event to show the form again.
		$('#'+id).click(function() {
			show_hide_edit_form(this.id);
			return false;
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Removing the reply form on cancel or submittion.
	function remove_form(si, id) {
		// Remove the form
		$('form[name=reply]').unbind('submit');
		$('form[name=reply]').unbind('growfield');
		$('form[name=reply]').unbind('spellcheck');
		$('form[name=reply]').remove();
		// Change the text back to Reply.
		$('#'+id).html('Reply');
		// Unbind the previous click event.
		$('#'+id).unbind('click');
		
		// Reset the click event to show the form again.
		$('#'+id).click(function() {
			show_hide_reply_form(si, this.id);
			return false;
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Report Abuse.
	function report_abuse(id) {
		var arr = id.split('_');
		
		$.post('/report_abuse', {'id': id}, function(data) {
				// Callback.
				if(data.message == ''){
					$('#'+id).html('Reported');
					$('#'+id).unbind('click');
				} else {
					alert(data.message);
				}
			}, 
			"json"
		);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Show/Hide the edit form.
	function show_hide_edit_form(id) {
		var id_arr = id.split('_');
	
		if($('#'+id).html() == 'Edit') {
			
			// Remove the form
			$('form[name=edit]').unbind('submit');
			$('form[name=edit]').remove();
			
			// Change the text back to Reply.
			$('.edit').html('Edit');
			// Unbind the previous click event.
			$('.edit').unbind('click');
			
			// Reset the click event to show the form again.
			$('.edit').click(function() {
				show_hide_edit_form(this.id);
				return false;
			});
			
			// Strip HTML
  			var raw_comment = $('#comment_detail_'+id_arr[2]).html().replace(/<br>/gi, "\n").replace(/<\/?[^>]+(>|$)/g, "");
			
			// Create reply form.
			$('#comment_detail_'+id_arr[2]).before(
						'<form action="#" method="post" name="edit" style="margin-left:0">'+
							'<h4>Edit<span> &#151; No HTML Please.</span></h4>'+
							'<textarea name="edit">'+raw_comment+'</textarea>'+
							'<div id="edit_err"></div>'+
							'<input type="submit" value="Save"/>'+
							'<a id="del_comment_'+id_arr[2]+'" class="del_comment" href="#">Delete Comment</a>'+
						'</form>'+
						'<div id="edit_old_'+id_arr[2]+'" style="display:none">'+$('#comment_detail_'+id_arr[2]).html()+'</div>'
			).remove();
			
			// Spellcheck comments
			$('textarea[name=edit]').spellcheck();
			
			// Expanable textarea.
			$('textarea[name=edit]').growfield({'min': 150});
			
			// Activate the delete button.
			$('#del_comment_'+id_arr[2]).click(function() {
				del(this.id);
				return false;
			});
			
			// Activate the form.
			$('form[name=edit]').submit(function() {
				edit_comment(id_arr[2]);
				return false;
			});
			
			// Change 'edit' to 'cancel edit'.
			$('#'+id).html('Cancel Edit');
			
			// Activate cancel reply button.
			$('#'+id).click(function(){
				remove_edit_form(this.id);
				return false;
			});
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Toggle Show/Hide the reply form
	function show_hide_reply_form(si, id){
	
		var id_arr = id.split('_');
	
		if($('#'+id).html() == 'Reply') {
		
			// Remove the form
			$('form[name=reply]').unbind('submit');
			$('form[name=reply]').remove();
			
			// Change the text back to Reply.
			$('.reply').html('Reply');
			// Unbind the previous click event.
			$('.reply').unbind('click');
			
			// Reset the click event to show the form again.
			$('.reply').click(function() {
				show_hide_reply_form(si, this.id);
				return false;
			});
			
			// Create reply form.
			$('#'+id).parent().parent().append(
						'<form action="#" method="post" name="reply">'+
							'<h4>Reply<span> &#151; No HTML Please.</span></h4>'+
							'<textarea name="reply"></textarea>'+
							'<div id="reply_err"></div><br/>'+
							'<input type="submit" value="Post Reply"/>'+
						'</form>'
			);
			
			// Spellcheck comments
			$('textarea[name=reply]').spellcheck();
			
			// Expanable textarea.
			$('textarea[name=reply]').growfield({'min': 150});
			
			// Activate the form.
			$('form[name=reply]').submit(function() {
				post_comment(si, 'reply', id);
				return false;
			});
			
			
			// Change 'reply' to 'cancel reply'.
			$('#'+id).html('Cancel Reply');
			
			// Activate cancel reply button.
			$('#'+id).click(function(){
				remove_form(si, this.id);
				return false;
			});		
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// For Voting on stories and comments.
	function vote(id){
		var arr = id.split('_');
		var votes = $('#'+arr[1]+'-pop-'+arr[2]).html();
		$('#'+arr[1]+'-pop-'+arr[2]).html('<img src="/img/load.gif" alt="Loading..."/>');
		$.post('/vote', {'id': id}, function(data) {
				// Callback.
				if(data.message == ''){
					// Set to default.
					$('#'+arr[1]+'_'+arr[2]).removeClass('demoted');
					$('#promote_'+arr[1]+'_'+arr[2]).removeClass('promoted');
					// Promote or demote.
					switch(data.value){
						case '1': $('#promote_'+arr[1]+'_'+arr[2]).addClass('promoted'); break;
						case '-1': $('#'+arr[1]+'_'+arr[2]).addClass('demoted'); break;
						default: break;
					}
					// Show new value.
					$('#'+arr[1]+'-pop-'+arr[2]).fadeOut('fast', function() {
						$('#'+arr[1]+'-pop-'+arr[2]).html(data.vote_count).fadeIn('fast');
					});
				} else {
					$('#'+arr[1]+'-pop-'+arr[2]).fadeOut('fast', function() {
						$('#'+arr[1]+'-pop-'+arr[2]).html(votes).fadeIn('fast');
					});
					alert(data.message);
				}
			}, 
			"json"
		);
	}
