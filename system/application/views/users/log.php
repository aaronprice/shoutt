<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/user_log.css"/>
<title><?= $this->config->item('title') ?> - <?= $url_username ?> - Admin</title>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#res').click(function(){
	
		var message = '';
		var html = $('#res').html();
		if(html == 'Unban')
			message = "Are you sure you're ready to trust this user?";
		else
			message = "Are you sure you want to limit this user's access to the website?";
		
		if(!confirm(message))
			return false;
		
		$.post('/users/toggle_ban', 
				{ 
					'user_id': '<?= $user_id ?>'
				}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the story
						var html = $('#res').html();
						if(html == 'Unban')	
							$('#res').html("Ban");
						else
							$('#res').html("Unban");
					}
				});
	});
});
</script>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/profile_menu'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
    <h3>User Log</h3>
    <form action="/users/<?= $url_username ?>/log" method="post">
    	<input name="user_id" type="hidden" value="<?= $user_id ?>"/>
    	<input name="report" class="report"/><input name="score" class="score"/>
        <input type="submit" value="Save"/>
        <?= form_error('user_id'); ?>
        <?= form_error('report'); ?>
        <?= form_error('score'); ?>
    </form>
    <?php if(count((array) $log) > 0) : ?>
    <ul>
		<?php foreach($log as $report) : ?>
        <li>
			<?= $report['report'] ?> ( <?= $report['score'] ?> )
            <div class="rep-time"><?= timespan($report['date_added']) ?></div>
        </li>
        <?php endforeach; ?>
    </ul>
    <div id="pages"><?= $this->pagination->create_links() ?></div>
    <?php else : ?>
    <p>There are no logs for this user.</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>