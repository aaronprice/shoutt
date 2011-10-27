<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/invitations.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(function() {
	<?php $this->load->view('commons/invite_js'); ?>

	$('.resend').click(function() {
		var id = this.id;
		$.post('/settings/resend_invite', {
			inv_id: id
		}, function (data){
			$('#'+id).unbind('click');
			$('#'+id).before(data).remove();
		});
	});
	
	$('.del_inv').click(function(){
		// Split the id.
		var id = this.id.split('_');
		
		// Confirm delete.
		if(!confirm('Are you sure you want to delete this invitation?')) 
			return false;
		// Do Delete.
		$.post('/settings/delete_invite', 
			{ 
				'id': id[2]
			}, function (data){
				if(data != ''){
					// Give feedback.
					alert(data);	
				} else {
					// Remove the story
					$('#invites_remaining').html(parseInt($('#invites_remaining').html()) + 1);
					$('#li_inv_'+id[2]).fadeOut("slow", function(){ $(this).remove(); });
				}
		});
	});
});
</script>
<title><?= $this->config->item('title') ?> - Settings - Invitations</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
	<?php $this->load->view('commons/profile_settings'); ?>
    <h3>Invitations</h3>
    <?php if(count((array) $invitations) > 0) : ?>
    <ul id="invitations">
    	<?php foreach($invitations as $inv) : ?>
        <?php if($inv['datesent'] > (time() - 60 * 60 * 24)) : ?>
        <li id="li_inv_<?= $inv['id'] ?>"><div class="inv_options">Sent <?= timespan($inv['datesent']) ?> | <a id="del_inv_<?= $inv['id'] ?>" class="del_inv">Delete</a></div><?= $inv['email'] ?></li>
        <?php else : ?>
        <li id="li_inv_<?= $inv['id'] ?>"><div class="inv_options"><a id="inv_<?= $inv['id'] ?>" class="resend">Resend Invitation</a> | <a id="del_inv_<?= $inv['id'] ?>" class="del_inv">Delete</a></div><?= $inv['email'] ?></li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php else : ?>
    <p>You have not invited anyone.</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>