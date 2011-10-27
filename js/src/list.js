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