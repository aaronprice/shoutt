<?php $counter = 0 ?>
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
	$('.del').click(function(){
		// Split the id.
		var id = this.id.split('_');
		var id_str = this.id;
		
		// Confirm delete.
		if(!confirm('Are you sure you want to delete this '+id[1]+'?')) 
			return false;
		// Do Delete.
		$.post('/stories/delete', 
				{ 
					'token': '<?= $user_id ?>',
					'id': id_str,
					'abuse': true
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
	
	$('.ign').click(function(){
		// Split the id.
		var id = this.id.split('_');
		var id_str = this.id;
		
		// Confirm delete.
		if(!confirm('Are you sure you want to ignore this '+id[1]+'?')) 
			return false;
		// Do Delete.
		$.post('/admin/abuse/ignore', 
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
<title><?= $this->config->item('title') ?> - Admin - Abuse</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/admin_menu'); ?>
<div id="main-content">
	<?php if(false) : ?>
	<pre>
    <?php print_r($reports); ?>
    </pre>
    <?php endif; ?>
	<?php if(count((array) $reports) > 0) : ?>
    <ul>
		<?php foreach($reports as $report) : ?>
        <?php if($report['story_view'] < '2' && $report['comment_view'] < 2) : ?>
        <?php ++$counter; ?>
        <?php 
            $category 	= ($report['subcat'] == 'all') ? $report['category'] : $report['subcat']; 
            $year 		= date('Y', $report['datesubmitted']);
            $month 		= date('m', $report['datesubmitted']);
            $day 		= date('d', $report['datesubmitted']);
        ?>
        <li id="abli_<?= $report['id'] ?>">
            <?php
            $img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$report['headline'].'/'; 
            if(file_exists($img_path)) {
                // Get file list.
                $files = $this->util->list_files($img_path);
                
                echo display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$report['headline'], image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$report['headline'].'/'.$files[0]['name'], 70, 70, '', true));
            }
            ?>
            <h2>
                <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$report['headline'], $report['headline_txt']) ?>
                <?php if(!empty($report['url'])) : ?>
                <?= display_ext_link($report['url'], '<img src="/img/el.gif" alt="External Link"/>') ?>
                <?php endif; ?>
            </h2>
            <div class="story-summary">
                <?php if(!empty($report['where'])) : ?><span class="story-location"><?= $report['where'] ?> &#151; </span><?php endif; ?>
                <?= display_preview($report['what']) ?>
            </div>
            <div class="story-details">
                <?= empty($report['url']) ? 'Posted' : 'Submitted' ?>: <?= timespan($report['datesubmitted']) ?> by <a href="/users/<?= $report['submitter_username'] ?>"><?= $report['submitter_username'] ?></a>
                in <a href="/<?= $category ?>"><?= $this->lang->line($category) ?></a> - 
                <?= display_story_link('/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$report['headline'].'#comments', ($report['num_comments'] == 1) ? '1 comment' : $report['num_comments'].' comments') ?>
                <?php if(empty($report['comment_id'])) : ?>
                - Reported by: <a href="/users/<?= $report['reporter_username'] ?>"><?= $report['reporter_username'] ?></a>
                <?php endif; ?>
            </div>
            <?php if(empty($report['comment_id'])) : ?>
            <div class="admin-options">
                <a id="ign_story_<?= $report['id'] ?>" class="ign">Ignore</a> |
                <a href="/edit/<?= $report['headline'] ?>">Edit</a> |
                <a id="del_story_<?= $report['id'] ?>" class="del">Delete</a> | 
                <?php if($report['submitter_status'] == '2') : ?>
                <a id="ban_<?= $report['submitter_id'] ?>" class="ban">Unban Submitter</a>
                <?php else : ?>
                <a id="ban_<?= $report['submitter_id'] ?>" class="ban">Ban Submitter</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if(!empty($report['comment_id'])) : ?>
            <div class="comment">
                <a href="/users/<?= $report['commenter_username'] ?>"><?= $report['commenter_username'] ?></a> said: 
                <?= display_preview($report['comment']) ?><br/>
                Posted: <?= timespan($report['comment_posted']) ?> - Reported by: <a href="/users/<?= $report['reporter_username'] ?>"><?= $report['reporter_username'] ?></a>
            </div>    
            <div class="admin-options">
                <a id="ign_comment_<?= $report['id'] ?>" class="ign">Ignore</a> |
                <?php if(false) : ?>
                <a href="/edit/<?= $report['headline'] ?>">Edit</a> |
                <?php endif; ?>
                <a id="del_comment_<?= $report['id'] ?>" class="del">Delete</a> | 
                <?php if($report['submitter_status'] == '2') : ?>
                <a id="ban_<?= $report['submitter_id'] ?>" class="ban">Unban Submitter</a>
                <?php else : ?>
                <a id="ban_<?= $report['submitter_id'] ?>" class="ban">Ban Submitter</a>
                <?php endif; ?>
            </div>
	        <?php endif; ?>
        </li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php if($counter == 0) : ?>
    <p>Hooray! no-one's abusing the site :)</p>
    <?php else : ?>
    <div id="pages"><?= $this->pagination->create_links() ?></div>
    <?php endif; ?>
    <?php else : ?>
    <p>Hooray! no-one's abusing the site :)</p>
    <?php endif; ?>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>