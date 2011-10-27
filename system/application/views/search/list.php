<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="news archives trinidad,shoutt,trinidad news,trinidad and tobago news,breaking news trinidad,crime trinidad,politics trinidad,business trinidad,lifestyle trinidad,environment trinidad,sports trinidad,entertainment trinidad"/>
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/list.css"/>
<title><?= $this->config->item('title') ?> - <?= empty($q) ? 'Search' : 'Search for "'.htmlspecialchars($q).'"' ?></title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<div id="main-content">
    <div id="side-bar">
    <?php $this->load->view('commons/invite_form'); ?>
    </div>
    <?php if(count((array) $stories) > 0) : ?>
    <ul id="river" class="clearfix">
        <?php foreach($stories as $story) : ?>
        <?php 
            $category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
            $year 		= date('Y', $story['datesubmitted']);
            $month 		= date('m', $story['datesubmitted']);
            $day 		= date('d', $story['datesubmitted']);
			
			// Determine Vote info.
			$vote_class = '';
			$story_class = ($user_info['id'] == $story['user_id']) ? ' class="author"' : '';
			// If the current user posted the story. Show the vote as positive.
			if($user_info['user_is_logged_in']) {
				if($user_info['id'] == $story['user_id']) {
					$votes[$story['id']] = '1';
				}
			}
			
			if(isset($votes[$story['id']])) {
				switch($votes[$story['id']]) {
					case '1': $vote_class = ' promoted'; break;
					case '-1': $story_class = ' class="demoted"'; break;
					default: break;
				}
			}
        ?>
        <li id="story_<?= $story['id'] ?>"<?= $story_class ?>>
        	<?php if(($user_info['user_is_admin'] === true || $story['user_id'] == $user_info['id'])) : ?>
            <a href="/edit/<?= $story['headline'] ?>" class="edit">Edit</a>
            <?php endif; ?>
            <div class="vote-col">
                <a id="promote_story_<?= $story['id'] ?>" class="vote<?= $vote_class ?>">+</a>
                <div id="story-pop-<?= $story['id'] ?>"><?= empty($story['popularity']) ? '1' : $story['popularity'] ?></div>
                <a id="demote_story_<?= $story['id'] ?>" class="vote">-</a>
            </div>
            <?php
            $img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'; 
            if(file_exists($img_path)) {
                // Get file list.
                $files = $this->util->list_files($img_path);
                
                if(count((array) $files) > 0) {
                	echo display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$files[0]['name'], 70, 70, '', true));
				}
			}
            ?>
            <div class="story-info">
                <h2>
                	<?php if(!empty($story['url'])) : ?>
                    <?= display_ext_link($story['url'], '<img src="/img/el.gif" alt="External Link"/>') ?>
                    <?php endif; ?>
                    <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], $story['headline_txt']) ?>
                </h2>
                <div class="story-summary">
                    <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], (($story['where'] == '') ? '' : '<span class="story-location">'.$story['where'].' &#151; </span>').display_preview($story['what']), false) ?>
                </div>
                <div class="story-details">
                    <?= empty($story['url']) ? 'Posted' : 'Submitted'?> <abbr class="timeago" title="<?= date($this->config->item('date_format'), $story['datesubmitted']) ?>"></abbr> 
					<?php if(empty($story['url'])) : ?> by <a href="/users/<?= $story['username'] ?>"><?= $story['username'] ?></a> <?php else : ?> via <?= display_ext_link($story['url'], get_domain($story['url'])) ?> <?php endif; ?>
                    in <a href="/<?= $category ?>"><?= $this->lang->line($category) ?></a> - 
                    <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'#comments', ($story['num_comments'] == 1) ? '1 comment' : $story['num_comments'].' comments') ?>
                    <?php if(empty($story['url']) && $user_info['user_is_admin'] == true) : ?>
					 - <strong><?= (false) ? intval($this->story->assess($story['id'], false)) : intval($this->story->get_score($story['id'])) ?>%</strong>
					<?php endif; ?>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
        <li><div id="pages"><?= $this->pagination->create_links() ?></div></li>
    </ul>
    <?php else : ?>
    <p>Even I'm stumped. Try <a href="http://www.google.com" rel="nofollow">Google</a>.</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.timeago.js"></script>
<script type="text/javascript" src="/js/list.js"></script>
<script type="text/javascript">
$(function() {
	<?php $this->load->view('commons/invite_js'); ?>
	$('abbr.timeago').timeago();
	$('.vote').click(function() { vote(this.id); return false; });
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
