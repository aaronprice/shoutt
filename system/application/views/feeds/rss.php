<?= '<?xml version="1.0" encoding="utf-8"?>' ?>

<rss version="2.0">
    <channel>
    <title><?= $feed_name; ?></title>
    <link><?= $feed_url; ?></link>
    <description><?= $page_description; ?></description>
    <?php foreach($stories as $story) : ?>
    <?php 
		$category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
		$year 		= date('Y', $story['datesubmitted']);
		$month 		= date('m', $story['datesubmitted']);
		$day 		= date('d', $story['datesubmitted']);
	?>
    <item>
      <title><?= xml_convert(display_headline($story['headline_txt'])); ?></title>
      <link><?= site_url($category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline']) ?></link>
      <guid><?= site_url($category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline']) ?></guid>
      <description><![CDATA[
      	<?php if(!empty($story['where'])) : ?><?= $story['where'] ?> - <?php endif; ?>
		<?= display_preview($story['what']) ?> 
        (<?= empty($story['popularity']) ? '1 vote' : $story['popularity'].' votes' ?>, <?= ($story['num_comments'] == 1) ? '1 comment' : $story['num_comments'].' comments' ?>)
      ]]></description>
      <pubDate><?php echo date('r', $story['datesubmitted']);?></pubDate>
	  <?php 
		$img_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'; 
		if(file_exists($img_path)) {
			// Get file list.
			$files = $this->util->list_files($img_path);

			if(count((array) $files) > 0) {
				
				$mime_type = 'image/jpeg';
				$info = pathinfo($files[0]['name']);
				
				switch($info['extension']) {
					case 'jpg':
					case 'jpe':
					case 'jpeg':
						$mime_type = 'image/jpeg';
						break;
					case 'gif':
						$mime_type = 'image/gif';
						break;
					case 'png':
						$mime_type = 'image/png';
						break;
				}
				
				image_thumb('/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/'.$files[0]['name'], 100, 100, '', true);
				
				echo '<enclosure url="'.site_url('img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/thm/'.$info['filename'].'_100_100.'.$info['extension']).'" length="'.$files[0]['size'].'" type="'.$mime_type.'" />';
			}
		}
      ?>
    </item>
    <?php endforeach; ?>
    </channel>
</rss>