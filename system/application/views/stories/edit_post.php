<?php 
	// Define calculated vars.
	$category 		= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat'];
	$tags 			= $story['category'].'-'.$story['subcat'];
	$headline_url 	= set_value('headline_url', $story['headline']);
	$pos_x 			= set_value('posx', $story['posx']);
	$pos_y 			= set_value('posy', $story['posy']);
	$where 			= set_value('where', $story['where']);
	$year 			= date('Y', $story['datesubmitted']);
	$month 			= date('m', $story['datesubmitted']);
	$day 			= date('d', $story['datesubmitted']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="/css/global.css"/>
<link type="text/css" rel="stylesheet" href="/css/story_submit.css"/>
<link type="text/css" rel="stylesheet" href="/css/datePicker.css"/>
<link type="text/css" rel="stylesheet" href="/js/fancybox/jquery.fancybox-1.3.1.css" media="screen"/>
<!--[if IE 6]>
<style type="text/css">
* #tags ul.subnav li { float: left; }
</style>
<![endif]-->
<!--[if IE 7]>
<style type="text/css">
* #tags ul.subnav li { float: left; }
</style>
<![endif]-->
<!--[if IE 6]>
<style type="text/css">
* #tags ul.subnav {	margin: 0 0 -30px 150px; }
* #tags li { height: 18px; }
* #tags a { line-height: 30px; }
</style>
<![endif]-->
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery.growfield2.js"></script>
<?php if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') : ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAdZQpG59yfHDnXCN5ofDeeRQrIVa_1su-_1cBa94uuJaHRmyXcRROrQuUm0Ai1KYpAA0TP4UnxvcPGw" type="text/javascript"></script>
<?php else : ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAdZQpG59yfHDnXCN5ofDeeRRyG9Jul30yA02Caxkzud86IZLvdhTK-l-jN_fe0bgCxAz0NjgnSilJSA" type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript" src="/js/jquery.jmap.min-r72.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript" src="/js/jquery.uploadify.js"></script>
<script type="text/javascript" src="/js/spellcheck/jquery.spellcheck.js"></script>
<script type="text/javascript">
var mediafileUploader;
$(function(){

	// Spell check.
	$('input[name=headline]').spellcheck();
	$('textarea[name=what]').spellcheck();
	
	// Expanable textarea.
	$('textarea[name=what]').growfield({'min': 100});

	// Enlarge images (fancybox)
	$('a.group').fancybox(); 

	function delete_image(id){
	
		// Confirm delete.
		if(!confirm('Are you sure you want to delete this image?')) 
			return false;
		
		// Do Delete.
		$.post('/stories/delete_image', 
				{ 
					'path': '/img/<?= $category ?>/<?= $year ?>/<?= $month ?>/<?= $day ?>/<?= $headline_url ?>',
					'id': id
				}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the link in the story.
						var filename = id.replace('_', '.');
						$('textarea[name=what]').val($('textarea[name=what]').val().replace(new RegExp("<image "+filename+">", "g"), ''));
						// Remove the image
						$('#im_'+id).fadeOut("slow", function(){ $(this).remove(); });
					}
				});
	}

	// Upload Files.
	$('#mediafile').fileUpload({
		'auto'		: true,
		'uploader'	: '/js/uploadify/uploader.swf',
		'script'	: '/upload',
		'cancelImg'	: '/js/uploadify/cancel.png',
		'folder'	: '/img/<?= $category ?>/<?= $year ?>/<?= $month ?>/<?= $day ?>/<?= $headline_url ?>',
		'multi'		: true,
		'fileDesc'	: 'Image Files (*.jpg; *.jpeg; *.gif; *.png)',
		'fileExt'	: '*.jpg; *.jpeg; *.gif; *.png',
		'sizeLimit' : (5 * 1024 * 1024),
		'onComplete': function(event, queueID, fileObj, response, data){
							
							if(response != 1) {
								eval('var res = '+response);
								$('#complete').append(res.html);
								
								$('textarea[name=what]').val("<image "+res.filename+">\n"+$('textarea[name=what]').val());
								
								$('#'+res.id).click(function() {
									delete_image(this.id);
								});
								
								$('a#thm_'+res.id).fancybox();
							}
					  }
	});
	
	mediafileUploader = document.getElementById('mediafileUploader');
	
	$('.dim').click(function(){
		delete_image(this.id);
	});


	// Tags
	$('#tags a').click(function(){
		$('input[name=tags]').val(this.id);
		$('#tags a').each(function() {
			$(this).removeAttr('style');
		});
		$(this).css({
			'background-color': '#333',
			'color': '#fff'
		});
		
		return false;
	});
	
	// Instanciate Google Map.
	$('#map').jmap('init', {
									'mapControl': 'small',
									<?php if(empty($pos_x) && empty($pos_y)) : ?>
									'mapZoom': 8,
									'mapCenter':[10.657189,-61.21994], 
									<?php else : ?>
									'mapZoom': 14,
									'mapCenter':[<?= $pos_x ?>, <?= $pos_y ?>], 
									<?php endif; ?>
									'mapShowjMapsIcon': false
								});
	
	<?php if(!empty($pos_x) && !empty($pos_y)) : ?>
	$('#map').jmap('AddMarker',{
			'pointHTML': '<?= htmlentities($where) ?>',
			'pointLatLng': [<?= $pos_x ?>, <?= $pos_y ?>],
			'pointIsDraggable': true,
			'centerMap': true,
			'centerMoveMethod': 'normal',
			'pointMaxZoom': 14,
			'dragEnd': function(marker){
							$('input[name=posx]').val(marker.lat());
							$('input[name=posy]').val(marker.lng());
					   }
		}, function(){
			$('input[name=posx]').val(<?= $pos_x ?>);
			$('input[name=posy]').val(<?= $pos_y ?>);
		});
	<?php endif; ?>
	
	function locate() {
		// Clear map.
		$('input[name=posx]').val('');
		$('input[name=posy]').val('');
		$('#map').jmap('clearOverlays');
		
		// Search if value is not null.
		if($('input[name=where]').val() != ''){
		
			$('#map').jmap('SearchAddress', {
				'query': $('input[name=where]').val()+', Trinidad & Tobago',
				'returnType': 'getLocations'
			}, function(result, options) {
				var valid = Mapifies.SearchCode(result.Status.code);
				if (valid.success) {
				$.each(result.Placemark, function(i, point){
					$('#map').jmap('AddMarker',{
							'pointHTML': point.address,
							'pointLatLng': [point.Point.coordinates[1], point.Point.coordinates[0]],
							'pointIsDraggable': true,
							'centerMap': true,
							'centerMoveMethod': 'normal',
							'pointMaxZoom': 14,
							'dragEnd': function(marker){
											$('input[name=posx]').val(marker.lat());
											$('input[name=posy]').val(marker.lng());
									   }
						}, function(){
							$('input[name=posx]').val(point.Point.coordinates[1]);
							$('input[name=posy]').val(point.Point.coordinates[0]);
						});
					});
				}
			});
			return false; 
		} 
	}
	
	$('#clr-map').click(function(){
		$('input[name=where]').val('');
		$('input[name=posx]').val('');
		$('input[name=posy]').val('');
		$('#map').jmap('clearOverlays');
		$('#map').jmap('init', {
									'mapControl': 'small',
									'mapZoom': 8,
									'mapCenter':[10.657189,-61.21994], 
									'mapShowjMapsIcon': false
								});
	});
	
							
    var delay;
	$('input[name=where]').keyup(function(){
		clearTimeout(delay);
		var val = this.value;
		if(val){
			 delay = setTimeout(function(){locate();}, 300);
		}
    });
	
	<?php if(empty($pos_x) && empty($pos_y)) : ?>
	locate();
	<?php endif; ?>
	
	function del(eid) {
		// Split the id.
		var id = eid.split('_');
		
		// Confirm delete.
		if(!confirm('Are you sure you want to delete this '+id[1]+'?')) 
			return false;
		// Do Delete.
		$.post('/delete', {'id': eid}, function (data){
					if(data != ''){
						// Give feedback.
						alert(data);	
					} else {
						// Remove the story
						var loc = new String(window.location);
						window.location = loc.substr(0, loc.indexOf('/', 8))+'/story_deleted';
					}
				});
	}
	
	$('.del_story').click(function(){
		del(this.id);
		return false;
	});
	
});
</script>
<title><?= $this->config->item('title') ?> - Edit Story</title>
</head>
<body>
<?php $this->load->view('commons/header'); ?>
<div id="main-content">
<form action="/edit" method="post">
	<input type="hidden" name="id" value="<?= $story['id'] ?>"/>
    <input type="hidden" name="headline_url" value="<?= set_value('headline_url', $story['headline']) ?>"/>
	<div id="maincol">
        <div id="headline">
            <label>Headline<span>Title of the story</span></label>
            <input name="headline" value="<?= set_value('headline', $story['headline_txt']) ?>"/>
            <?= form_error('headline') ?>
        </div>
        <div id="media" class="clearfix">
             <label>Upload Images</label>
            <input name="media" type="file" id="mediafile"/>
        	<?= form_error('media') ?>
            <div id="complete">
            <?php 
			// Display Images if they exist.	
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$headline_url.'/')) {
				$files = $this->util->list_files($_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$headline_url.'/');
				
				foreach($files as $img) {
					$safe_filename = str_replace('.', '_', $img['name']);
					echo '<div id="im_'.$safe_filename.'">'.
							'<a id="thm_'.$safe_filename.'" class="group" href="/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$headline_url.'/'.$img['name'].'">'.
								image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$headline_url.'/'.$img['name'], 100, 100, '', true).
							'</a>'.
							'<span class="filename">'.$img['name'].'</span>'.
							'<a id="'.$safe_filename.'" class="dim">Delete</a>'.
						 '</div>';
				}
			}
			?>
            </div>
            <p>By uploading images, you certify that you have the right to distribute these images and they do not violate the <a href="/terms">Terms of Use</a>.</p>
        </div>
        <div id="what">
        	<label>What happened<span>No HTML please.</span></label>
            <textarea name="what"><?= set_value('what', $story['what']) ?></textarea>
            <?= form_error('what') ?>
        </div>
        <div id="tags">
        	<label>Category<span>General Topic</span></label>
            <ul>
            	<?php $main_menu = $this->config->item('menu'); ?>
				<?php foreach($main_menu as $main_key => $main_value) : ?>
                    <li><a id="<?= $main_key.'-all' ?>" href="#"<?php if($tags == $main_key.'-all') echo ' style="background-color:#444;color:#fff"' ?>><?= $main_value ?></a>
                        <?php $sub_menus = $this->config->item('sub_menus'); $counter = 0; ?>
                        <?php if(isset($sub_menus[$main_key])) : ?>
                        <ul class="subnav">
                        <?php foreach($sub_menus[$main_key] as $sub_key => $sub_value) : ?>
                        <?php if(++$counter % 4 == 0) : ?>
                        </ul>
                    </li>
                    <li><div class="spcr">&nbsp;</div>
                        <ul class="subnav">
						<?php endif; ?>
                        <li><a id="<?= $main_key.'-'.$sub_key ?>" href="#"<?php if($tags == $main_key.'-'.$sub_key) echo ' style="background-color:#444;color:#fff"' ?>><?= $sub_value ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <input type="hidden" name="tags" value="<?= htmlentities(set_value('tags', $tags)) ?>"/><br/>
            <?= form_error('tags') ?>
        </div>
    </div>
    <div id="sidebar">
    	<div id="where">
        	<label>Where<span>City or town in TnT</span></label>
            <input name="where" value="<?= set_value('where', $story['where']) ?>"/><a id="clr-map">Reset</a>
            <input type="hidden" name="posx" value="<?= set_value('posx', $story['posx']) ?>"/>
            <input type="hidden" name="posy" value="<?= set_value('posy', $story['posy']) ?>"/>
            <?= form_error('where') ?>
            <div id="map"></div>
        </div>
        <div id="guidelines">
        	<h3>Guidelines for Posting</h3>
            <ul>
                <li>Be descriptive and concise about the details of your story.</li>
                <li>Stick to the facts. Avoid rumor and hearsay.</li>
                <li>Use images and video to support your story and give readers a rich experience.</li>
                <li>Don't post any content with spam, pornography, profanity or material that otherwise violates our <a href="/terms">Terms of Use</a>.</li>
            </ul>
        </div>
    </div>
    <div id="submit">
        <input type="submit" value="Update Story"/>
        <a id="del_story_<?= $story['id'] ?>" class="del_story" href="#">Delete Story</a>
    </div>
</form>
</div>
<?php $this->load->view('commons/footer'); ?>
</body>
</html>