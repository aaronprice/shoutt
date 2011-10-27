<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/admin.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#delete_thumbnails').click(function(){
		// Confirm delete.
		if(!confirm('Are you sure you want to delete all the thumbnails stored on the server?')) 
			return false;
		// Do Delete.
		$.post('/admin/delete_thumbnails', {}, function (data){ alert(data); });
	});
	$('#replenish_invites').click(function(){
		$.post('/admin/replenish_invites', {}, function (data){ alert(data); });
	});
});
</script>
<title><?= $this->config->item('title') ?> - Admin</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/admin_menu'); ?>
<div id="main-content">
	<h3>General Maintenance</h3>
    <p>Here are a couple things you could do as general maintenance of the site:</p>
	<ul>
    	<li><a id="delete_thumbnails">Delete thumbnails</a> to save space - Thumbnails are generated on the fly so no need to keep them on the server.</li>
        <li><a id="replenish_invites">Replenish Invites</a> for users - Give everyone <?= ($this->config->item('num_invites_per_person') == '1') ? '1 invite' : $this->config->item('num_invites_per_person').' invites' ?>.</li>
    </ul>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>
