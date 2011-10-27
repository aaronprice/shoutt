<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="submit news,post news,share news,trinidad news,trinidad tobago news" />
<meta name="description" content="Got news? Be heard! Write the news as you see it and read it as it happens. Pinpoint story locations in T&T and share images and video. Got something to say? Shoutt it!" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/login.css"/>
<title><?= $this->config->item('title') ?> - Login</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
    <div id="login">
        <h3>Login</h3>
        <form action="/login" method="post">
            <label>Username</label>
            <input class="txt" name="username" value="<?= set_value('username') ?>" />
            <br/>
            <?= form_error('username') ?>
            <label>Password</label>
            <input class="txt" type="password" name="pwd" />
            <br/>
            <?= form_error('pwd') ?>
            <input type="submit" value="Login" />
        </form>
    </div>
    <div id="forgot">
        <h3>Forgot Password</h3>
        <form action="/recover" method="post">
            <label>Email</label>
            <input class="txt" name="email" value="<?= set_value('email') ?>"/>
            <input type="submit" value="Reset Password"/>
            <?= form_error('email') ?>
        </form>
    </div>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
<?php if($_SERVER['REMOTE_ADDR'] != '127.0.0.1') : ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-4391221-3");
pageTracker._trackPageview();
} catch(err) {}</script>
<?php endif; ?>
</html>