<?php 
	$user_id = $this->session->userdata($this->config->item('session_key').'_usr');
	$user_is_admin = $this->util->user_is_admin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/contact.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/spellcheck/jquery.spellcheck.js"></script>
<script type="text/javascript" src="/js/jquery.growfield2.js"></script>
<script type="text/javascript">
$(function(){
	// Spell check.
	$('textarea[name=message]').spellcheck();
	
	// Expanable textarea.
	$('textarea[name=message]').growfield({'min': 200});
});
</script>
<title><?= $this->config->item('title') ?> - Contact Us</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
    <h2>Contact Us</h2>
    <p>You message was successfully sent.</p>
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