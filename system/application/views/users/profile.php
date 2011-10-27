<?php $session_username = $this->session->userdata($this->config->item('session_key').'_unm'); ?>
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
<link type="text/css" rel="stylesheet" href="/css/profile.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.timeago.js"></script>
<script type="text/javascript">
$(function(){

	// Enable Timestamps.
	$('abbr.timeago').timeago();
	<?php if($show_ban) : ?>
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

<title><?= $this->config->item('title') ?> - <?= $url_username ?></title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php if( ($session_username == 'loraine20tt') && 
			($url_username == 'loraine20tt' || $url_username == 'aaronprice') ) : ?>
<div style="text-align:center">
<img src="/img/hearts.gif" alt="<3 <3 <3 hearts for you Lo *kisses* <3 <3 <3"/>
</div>
<?php endif; ?>
<?php $this->load->view('commons/profile_menu'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
	<div id="side-bar">
	   	<div class="side-box">
            <h4>Stats</h4>
            <ul id="activity_stats">
                <li><span><?= $activity_stats['submission'] ?></span>Posts</li>
                <li><span><?= $activity_stats['comment'] ?></span>Comments</li>
                <li><span><?= $activity_stats['vote'] ?></span>Votes</li>
            </ul>
        </div>
    </div>
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
						echo '<a href="/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$favorite['headline'].'">'.
								image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$favorite['headline'].'/'.$files[0]['name'], 30, 30, '', true).
							 '</a>';
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
        <div class="view-all"><a href="/users/<?= $url_username ?>/history/favorites">View all favorites</a></div>
		<?php else : ?>
        <p>No favorites.</p>
        <?php endif; ?>
    </div>
    <div id="history">
        <h3>History</h3>
        <?php if(count((array) $activity) > 0) : ?>
        <ul class="clearfix">
            <?php foreach($activity as $history) : ?>
            <?php 
                $category 	= ($history['subcat'] == 'all') ? $history['category'] : $history['subcat']; 
                $year 		= date('Y', $history['date_submitted']);
                $month 		= date('m', $history['date_submitted']);
                $day 		= date('d', $history['date_submitted']);
            ?>
            <li>
                <div class="vote-col"><?= empty($history['popularity']) ? '1' : $history['popularity'] ?><div><?= empty($history['popularity']) ? 'vote' : 'votes' ?></div></div>
                <?php
                $img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$history['headline'].'/'; 
                if(file_exists($img_path)) {
                    // Get file list.
                    $files = $this->util->list_files($img_path);
                    
					if(count((array) $files) > 0) {
						echo display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$history['headline'], image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$history['headline'].'/'.$files[0]['name'], 30, 30, '', true));
					}
                }
                ?>
                <div class="activity-info">
                    <div><?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$history['headline'], $history['headline_txt']) ?></div>
                    <?php switch($history['type']) :
                        case 'submission' : ?>
                    <div>Posted <abbr class="timeago" title="<?= date($this->config->item('date_format'), $history['activity_time']) ?>"></abbr></div>
                    <?php break; ?>
                    <?php case 'comment' : ?>
                    <div>Comment <abbr class="timeago" title="<?= date($this->config->item('date_format'), $history['activity_time']) ?>"></abbr>:</div>
                    <div class="preview-comment">
                        "<?= display_preview($history['comment'], 100) ?>" - 
                        (<?= empty($history['votes']) ? '1' : $history['votes'] ?> <?= empty($history['votes']) ? 'vote' : 'votes' ?><?php if($history['replies'] > 0) : ?>, <?= $history['replies'] ?> <?= ($history['replies'] == 1) ? 'reply' : 'replies' ?><?php endif; ?>)
                    </div>
                    <?php break; ?>
                    <?php case 'vote' : ?>
                    <div>Promoted <abbr class="timeago" title="<?= date($this->config->item('date_format'), $history['activity_time']) ?>"></abbr></div>
                    <?php break; ?>
                    <?php case 'favorite' : ?>
                    <div>Favorite <abbr class="timeago" title="<?= date($this->config->item('date_format'), $history['activity_time']) ?>"></abbr></div>
                    <?php break; ?>
                    <?php endswitch; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="view-all"><a href="/users/<?= $url_username ?>/history">View all history</a></div>
		<?php else : ?>
        <p>No activity.</p>
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
