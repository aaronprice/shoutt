<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $this->config->item('title') ?> - <?= $title ?></title>
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/menu.css"/>
<link type="text/css" rel="stylesheet" href="/css/perspective.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<?php if ( count( (array) $stories ) > 0 ) : ?> 
<link type="text/css" rel="stylesheet" href="/css/timemap.css"/>
<?php if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') : ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAdZQpG59yfHDnXCN5ofDeeRQrIVa_1su-_1cBa94uuJaHRmyXcRROrQuUm0Ai1KYpAA0TP4UnxvcPGw" type="text/javascript"></script>
<?php else : ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAdZQpG59yfHDnXCN5ofDeeRRyG9Jul30yA02Caxkzud86IZLvdhTK-l-jN_fe0bgCxAz0NjgnSilJSA" type="text/javascript"></script>
<?php endif; ?>
<?php if(false) : ?><script type="text/javascript" src="http://static.simile.mit.edu/timeline/api/timeline-api.js"></script><?php endif; ?>
<script type="text/javascript" src="/js/timemap.1.5/lib/timeline-api.js"></script>
<script type="text/javascript" src="/js/timemap.1.5/timemap.pack.js"></script>
<script type="text/javascript">
var tm;
$(function(){
	tm = TimeMap.init({
		mapId: "map",               // Id of map div element (required)
		timelineId: "timeline",     // Id of timeline div element (required)
		options: {
			eventIconPath: "/js/timemap.1.5/images/"
		},
		datasets: [
			{
				id: "news",
				title: "News",
				theme: "red",
				// note that the lines below are now the preferred syntax
				type: "basic",
				options: {
					items: [
						<?php foreach($stories as $key => $story) : ?>
						<?php 
							$category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
							$year 		= date('Y', $story['datesubmitted']);
							$month 		= date('m', $story['datesubmitted']);
							$day 		= date('d', $story['datesubmitted']);
						?>
						{ "start" : "<?= date('Y-m-d', $story['datesubmitted']) ?>",
						  <?php if(!empty($story['posx']) && !empty($story['posy'])) : ?>
						  "point" : { "lat" : "<?= $story['posx'] ?>", "lon" : "<?= $story['posy'] ?>" },
						  <?php endif; ?>
						  "title" : "<?= addslashes(display_headline($story['headline_txt'])) ?>",
						  "options" : {	"infoHtml": '<h3><?= addslashes(display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], $story['headline_txt'])) ?></h3><p><?php
								$img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'; 
								if(file_exists($img_path)) {
									// Get file list.
									$files = $this->util->list_files($img_path);
									
									if(count((array) $files) > 0) {
										echo '<div class="story-thumb">'.display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$files[0]['name'], 70, 70, '', true)).'</div>';
									}
								}
								?><div class="story-details"><?php if(!empty($story['where'])) : ?><span class="story-location"><?= addslashes($story['where']) ?> &#151; </span><?php endif; ?><?= display_js_preview($story['what']) ?></div></p><p class="story-summary"><?= empty($story['url']) ? 'Posted' : 'Submitted' ?> <?= timespan($story['datesubmitted']) ?><?php if(empty($story['url'])) : ?> by <a href="/users/<?= $story['username'] ?>"><?= $story['username'] ?></a> <?php else : ?> via <?= display_ext_link($story['url'], get_domain($story['url'])) ?> <?php endif; ?> in <a href="/<?= $category.$url_info['upcoming'] ?>"><?= $this->lang->line($category) ?></a> - <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'#comments', ($story['num_comments'] == 1) ? '1 comment' : $story['num_comments'].' comments') ?></p>' }  
						} <?php if($key != (count((array) $stories) - 1)) : ?>,<?php endif; ?>
						<?php endforeach; ?>
					]
				}
			}<?php if(false) : ?>,
			{
				id: "chains",
				title: "Chains",
				theme: "purple",
				// note that the lines below are now the preferred syntax
				type: "basic",
				options: {
					items: [
						<?php foreach($chains as $key => $chain) : ?>
						<?php 
							$title = ''.$chain['num_stories'].' Stories: "'.$chain['start_headline_txt'].'" - "'.$chain['end_headline_txt'].'"'; 
							$stories = $this->story->get_stories_in_chain($chain['chain_id']);
						?>
						{ "start" : "<?= date('Y-m-d', $chain['start_date']) ?>",
						  "end"   : "<?= date('Y-m-d', $chain['end_date']) ?>",
						  <?php if(!empty($chain['posx']) && !empty($chain['posy'])) : ?>
						  "point" : { "lat" : "<?= $chain['posx'] ?>", "lon" : "<?= $chain['posy'] ?>" },
						  <?php endif; ?>
						  "title" : '<?= str_replace('&#8230;', '...', character_limiter($title, 25)); ?>',
						  "options" : {	"infoHtml": '<h3><?= $chain['num_stories'] ?> Stories</h3><?php 
						  		// Display story list.
						  		foreach($stories as $i => $story) {
									if($chain['num_stories'] > 2 && $i > 0 && $i < ($chain['num_stories'] - 2)) {
										echo '<div class="spacer">...</div>';
									} else {
										// Declare consistent vars.
										$category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
										$year 		= date('Y', $story['datesubmitted']);
										$month 		= date('m', $story['datesubmitted']);
										$day 		= date('d', $story['datesubmitted']);
										
										echo '<div class="sub-story">';
										$img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'; 
										if(file_exists($img_path)) {
											// Get file list.
											$files = $this->util->list_files($img_path);
											if(count((array) $files) > 0) {
												echo '<div class="chain-thumb">'.display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$files[0]['name'], 30, 30, '', true)).'</div>';
											}
										}
										echo addslashes(display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], $story['headline_txt']));
										echo '<div class="story-summary">'.(empty($story['url']) ? 'Posted' : 'Submitted').': '.timespan($story['datesubmitted']).' by <a href="/users/'.$story['username'].'">'.$story['username'].'</a> in <a href="/'.$category.$upcoming.'">'.$this->lang->line($category).'</a> - '.display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'#comments', ($story['num_comments'] == 1) ? '1 comment' : $story['num_comments'].' comments').'</div>';
										echo '</div>';
									}
								}
								?>' }  
						} <?php if($key != (count((array) $chains) - 1)) : ?>,<?php endif; ?>
						<?php endforeach; ?>
					]
				}
			}<?php endif; ?>
		],
		bandIntervals: [
			Timeline.DateTime.WEEK, 
			Timeline.DateTime.MONTH
		], 
		scrollTo: "latest"
	});
});
</script>
<?php endif; ?>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/menu'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
	<?php if ( count( (array) $stories ) > 0 ) : ?> 
    <div id="timemap">
        <div id="timelinecontainer">
            <div id="timeline"></div>
        </div>
        <div id="mapcontainer">
            <div id="map"></div>
        </div>
    </div>
    <?php else : ?>
    <p>There are no stories to display.</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
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