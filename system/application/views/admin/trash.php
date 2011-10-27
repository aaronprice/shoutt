<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/admin.css"/>
<link type="text/css" rel="stylesheet" href="/css/abuse.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(function(){
	$('.undelete').click(function(){
		// Split the id.
		var id = this.id.split('_');
		var id_str = this.id;
		
		// Confirm delete.
		if(!confirm('Are you sure you want to make this '+id[1]+' public again?')) 
			return false;
		// Do Delete.
		$.post('/stories/undelete', 
				{ 
					'token': '<?= $user_id ?>',
					'id': id_str
				}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the story
						$('#abli_'+id[2]).fadeOut("slow", function(){ $(this).remove(); });
					}
				});
	});
	
	$('.ban').click(function(){
	
		var message = '';
		var id = this.id.substr(4);
		var html = $(this).html();
		if(html == 'Unban Submitter')
			message = "Are you sure you're ready to trust this user?";
		else
			message = "Are you sure you want to ban this user?";
		
		if(!confirm(message))
			return false;
		
		$.post('/users/toggle_ban', 
				{ 
					'user_id': id
				}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the story
						var html = $('#ban_'+id).html();
						if(html == 'Unban Submitter')	
							$('#ban_'+id).html("Ban Submitter");
						else
							$('#ban_'+id).html("Unban Submitter");
					}
				});
	});

});
</script>
<title><?= $this->config->item('title') ?> - Admin - Trash</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/admin_menu'); ?>
<div id="main-content">
	<?php if(count((array) $stories) > 0) : ?>
    <ul>
		<?php foreach($stories as $story) : ?>
        <?php 
            $category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
            $year 		= date('Y', $story['datesubmitted']);
            $month 		= date('m', $story['datesubmitted']);
            $day 		= date('d', $story['datesubmitted']);
        ?>
        <li id="abli_<?= $story['id'] ?>">
            <?php
            $img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'; 
            if(file_exists($img_path)) {
                // Get file list.
                $files = $this->util->list_files($img_path);
                
                echo display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$files[0]['name'], 70, 70, '', true));
            }
            ?>
            <h2>
                <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'], $story['headline_txt']) ?>
                <?php if(!empty($story['url'])) : ?>
                <?= display_ext_link($story['url'], '<img src="/img/el.gif" alt="External Link"/>') ?>
                <?php endif; ?>
            </h2>
            <div class="story-summary">
                <?php if(!empty($story['where'])) : ?><span class="story-location"><?= $story['where'] ?> &#151; </span><?php endif; ?>
                <?= display_preview($story['what']) ?>
            </div>
            <div class="story-details">
                <?= empty($story['url']) ? 'Posted' : 'Submitted' ?>: <?= timespan($story['datesubmitted']) ?> by <a href="/users/<?= $story['submitter_username'] ?>"><?= $story['submitter_username'] ?></a>
                in <a href="/<?= $category ?>"><?= $this->lang->line($category) ?></a> - 
                <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'#comments', ($story['num_comments'] == 1) ? '1 comment' : $story['num_comments'].' comments') ?>
                <?php if(empty($story['comment_id'])) : ?>
                - Trashed by: <a href="/users/<?= $story['reporter_username'] ?>"><?= $story['reporter_username'] ?></a>
                <?php endif; ?>
            </div>
            <?php if(empty($story['comment_id'])) : ?>
            <div class="admin-options">
                <a id="und_story_<?= $story['id'] ?>" class="undelete">Undelete</a> |
                <a href="/edit/<?= $story['headline'] ?>">Edit</a> |
                <?php if($story['submitter_status'] == '2') : ?>
                <a id="ban_<?= $story['submitter_id'] ?>" class="ban">Unban Submitter</a>
                <?php else : ?>
                <a id="ban_<?= $story['submitter_id'] ?>" class="ban">Ban Submitter</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if(!empty($story['comment_id'])) : ?>
            <div class="comment">
                <a href="/users/<?= $story['commenter_username'] ?>"><?= $story['commenter_username'] ?></a> said: 
                <?= display_preview($story['comment']) ?><br/>
                Posted: <?= timespan($story['comment_posted']) ?> - Trashed by: <a href="/users/<?= $story['reporter_username'] ?>"><?= $story['reporter_username'] ?></a>
            </div>    
            <div class="admin-options">
                <a id="und_comment_<?= $story['id'] ?>" class="undelete">Undelete</a> |
                <?php if($story['submitter_status'] == '2') : ?>
                <a id="ban_<?= $story['submitter_id'] ?>" class="ban">Unban Submitter</a>
                <?php else : ?>
                <a id="ban_<?= $story['submitter_id'] ?>" class="ban">Ban Submitter</a>
                <?php endif; ?>
            </div>
	        <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <div id="pages"><?= $this->pagination->create_links() ?></div>
    <?php else : ?>
    <p>Hooray! Nothing in the trash :)</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>