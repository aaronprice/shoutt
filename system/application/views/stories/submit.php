<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="submit news,post news,share news,trinidad news,trinidad tobago news" />
<meta name="description" content="Got news? Be heard! Write the news as you see it and read it as it happens. Pinpoint story locations in T&T and share images and video. Got something to say? Shoutt it!" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/story_submit.css"/>
<title><?= $this->config->item('title') ?> - Submit a story</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<div id="main-content">
<p>Use this area to submit stories from other websites to SHOUTT!</p>
<p>Currently, the only websites supported at the moment are <?= display_ext_link('http://guardian.co.tt', 'guardian.co.tt') ?>, <?= display_ext_link('http://newsday.co.tt', 'newsday.co.tt') ?> and <?= display_ext_link('http://www.trinidadexpress.com', 'trinidadexpress.com') ?>. If you would like to have your site added, please <a href="/contact">contact us</a> and let us know.</p>
<form action="/submit" method="post">
	<div id="url_holder">
        <div id="url">
            <label>URL<span>e.g.: http://www.example.com</span></label>
            <input name="url" value="<?= set_value('url') ?>"/>
            <?= form_error('url') ?>
        </div>
    </div>
    <div id="submit">
        <input type="submit" value="Proceed to Step 2"/>
    </div>
</form>
</div>
<?php $this->load->view('commons/footer'); ?>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.preloadimages.js"></script>
<script type="text/javascript">
$.preLoadImages("/img/load.gif");
$(function(){
	$('input[type=submit]').click(function() {
		$('#submit').append(' <img src="/img/load.gif" alt="Loading..."/>');
	});
});
</script>
</body>
</html>