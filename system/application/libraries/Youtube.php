<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Youtube {
	
	
	function Youtube(){
		require_once('Zend/Loader.php');
		Zend_Loader::loadClass('Zend_Gdata_YouTube');
		
		$this->yt = new Zend_Gdata_YouTube();
		$this->yt->setMajorProtocolVersion(2);
	}
	
	
	
	
	
	
	
	function get_user_videos($username){
		// Get videos from User via Youtube API.
		$videos = $this->yt->getuserUploads($username);
		$entries = array();
		
		foreach($videos as $index => $videoEntry){
			
			// Break down Rating information.
			$rating_info = $videoEntry->getVideoRatingInfo();
			$num_raters = isset($rating_info['numRaters']) ? $rating_info['numRaters'] : '0';
			$avg_rating = isset($rating_info['average']) ? $rating_info['average'] : '0';
			
			// Break down Thumbnail info.
			$thumb_info = $videoEntry->getVideoThumbnails();
			$default_thumb_url = isset($thumb_info[0]['url']) ? $thumb_info[0]['url'] : '';
			$default_thumb_width = isset($thumb_info[0]['width']) ? $thumb_info[0]['width'] : '';
			$default_thumb_height = isset($thumb_info[0]['height']) ? $thumb_info[0]['height'] : '';
			$hq_thumb_url = isset($thumb_info[4]['url']) ? $thumb_info[4]['url'] : '';
			$hq_thumb_width = isset($thumb_info[4]['width']) ? $thumb_info[4]['width'] : '';
			$hq_thumb_height = isset($thumb_info[4]['height']) ? $thumb_info[4]['height'] : '';
			
			// Build array.
			$entries[$index] = array(
				'id' 					=> $videoEntry->getVideoId(),
				'url'					=> 'http://www.youtube.com/watch?v='.$videoEntry->getVideoId(),
				'title' 				=> $videoEntry->getVideoTitle(),
				'description'			=> $videoEntry->getVideoDescription(),
				'duration'				=> $videoEntry->getVideoDuration(),
				'view_count'			=> $videoEntry->getVideoViewCount(),
				'category'				=> $videoEntry->getVideoCategory(),
				'published'				=> $videoEntry->getPublished()->text,
				'updated'				=> $videoEntry->getUpdated()->text,
				'num_raters'			=> $num_raters,
				'avg_rating'			=> $avg_rating,
				'default_thumb_url'		=> $default_thumb_url,
				'default_thumb_width'	=> $default_thumb_width,
				'default_thumb_height'	=> $default_thumb_height,
				'hq_thumb_url'			=> $hq_thumb_url,
				'hq_thumb_width'		=> $hq_thumb_width,
				'hq_thumb_height'		=> $hq_thumb_height
			);
		}
		
		return $entries;
    }
}
?>