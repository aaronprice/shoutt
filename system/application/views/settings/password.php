<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/password.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(function(){
<?php $this->load->view('commons/invite_js'); ?>
});
</script>
<title><?= $this->config->item('title') ?> - Settings - Change Password</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<div id="main-content">
	<?php $this->load->view('commons/profile_settings'); ?>
    <h3>Change Password</h3>
    <form action="/settings/password" method="post">
        <label>Old Password</label><input class="txt" name="old" type="password" value="<?= set_value('old') ?>"/><br/>
        <?= form_error('old') ?>
        <label>New Password</label><input class="txt" name="new" type="password" value="<?= set_value('new') ?>"/><br/>
        <?= form_error('new') ?>
        <label>Confirm Password</label><input class="txt" name="confirm" type="password" value="<?= set_value('confirm') ?>"/><br/>
        <?= form_error('confirm') ?>
        <input type="submit" value="Save Password"/>
    </form>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>
