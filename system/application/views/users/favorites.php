<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="submit news,post news,share news,trinidad news,trinidad tobago news,<?= $url_username ?>" />
<?php if($activity_stats['comment'] == '0' && $activity_stats['submission'] == '0' && $activity_stats['vote'] == '0' && $activity_stats['favorite'] == '0') : ?>
<meta name="description" content="Profile for <?= $url_username ?>. No recent activity." />
<?php else : ?>
<meta name="description" content="Profile for <?= $url_username ?>. <?= $activity_stats['submission'] ?> Post<?= ($activity_stats['submission'] == '1') ? '' : 's' ?>, <?= $activity_stats['comment'] ?> Comment<?= ($activity_stats['comment'] == '1') ? '' : 's' ?>, <?= $activity_stats['vote'] ?> Vote<?= ($activity_stats['vote'] == '1') ? '' : 's' ?>, <?= $activity_stats['favorite'] ?> Favorite<?= ($activity_stats['favorite'] == '1') ? '' : 's' ?>" />
<?php endif; ?>
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/favorites.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.timeago.js"></script>
<script type="text/javascript">
$(function(){

	// Enable Timestamps.
	$('abbr.timeago').timeago();

	<?php if($this->util->user_is_admin()) : ?>
	$('#res').click(function(){
	
		var message = '';
		var html = $('#res').html();
		if(html == 'Unban')
			message = "Are you sure you're ready to trust this user?";
		else
			message = "Are you sure you want to limit this user's access to the website?";
		
		if(!confirm(message))
			return false;
		
		$.post('/users/toggle_ban', 
				{ 
					'user_id': '<?= $user_id ?>'
				}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the story
						var html = $('#res').html();
						if(html == 'Unban')	
							$('#res').html("Ban");
						else
							$('#res').html("Unban");
					}
				});
	});
	<?php endif; ?>
});
</script>
<title><?= $this->config->item('title') ?> - <?= $url_username ?> - Favorites</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/profile_menu'); ?>
<?php $this->load->view('commons/messages.php'); ?>
<div id="main-content" class="clearfix">
    <div id="favorites">
        <h3>Favorites</h3>
        <?php if(count((array) $favorites) > 0) : ?>
        <ul class="clearfix">
            <?php foreach($favorites as $favorite) : ?>
            <?php 
                $category 	= ($favorite['subcat'] == 'all') ? $favorite['category'] : $favorite['subcat']; 
                $year 		= date('Y', $favorite['date_submitted']);
                $month 		= date('m', $favorite['date_submitted']);
                $day 		= date('d', $favorite['date_submitted']);
            ?>
            <li>
                <div class="vote-col"><?= empty($favorite['popularity']) ? '1' : $favorite['popularity'] ?><div><?= empty($favorite['popularity']) ? 'vote' : 'votes' ?></div></div>
                <?php
                $img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$favorite['headline'].'/'; 
                if(file_exists($img_path)) {
                    // Get file list.
                    $files = $this->util->list_files($img_path);
                    
					if(count((array) $files) > 0) {
						echo display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$history['headline'], image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$favorite['headline'].'/'.$files[0]['name'], 30, 30, '', true));
					}
                }
                ?>
                <div class="activity-info">
                    <div><?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$favorite['headline'], $favorite['headline_txt']) ?></div>
                    <div>Favorite <abbr class="timeago" title="<?= date($this->config->item('date_format'), $favorite['activity_time']) ?>"></abbr></div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <div id="pages"><?= $this->pagination->create_links() ?></div>
		<?php else : ?>
        <p>No Favorites.</p>
        <?php endif; ?>
    </div>
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
