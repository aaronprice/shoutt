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
<title><?= $this->config->item('title') ?> - Settings - Email Preferences</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
	<?php $this->load->view('commons/profile_settings'); ?>
    <h3>Email Preferences</h3>
    <p>Notify me by email:</p>
    <form action="/settings/email" method="post">
        <ul id="prefs">
            <li>
                <label class="pref-name">When someone comments on my story...</label><label><input name="on_comment" type="radio" value="yes" <?= set_radio('on_comment', 'yes', ($settings['on_comment'] == '1')) ?>/> Subscribe</label> <label><input name="on_comment" type="radio" value="no" <?= set_radio('on_comment', 'no', ($settings['on_comment'] == '0')) ?>/> No Email</label><br/>
                <?= form_error('on_comment') ?>
            </li>
            <li>
                <label class="pref-name">When someone replies to my comment...</label><label><input name="on_reply" type="radio" value="yes" <?= set_radio('on_reply', 'yes', ($settings['on_reply'] == '1')) ?>/> Subscribe</label> <label><input name="on_reply" type="radio" value="no" <?= set_radio('on_reply', 'no', ($settings['on_reply'] == '0')) ?>/> No Email</label><br/>
                <?= form_error('on_reply') ?>
            </li>
            <li>
                <label class="pref-name">When shoutt has news...</label><label><input name="on_news" type="radio" value="yes" <?= set_radio('on_news', 'yes', ($settings['on_news'] == '1')) ?>/> Subscribe</label> <label><input name="on_news" type="radio" value="no" <?= set_radio('on_news', 'no', ($settings['on_news'] == '0')) ?>/> No Email</label><br/>
                <?= form_error('on_news') ?>
            </li>
        </ul>
        <input type="submit" value="Save Settings"/>
    </form>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>