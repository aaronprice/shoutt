<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/ads_create.css"/>
<link type="text/css" rel="stylesheet" href="/css/datePicker.css"/>
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.uploadify.js"></script>
<script type="text/javascript" src="/js/date.js"></script>
<!--[if IE]><script type="text/javascript" src="/js/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="/js/jquery.datePicker.js"></script>
<script type="text/javascript">
Date.firstDayOfWeek = 7;
Date.format = 'yyyy-mm-dd';
d = new Date();
var mediafileUploader;
$(function(){

	function delete_image(){
	
		// Confirm delete.
		if(!confirm('Are you sure you want to delete this image?')) 
			return false;
		
		// Do Delete.
		$.post('/ads/delete_image', 
				{ 
					'path': '/tmp/<?= $img_folder ?>'
				}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);
					} else {
						// Remove the image
						$('#ad_image img').fadeOut("slow", function(){ $(this).remove(); });
						$('#rem_img_cont a').fadeOut("slow", function(){ $(this).remove(); });
					}
				});
	}

	// Upload Files.
	$('#mediafile').fileUpload({
		'auto'		: true,
		'uploader'	: '/js/uploadify/uploader.swf',
		'script'	: '/ads/upload',
		'cancelImg'	: '/js/uploadify/cancel.png',
		'folder'	: '/tmp/<?= $img_folder ?>',
		'multi'		: false,
		'fileDesc'	: 'Image Files (*.jpg; *.jpeg; *.gif; *.png)',
		'fileExt'	: '*.jpg; *.jpeg; *.gif; *.png',
		'sizeLimit' : (5 * 1024 * 1024),
		'onComplete': function(event, queueID, fileObj, response, data){
							
							if(response != 1) {
								eval('var res = '+response);
								$('#ad_image').html('<img src="'+res.imgpath+'" alt="">');
								$('input[name=img_name]').val(res.filename);
								$('#rem_img_cont').html('<a id="del_img">Delete Image</a>');
								$('#del_img').click(function(){ delete_image();	});
							}
					  }
	});
	
	mediafileUploader = document.getElementById('mediafileUploader');
	
	$('#del_img').click(function(){ delete_image();	});
	
	$('input[name=title],textarea[name=description]').keyup(function(){
		$('#ad_'+this.name).html($(this).val());
		if(this.name == 'title' && $(this).val() == '') $('#ad_'+this.name).html('Your Advertisement');
		if(this.name == 'description' && $(this).val() == '') $('#ad_'+this.name).html('Description of your product or service.');
	});
	
	
	// Date picker.
	$('.date-pick').datePicker();
	
	// Remove date when duration not selected.
	$('input[name=duration]').change(function() {
		$('input[name=end_date]').attr('value', '');
	});
});
</script>
<title><?= $this->config->item('title') ?> - Advertising - Create your ad</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<?php $this->load->view('commons/messages'); ?>
<div id="main-content">
    <h3>Create Ad</h3>
    <form action="/ads/create" method="post">
    <div id="ad_form">
        <label>URL e.g.: http://www.example.com</label><input class="txt" name="url" value="<?= set_value('url') ?>"/><br/>
        <?= form_error('url') ?>
        <label>Title</label><input class="txt" name="title" value="<?= set_value('title') ?>"/><br/>
        <?= form_error('title') ?>
        <label>Image (optional)</label><input name="img" type="file" id="mediafile"/><br/>
        <input type="hidden" name="img_name" value="<?= set_value('img_name') ?>" />
        <div id="rem_img_cont">
            <?php if(is_file($_SERVER['DOCUMENT_ROOT'].'/tmp/ad_'.$img_folder.'/'.set_value('img_name'))) : ?>
            <a id="del_img">Delete Image</a>
            <?php endif; ?>
        </div>
        <label>Short Description</label><textarea name="description"><?= set_value('description') ?></textarea><br/>
        <?= form_error('description') ?>
        <input type="submit" class="btn" value="Create Advertisement"/>
    </div>
    <div id="ad_preview">
    	<div id="ad_title"><?= (set_value('title') == '') ? 'Your Advertisement' : set_value('title') ?></div>
        <div id="ad_image">
			<?php if(is_file($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$img_folder.'/'.set_value('img_name'))) : ?>
        	<img src="/tmp/<?= $img_folder ?>/<?= set_value('img_name') ?>" alt="Ad Image" />
			<?php endif; ?>
        </div>
        <div id="ad_description"><?= (set_value('description') == '') ? 'Description of your product or service.': set_value('description') ?></div>
    </div>
    <div id="ad_campaign">
    	<label>Budget (US$)</label> <input class="txt" name="budget" value="<?= set_value('budget', '50.00') ?>"/><br/>
        <?= form_error('budget') ?>
        <label>Run advertisement campaign:</label>
        <label><input type="radio" name="campaign_type" value="3"<?= set_radio('campaign_type', '3') ?>/> Agressive</label>
        <label><input type="radio" name="campaign_type" value="2"<?= set_radio('campaign_type', '2', true) ?>/> Normal</label>
        <label><input type="radio" name="campaign_type" value="1"<?= set_radio('campaign_type', '1') ?>/> Economical</label>
        <br/>
        <?= form_error('campaign_type') ?>
        <label>Until:</label>
        <label><input type="radio" name="duration" value="2"<?= set_radio('duration', '2') ?>/> End Date <input name="end_date" class="date-pick" value="<?= set_value('end_date') ?>"/></label>
        <label><input type="radio" name="duration" value="1"<?= set_radio('duration', '1', true) ?>/> Budget Expiry</label>
        <br/>
        <?= form_error('duration') ?>
        <?= form_error('end_date') ?>
    </div>
    <div id="ad_legal">
    </div>
    </form>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>