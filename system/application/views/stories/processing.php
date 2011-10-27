<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="submit news,post news,share news,trinidad news,trinidad tobago news" />
<meta name="description" content="Got news? Be heard! Write the news as you see it and read it as it happens. Pinpoint story locations in T&T and share images and video. Got something to say? Shoutt it!" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<style type="text/css">
#main-content div {
	position: relative;
	top: 150px;
	left: 377px;
	text-align: center;
	width: 206px;
	line-height: 60px;
}
</style>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<title><?= $this->config->item('title') ?> - Processing...</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<div id="main-content">
	<div>
    	<h2>Processing...</h2>
    	<img src="/img/big_load.gif" alt="Processing..."/>
    </div>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>