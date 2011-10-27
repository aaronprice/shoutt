<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/tech_details.css"/>
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
<div id="main-content">
    <h3>User Technical Details</h3>
    <table>
        <tr>
            <th>IP Address</th>
            <th>Browser</th>
            <th>Operating System</th>
        </tr>
        <?php foreach($tech_report as $tr) : ?>
        <?php 
            $browser = implode(' ', $this->util->get_browser_from_user_agent($tr['user_agent'])); 
            $operating_system = $this->util->get_os_from_user_agent($tr['user_agent']);
        ?>
        <tr>
            <td><?= $tr['ip'] ?></td>
            <td><?= $browser ?></td>
            <td><?= $operating_system ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>