<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/viewing.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(function(){
<?php $this->load->view('commons/invite_js'); ?>
});
</script>
<title><?= $this->config->item('title') ?> - Settings - Viewing Preferences</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
	<?php $this->load->view('commons/profile_settings'); ?>
    <h3>Viewing Preferences</h3>
    <form action="/settings/viewing" method="post">
        <ul id="prefs">
            <li>
                <label class="pref-name">Profanity Filter</label><label><input name="profanity" type="radio" value="on" <?= set_radio('profanity', 'on', ($settings['profanity'] == '0')) ?>/> on</label> <label><input name="profanity" type="radio" value="off" <?= set_radio('profanity', 'off', ($settings['profanity'] == '1')) ?>/> off</label><br/>
                <?= form_error('profanity') ?>
            </li>
            <li>
                <label class="pref-name">Open External Links in...</label><label><input name="openextlinks" type="radio" value="current" <?= set_radio('openextlinks', 'current', ($settings['openextlinks'] == '0')) ?>/> current window</label> <label><input name="openextlinks" type="radio" value="new" <?= set_radio('openextlinks', 'new', ($settings['openextlinks'] == '1')) ?>/> new window</label><br/>
                <?= form_error('openextlinks') ?>
            </li>
            <li>
                <label class="pref-name">Open Story Links in...</label><label><input name="openstorylinks" type="radio" value="current" <?= set_radio('openstorylinks', 'current', ($settings['openstorylinks'] == '0')) ?>/> current window</label> <label><input name="openstorylinks" type="radio" value="new" <?= set_radio('openstorylinks', 'new', ($settings['openstorylinks'] == '1')) ?>/> new window</label><br/>
                <?= form_error('openextlinks') ?>
            </li>
        </ul>
        <input type="submit" value="Save Settings"/>
    </form>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>