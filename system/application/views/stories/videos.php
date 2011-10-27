<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $this->config->item('title') ?> - <?= $title ?></title>
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/menu.css"/>
<link type="text/css" rel="stylesheet" href="/css/videos.css"/>
<link type="text/css" rel="stylesheet" href="/js/fancybox/jquery.fancybox-1.3.1.css" media="screen"/>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/menu'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content" class="clearfix">
	<div id="side-bar">
    	<div id="side-menu">
        	<ul>
            	<li><a href="<?= $url_info['news_type_link'] ?>">Readers Choice</a></li>
                <li><a href="<?= $url_info['news_type_link'] ?>/upcoming">More News</a></li>
            </ul>
        </div>
    	<?php $this->load->view('commons/invite_form'); ?>
		<div id="facebook">
			<iframe src="http://www.facebook.com/plugins/likebox.php?id=180889814159&width=200&height=287&header=true&connections=6&stream=false" scrolling="no" frameborder="0" style="border:none;overflow:hidden;width:200px;height:287px" allowTransparency="true"></iframe>
		</div>
		<div id="youtube">
			<h3>CNC3 News</h3>
			<div id="youtube_feed"></div>
		</div>
        <?php $this->load->view('commons/ad_side'); ?>
    </div>
	<div id="videos">
	<?php if ( count( (array) $videos ) > 0 ) : ?> 
	<ul>
		<?php foreach($videos as $key => $story) : ?>
		<?php
			$category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
			$year 		= date('Y', $story['datesubmitted']);
			$month 		= date('m', $story['datesubmitted']);
			$day 		= date('d', $story['datesubmitted']);
		?>
		<li>
			<h2><?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], $story['headline_txt']) ?></h2>
			<?php foreach( $story['videos'] as $video ) : ?>
			<?= display_video($video, '728', '437') ?>
			<?php endforeach; ?>
		</li>
		<?php endforeach; ?>
	</ul>
    <?php else : ?>
    <p>There are no videos just yet.</p>
    <?php endif; ?>
    </div>
</div>
<?php $this->load->view('commons/footer'); ?>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/jquery.youtube.channel/jquery.youtube.channel.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
$(function() {
	<?php $this->load->view('commons/invite_js'); ?>
	$('#youtube_feed').youTubeChannel({ 
		userName: 'cnc3television',
		numberToDisplay: 10,
		thumbWidth: 100
	});
});
$(window).load(function(){
	$("#youtube_feed a").each(function() {
		$(this).click(function() {
			$.fancybox({
					'padding'		: 0,
					'autoScale'		: false,
					'transitionIn'	: 'none',
					'transitionOut'	: 'none',
					'title'			: this.title,
					'width'			: 600,
					'height'		: 450,
					'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
					'type'			: 'swf',
					'swf'			: {
					    'wmode'				: 'transparent',
						'allowfullscreen'	: 'true'
					}
				});
		
			return false;
		});
	});
});
</script>
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
</body>
</html>