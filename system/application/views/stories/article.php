<?php $other_images = ''; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="<?= get_keywords($story['what']) ?>" />
<meta name="description" content="<?= str_replace(array('"', "\n"), '', display_preview($story['what'])) ?>" />
<title><?= $this->config->item('title') ?> - <?= display_headline($story['headline_txt']) ?></title>
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/article.css"/>
<link type="text/css" rel="stylesheet" href="/js/fancybox/jquery.fancybox-1.3.1.css" media="screen"/>
<link type="text/css" rel="stylesheet" href="/css/menu.css"/>
<!--[if IE 7]>
<style type="text/css">
.share-buttons { display: block; margin: -20px 0 0; }
.user-options {	width: 950px; }
</style>
<![endif]-->
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/menu'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
	<?php 
		// Determine Vote info.
		$vote_class = '';
		$story_class = '';
		// If the current user posted the story. Show the vote as positive.
		if($user_info['user_is_logged_in']) {
			if($user_info['id'] == $story['user_id']) {
				$user_vote = '1';
			}
		}
		
		switch($user_vote) {
			case '1': $vote_class = ' promoted'; break;
			case '-1': $story_class = ' class="demoted"'; break;
			default: break;
		}
	?>
	<div id="story_<?= $story['id'] ?>"<?= $story_class ?>>
    <?php if($user_info['user_is_admin'] === true || $story['user_id'] == $user_info['id']) : ?>
    <a href="/edit/<?= $story['headline'] ?>" style="float:right">Edit</a>
    <?php endif; ?>
        <div class="vote-col">
            <a id="promote_story_<?= $story['id'] ?>" class="vote<?= $vote_class ?>" href="#">+</a>
            <div id="story-pop-<?= $story['id'] ?>"><?= empty($story['popularity']) ? '1' : $story['popularity'] ?></div>
            <a id="demote_story_<?= $story['id'] ?>" class="vote" href="#">-</a>
        </div>
        <h1>
            <?= display_story_link((empty($story['url']) ? '/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'] : $story['url']), $story['headline_txt']) ?>
            <?php if(!empty($story['url'])) : ?>
            <?= display_ext_link($story['url'], '<img src="/img/el.gif" alt="External Link"/>') ?>
            <?php endif; ?>
        </h1>
        <div class="share">
            <?= empty($story['url']) ? 'Posted' : 'Submitted' ?> <abbr class="timeago" title="<?= date($this->config->item('date_format'), $story['datesubmitted']) ?>"></abbr> by <a href="/users/<?= $story['username'] ?>"><?= $story['username'] ?></a>
            in <a href="/<?= $category.(($story['view'] == '0') ? '/upcoming' : '') ?>"><?= $this->lang->line($category) ?></a>
        </div>
		<div class="goto">
			<?php if(!empty($story['posx']) && !empty($story['posy'])) : ?>
			<span class="quick_links">Location: <a href="#map-container"><?= $story['where'] ?> (Map)</a></span>
			<?php endif; ?>
			<span class="quick_links"><a href="#num_comments"><?= ($story['num_comments'] == 1) ? '<span>1</span> Comment' : '<span>'.$story['num_comments'].'</span> Comments' ?></a></span>
		</div>
        <div class="user-options">&nbsp;
		<?php if($_SERVER['REMOTE_ADDR'] != '127.0.0.1') : ?>
        <ul class="share-buttons">
			<?php if ( false ) : ?>
            <li class="digg"><a class="DiggThisButton">('<img src="http://digg.com/img/diggThisCompact.png" height="18" width="120" alt="DiggThis" />’)</a><script src="http://digg.com/tools/diggthis.js" type="text/javascript"></script></li>
            <li class="reddit"><script type="text/javascript" src="http://www.reddit.com/buttonlite.js?i=5"></script></li>
            <li class="stumbleupon"><script src="http://www.stumbleupon.com/hostedbadge.php?s=2"></script></li>
            <li class="google">
				<script type="text/javascript">
				var njuice_buzz_size = 'small';
				var njuice_buzz_share = 'reader';
				</script>
				<script type="text/javascript" src="http://button.njuice.com/buzz.js"></script>
            </li>
			<?php endif; ?>
            <li class="facebook"><a class="share_button" name="fb_share" type="button_count" share_url="http://<?= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] ?>"></a></li>
            <li class="twitter">
                <script type="text/javascript">
                tweetmeme_style = 'compact';
                tweetmeme_service = 'bit.ly';
                </script>
                <script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
            </li>
        </ul>
		<?php endif; ?>
        <span id="part_holder"><a id="participants_<?= $story['id'] ?>" href="#">Participants</a> <?php if($user_info['user_is_logged_in'] === true) : ?> | <?php endif; ?></span>
        <?php if($user_info['user_is_logged_in'] === true) : ?>
                <?php if($this->story->is_favorite($story['id'])) : ?>
                <a>Added to Favorites</a> |
                <?php else : ?>
                <a id="fav_<?= $story['id'] ?>" class="fav">Add to Favorites</a> | 
                <?php endif; ?>
                <?php if($this->story->is_reported($story['id'])) : ?>
                <a>Reported</a>
                <?php else : ?>
                <a id="report_story_<?= $story['id'] ?>" class="rep">Report Abuse</a>
                <?php endif; ?>
                <?php if($user_info['user_is_admin'] == true) : ?>
                 - <strong><?= (false) ? intval($this->story->assess($story['id'], false)) : intval($this->story->get_score($story['id'])) ?>%</strong>
                <?php endif; ?>
                <?php if(false && ($user_info['user_is_admin'] === true || $story['user_id'] == $user_info['id'])) : ?>
                | <a href="/compose/<?= $story['headline'] ?>">Follow up</a> 
                <?php if(false) : ?>
                | <a href="/submit/<?= $story['headline'] ?>">Submit Follow up</a>
                <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div id="part_container"></div>
    <?php 
    // Display images that don't appear in the story.	
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/')) {
        $files = $this->util->list_files($_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/');
        
        echo '<div id="images">';
        
        foreach($files as $img) {
			// If image is found in the story.
			if(preg_match('#<image '.$img['name'].'>?#i', $story['what'])) {
				// Generate a thumbnail for it (size 200x200).
				image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$img['name'], 956, 956);
			} else {
				// Display the images.
				$other_images .= '<div>'.
									'<a class="group" rel="group" href="/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$img['name'].'">'.
										image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$img['name'], 100, 100, '', true).
								 	'</a>'.
								 '</div>';
			}
        }
        
        echo '</div>';
    }
    ?>
    <div id="side-bar">
    	<?php $this->load->view('commons/ad_side'); ?>
    </div>
    <div id="article" class="clearfix">
    <?= empty($story['url']) ? display_story($story['what'], '/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline']) : display_comment($story['what']) ?>
    </div>
    <?php if(!empty($other_images)) : ?>
    <div id="other-images" class="clearfix">
    	<h4>Images</h4>
        <?= $other_images ?>
    </div>
    <?php endif; ?>
    <?php if(!empty($story['posx']) && !empty($story['posy'])) : ?>
    <div id="map-container">
    	<h4>Location</h4>
    	<div id="map"></div>
    </div>
    <?php endif; ?>
    <h3><a id="num_comments"><?= ($story['num_comments'] == 1) ? '<span>1</span> Comment' : '<span>'.$story['num_comments'].'</span> Comments' ?></a></h3>
    <div id="side-bar">
    	<?php $this->load->view('commons/ad_side'); ?>
    </div>
    <ul id="comments">
    	<?php if(count((array) $comments) > 0 ) : ?>
        <?php foreach($comments as $comment) : ?>
        <?php 
			// Determine Vote info.
			$vote_class = '';
			$comment_class = '';
			// If the current user posted the story. Show the vote as positive.
			if($user_info['user_is_logged_in']) {
				if($user_info['id'] == $comment['user_id']) {
					$comment_votes[$comment['id']] = '1';
				}
			}
			
			if(isset($comment_votes[$comment['id']])) {
				switch($comment_votes[$comment['id']]) {
					case '1': $vote_class = ' promoted'; break;
					case '-1': $comment_class = ' class="demoted"'; break;
					default: break;
				}
			}
		?>
        <li id="comment_<?= $comment['id'] ?>"<?= $comment_class ?>>
        	<div class="comment_container<?= ($user_info['id'] == $comment['user_id']) ? ' author' : '' ?>">
                <div class="com-vote">
                    <div id="comment-pop-<?= $comment['id'] ?>"><?= isset($comment['popularity']) ? $comment['popularity'] : '1' ?></div>
                    <a id="promote_comment_<?= $comment['id'] ?>" class="vote<?= $vote_class ?>">+</a> <a id="demote_comment_<?= $comment['id'] ?>" class="vote">-</a>
                </div>
                <div><a href="/users/<?= $comment['username'] ?>"><?= $comment['username'] ?></a> says:</div>
                <div class="com-time"><abbr class="timeago" title="<?= date($this->config->item('date_format'), $comment['dateposted']) ?>"></abbr></div>
                <div>
                    <div id="comment_detail_<?= $comment['id'] ?>"><?= display_comment($comment['comment']) ?></div>
                    <?php if($user_info['user_is_logged_in'] === true) : ?>
                    <div class="comment_options">
                        <?php if($user_info['user_is_admin'] === true || $comment['user_id'] == $user_info['id']) : ?>
                        <a id="edit_comment_<?= $comment['id'] ?>" class="edit" href="#">Edit</a>
                        <?php else : ?>
                            <?php if($this->story->is_reported($story['id'], $comment['id'])) : ?>
                            <a>Reported</a>
                            <?php else : ?>
                            <a id="report_comment_<?= $comment['id'] ?>" class="rep" href="#">Report abuse</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        | <a id="r_<?= $comment['id'] ?>" class="reply" href="#">Reply</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($this->story->comment_has_replies($comment['id'])) : ?>
            <ul id="replies_to_<?= $comment['id'] ?>">
                <?php $replies = $this->story->get_comments_where(array('reply_to' => $comment['id'])); ?>
                <?php foreach($replies as $reply) : ?>
                <?php 
					// Determine Vote info.
					$vote_class = '';
					$reply_class = '';
					// If the current user posted the story. Show the vote as positive.
					if($user_info['user_is_logged_in']) {
						if($user_info['id'] == $reply['user_id']) {
							$comment_votes[$reply['id']] = '1';
						}
					}
					
					if(isset($comment_votes[$reply['id']])) {
						switch($comment_votes[$reply['id']]) {
							case '1': $vote_class = ' promoted'; break;
							case '-1': $reply_class = ' class="demoted"'; break;
							default: break;
						}
					}
				?>
                <li id="comment_<?= $reply['id'] ?>"<?= $reply_class ?>>
                	<div class="comment_container<?= ($user_info['id'] == $reply['user_id']) ? ' author' : '' ?>">
                        <div class="com-vote">
                            <div id="comment-pop-<?= $reply['id'] ?>"><?= empty($reply['popularity']) ? '1' : $reply['popularity'] ?></div>
                            <a id="promote_comment_<?= $reply['id'] ?>" class="vote<?= $vote_class ?>" href="#">+</a> <a id="demote_comment_<?= $reply['id'] ?>" class="vote" href="#">-</a>
                        </div>
                        <div><a href="/users/<?= $reply['username'] ?>"><?= $reply['username'] ?></a> says:</div>
                        <div class="com-time"><abbr class="timeago" title="<?= date($this->config->item('date_format'), $reply['dateposted']) ?>"></abbr></div>
                        <div>
                            <div id="comment_detail_<?= $reply['id'] ?>"><?= display_comment($reply['comment']) ?></div>
                            <?php if($user_info['user_is_logged_in'] === true) : ?>
                            <div class="comment_options">
                                <?php if($user_info['user_is_admin'] === true || $reply['user_id'] == $user_info['id']) : ?>
                                <a id="edit_comment_<?= $reply['id'] ?>" class="edit" href="#">Edit</a>
                                <?php else : ?>
                                    <?php if($this->story->is_reported($story['id'], $reply['id'])) : ?>
                                    <a href="#">Reported</a>
                                    <?php else : ?>
                                    <a id="report_comment_<?= $reply['id'] ?>" class="rep" href="#">Report abuse</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
        <?php else : ?>
        <li id="no_comment">Be the first to comment on this story.</li>
        <?php endif; ?>
    </ul>
    <div><?= $this->pagination->create_links() ?></div>
    
    <?php if($user_info['user_is_logged_in'] === true) : ?>
    <h4>Add a comment<span> &#151; No HTML Please.</span></h4>
    <form action="#" method="post" name="comment">
        <textarea name="comment"></textarea>
        <div id="comment_err"></div>
        <br/>
        <input type="submit" value="Post Comment"/>
    </form>
    <?php else : ?>
    <h4>Would you like to comment?</h4>
    <p><a href="/signup">Sign up</a> or <a href="/login">Login</a> to comment.</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.timeago.js"></script>
<script type="text/javascript" src="/js/jquery.jmap.min-r72.js"></script>
<?php if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') : ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAdZQpG59yfHDnXCN5ofDeeRQrIVa_1su-_1cBa94uuJaHRmyXcRROrQuUm0Ai1KYpAA0TP4UnxvcPGw" type="text/javascript"></script>
<?php else : ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAdZQpG59yfHDnXCN5ofDeeRRyG9Jul30yA02Caxkzud86IZLvdhTK-l-jN_fe0bgCxAz0NjgnSilJSA" type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="/js/jquery.growfield2.js"></script>
<script type="text/javascript" src="/js/spellcheck/jquery.spellcheck.js"></script>
<script type="text/javascript" src="/js/article<?php if($user_info['user_is_logged_in'] === true) : ?>_user<?php endif; ?>.js"></script>
<script type="text/javascript">
$(function(){
	$('abbr.timeago').timeago();
	$('a.group').fancybox(); 
	$('textarea[name=comment]').spellcheck();
	$('textarea[name=comment]').growfield({'min': 150});
	$('.vote').click(function(){ vote(this.id); return false;});
	$('#participants_<?= $story['id'] ?>').click(function(){ get_participants(this.id); return false; });
	
	<?php if($user_info['user_is_logged_in'] === true) : ?>
	$('.fav').click(function(){ fav(this.id); return false; });
	$('.rep').click(function(){ report_abuse(this.id); return false; });
	$('form[name=comment]').submit(function() { post_comment('<?= $story['id'] ?>', 'comment', ''); return false; });
	$('.reply').click(function() { show_hide_reply_form('<?= $story['id'] ?>', this.id); return false; });
	$('.edit').click(function(){ show_hide_edit_form(this.id); return false; });
	<?php endif; ?>
	
	$('.fb_share_count_nub_right').css({'vertical-align': 'middle', 'background-position': 'right 2px'});
	<?php if(!empty($story['posx']) && !empty($story['posy'])) : ?>
	// Instanciate Google Map.
	$('#map').jmap('init', {
		'mapType': G_PHYSICAL_MAP,
		'mapControl': 'large', 
		'mapEnableType': true,
		'mapCenter':[<?= $story['posx'] ?>, <?= $story['posy'] ?>], 
		'mapShowjMapsIcon': false,
		'mapZoom': 12
	});
	$('#map').jmap('AddMarker',{
			'pointHTML': '<?= addslashes($story['where']) ?>, Trinidad &amp; Tobago',
			'pointLatLng': [<?= $story['posx'] ?>, <?= $story['posy'] ?>],
			'pointMaxZoom': 14
	});
	<?php endif; ?>

	$('.map').each(function( index ) {
		var el = $(this);
		el.jmap('init', {
			'mapType': G_PHYSICAL_MAP,
			'mapControl': 'large', 
			'mapEnableType': true,
			'mapCenter':[el.attr('latitude'), el.attr('longitude')], 
			'mapShowjMapsIcon': false,
			'mapZoom': ( el.attr('zoom') > 11 ) ? 11 : el.attr('zoom')
		});
		el.jmap('AddMarker', {
			'pointHTML': el.attr('q').replace(/\+/g, ' ')+', Trinidad &amp; Tobago',
			'pointLatLng': [el.attr('latitude'), el.attr('longitude')],
			'pointMaxZoom': ( el.attr('zoom') > 11 ) ? 11 : el.attr('zoom')
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
<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
</body>
</html>