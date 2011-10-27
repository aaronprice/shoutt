<?php if(isset($user_info['id'])) : ?>
	$('form[name=invite]').submit(function(){
		$('#inv_info').html('Loading...');
		$.post('/settings/invite', 
			{ 'email': $('input[name=email]').val() }, function (data){
				$('#inv_info').html(data.message);
				$('#invites_remaining').html(data.remaining);
				if(data.message == 'Sent.') $('input[name=email]').attr('value', '');
			}, 'json'
		);	
		return false;
	});
<?php endif; ?>