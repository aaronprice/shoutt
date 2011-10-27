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
