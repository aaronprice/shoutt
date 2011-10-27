<?php

class Story extends Model {

	function Story(){
		parent::Model();
		
		// Load the util class for cleaning values.
		$this->load->library('util');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function abuse_info($id){
		// Delete from the abuse page.
		$query = $this->db->get_where('abuse', array('id' =>  $id));
		if($query->num_rows > 0){
			return $query->row_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function add_comment() {
	
		$query = $this->db->get_where( 'stories', array( 'id' => $_POST[ 'si' ] ) );
		$story = $query->row_array();
	
		// Reply to is not always manditory. Handle separately.
		$reply_to = isset($_POST['rt']) ? $_POST['rt'] : 0;
		
		$user_id = $this->session->userdata($this->config->item('session_key').'_usr');
		
		// Prepare data for insertion.
		$data = array(
					'user_id'		=> $user_id,
					'story_id'	 	=> $this->util->clean($_POST['si']),
					'comment'		=> $this->util->clean($_POST['comment']),
					'dateposted' 	=> time(),
					'ip'			=> $_SERVER['REMOTE_ADDR'],
					'user_agent'	=> $_SERVER['HTTP_USER_AGENT'],
					'reply_to'		=> $this->util->clean($reply_to),
					'view'			=> '1'
				);
		// Insert data.
		$this->db->insert('comments', $data);
		
		$comment_id = $this->db->insert_id();
		
		if ( $story[ 'user_id' ] != $user_id ) {
			// Add score to assessment story.
			$this->append_score($data['story_id'], $this->config->item('add_comment_score'));
		}
		
		// Return the comment_id;
		return $comment_id;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function add_to_favorites($story_id, $user_id){
		// Prepare data for insertion.
		$data = array(
					'story_id'	 	=> $this->util->clean($story_id),
					'user_id'		=> $this->util->clean($user_id),
					'date_registered' => time(),
					'ip'			=> $_SERVER['REMOTE_ADDR'],
					'user_agent'	=> $_SERVER['HTTP_USER_AGENT']
				);
				
		// Insert data.
		$this->db->insert('favorites', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function already_submitted($headline) {
		
		// Get what headline is supposed to look like.
		$headline = trim(strtolower(preg_replace("/[^a-zA-Z0-9]+/","_", html_entity_decode($headline))), '_');
		
		// Get all stories with the same headline submitted in the last 10 minutes.
		$query = $this->db->get_where('stories', array('headline' => $headline, 'datesubmitted >' => (time() - 60 * 10)));
		
		return ($query->num_rows() == 0) ? false : true;
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function append_score($story_id, $score) {
			// update score in story_score.
			$this->db->query("UPDATE story_score
							  SET score = (score + ".$score.")
							  WHERE story_id = '".$story_id."'");
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	function assess($story_id, $act=true){
		
		if($story_id != '') {
			
			// Get story info.
			$story = $this->info_from_id($story_id);
			
			// Define points. Each category is out of 10 points 
			$score = array();
			$max_category_points = 10;
			
			// Define story information.
			$category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
            $year 		= date('Y', $story['datesubmitted']);
            $month 		= date('m', $story['datesubmitted']);
            $day 		= date('d', $story['datesubmitted']);
			
			
			
			/*****************************************************************/
			/*                1. Analyse Story Length                        */
			/*****************************************************************/
			
			$raw_story = preg_replace('#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm','', strip_tags($story['what']));
			
			$score['story_length'] = 0;
			$num_chars = strlen($raw_story);
			$min_chars = 300;
			$max_chars = 2500;
			
			switch(true) {
				case ($num_chars >= $min_chars && $num_chars <= ($max_chars / 2)): 
					$score['story_length'] = $num_chars / ($max_chars / 2) * $max_category_points;
					break;
				case ($num_chars > ($max_chars / 2) && $num_chars < $max_chars):
					$score['story_length'] = ($max_chars - $num_chars) / (($max_chars - ($max_chars / 2)) / $max_category_points);
					break;
				default:
					$score['story_length'] = 0;
					break;
			}
			
			
			
			/*****************************************************************/
			/*                2. Analyse Readability                         */
			/*****************************************************************/
			// A good story should be easy to understand and very readable.
			// Load the TextStatistics Library.
			$this->load->library('textstatistics');
			
			$flesch_kincaid_reading_ease_score = $this->textstatistics->flesch_kincaid_reading_ease($story['what']);
			
			$score['readability'] = ($flesch_kincaid_reading_ease_score / 100) * $max_category_points;
			
			// Determine what level of education needed to understand.
			$edu_level = array(
							   $this->textstatistics->flesch_kincaid_grade_level($story['what']),
							   $this->textstatistics->gunning_fog_score($story['what']),
							   $this->textstatistics->coleman_liau_index($story['what']),
							   $this->textstatistics->smog_index($story['what']),
							   $this->textstatistics->automated_readability_index($story['what'])
							   );
			
			$avg_edu_level = array_sum($edu_level) / count($edu_level);
			
			$score['edu_level'] = 0;
			switch(true) {
				case ($avg_edu_level >= 8):
					$score['edu_level'] = 10;
					break;
				default:
					$score['edu_level'] = 0;
					break;
			}
			
			
			
			
			/*****************************************************************/
			/*                3. Check for Profanity                         */
			/*****************************************************************/
			$score['profanity'] = 0;
			// A good story doesn't have any profanity.
			$profanity_count = profanity_counter($story['what'], $this->config->item('profanity_words'));
			$profanity_count += profanity_counter($story['headline_txt'], $this->config->item('profanity_words'));
			
			// This is pass / fail.
			$score['profanity'] = ($profanity_count == 0) ? 10 : 0;
			
			
			
			
			
			/*****************************************************************/
			/*                4. Check for Media                            */
			/*****************************************************************/
			$score['media'] = 0;
			// A good story should have pictures and/or video.
			$images_exist = false;
			$video_exists = false;
			$num_images = 0;
			
			$path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'].'/';
			if(file_exists( $path )) {
				$files = $this->util->list_files( $path );
				
				if(count((array) $files) > 0) {
					
					// People like to see large images.
					foreach ( $files as $img ) {
						list( $width, $height ) = @getimagesize( $path.$img[ 'name' ] );
						
						if ( $width > 400 && $height > 300 ) {
							++ $num_images;
							
							$images_exist = true;
						}
					}
				}
				
			} elseif(preg_match("#http://(www\.)?youtube.com/watch\?v=([A-Za-z0-9_-]*)(.*)?#", $story['what']) ||
					preg_match("#http://video.google.com/videoplay\?docid=([0-9\-]*)(.*)?#", $story['what']) ||
					preg_match("#http://(www\.)?vimeo.com/([0-9]*)(.*)?#", $story['what']) ||
					preg_match("#http://www.facebook.com/(.*?)video/video.php\?v=([0-9]*)(.*)?#", $story['what']) ) {
				$video_exists = true;
			}
			
			$score['media'] = ($images_exist == true || $video_exists == true) ? 10 : 0;
			
			
			
			
			
			/*****************************************************************/
			/*                5. Check for Location                          */
			/*****************************************************************/
			// A good story would have a location.
			$score['location'] = (empty($story['where']) || empty($story['posx']) || empty($story['posy'])) ? 0 : 10;
			// Expand the margin for location to make location more important in Stories.
			$score['location_1'] = (empty($story['where']) || empty($story['posx']) || empty($story['posy'])) ? 0 : 10;
			
			
			
			
			
			
			/*****************************************************************/
			/*                8. Analyse Diversity                           */
			/*****************************************************************/
			// There shouldn't too many stories posted in the a single category at a time.
			$num_stories_in_category = $this->count_where(array('category' => $category));
			$total_stories = $this->count_where();
			if($num_stories_in_category == 0 || $total_stories) {
				$score['diversity'] = $max_category_points;
			} else {
				$diversity = $total_stories / $num_stories_in_category;
				$score['diversity'] = ($diversity >= $max_category_points) ? $max_category_points : $diversity;
			}
			
			
			
			
			
			/*****************************************************************/
			/*****************************************************************/
			/**                                                             **/
			/**                    Add Weighting and Tally.                 **/
			/**                                                             **/
			/*****************************************************************/
			/*****************************************************************/
			$num_factors = count($score);
			// Make stories harder to get on the front page.
			$total = -20;
			
			// Calculate score and add appropriate weighting.
			foreach($score as $key => $value) {
				if($this->config->item($key.'_weight') !== false) {
					$score[$key] = $value * $this->config->item($key.'_weight');
				}
				
				$total += $score[$key];
			}
			
			// Get result as a percentage.
			$percentage = $total / ($num_factors * $max_category_points) * 100;
			
			
			
			
			/*****************************************************************/
			/*****************************************************************/
			/**                                                             **/
			/**            Apply factors directly to final score            **/
			/**                                                             **/
			/*****************************************************************/
			/*****************************************************************/
			
			
			
			/*****************************************************************/
			/*                   Analyse Category Choice                     */
			/*****************************************************************/
			if(in_array($category, $this->config->item('topical'))){
				$percentage += 20;
			} else {
				switch($category) {
					case 'murder':
					case 'kidnapping':
						$percentage -= 40; break;
					
					case 'crime':
					case 'burglary':
					case 'robbery':
					case 'assult':
					case 'other_crime':
					case 'misc':
					case 'comedy':
						$percentage -= 10; break;
						
					// case 'lifestyle':
					// case 'culture':
					// case 'health':
					// case 'safety':
					// case 'features':
					// case 'arts':
					// case 'people':
					// case 'sports':
					// case 'cricket':
					// case 'soccer':
					// case 'other_sports':
					// case 'education':
					// case 'awareness':
					// case 'environment':
					// case 'going_green':
					// 	$percentage += 10; break;
				}
			}
			
			
			/*****************************************************************/
			/*                       Analyse Votes                           */
			/*****************************************************************/
			// Good stories have positive ratio in voting.
			$percentage += ($this->get_vote_count($story_id) * intval($this->config->item('promote_story_score')));
			





			/*****************************************************************/
			/*                      Analyse Comments                         */
			/*****************************************************************/
			// Good stories should encourage discussion. Comments are positive.
			$percentage += ($this->count_comments_where(array('story_id' => $story_id, 'user_id <>' => $story['user_id'])) * intval($this->config->item('add_comment_score')));
			
			
			
			
			
			/*****************************************************************/
			/*                      Award points for images                  */
			/*****************************************************************/
			// Good stories should encourage discussion. Comments are positive.
			$percentage += ($num_images * intval($this->config->item('add_image_score')));
			
			
			
			
			
			
			// Determine if to promote or demote story or just display score.
			if($act == true) {
				if($percentage >= 50 && $story['view'] == 0) {
					$this->promote($story_id);
				} else if($percentage < 50 && $story['view'] == 1) {
					$this->demote($story_id);
				}
				
				// Save Score.
				$this->save_score($story_id, $percentage);
			} else {
				// Just return the result.
				return $percentage;
			}


			// Good users post good stories.
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function check_duplicate_submission($url){
		$query = $this->db->get_where('stories', array('url' => $url));
		
		if($query->num_rows() == 0) return false;
		else {
			$story = $query->row_array();
			
			$category 	= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat']; 
            $year 		= date('Y', $story['datesubmitted']);
            $month 		= date('m', $story['datesubmitted']);
            $day 		= date('d', $story['datesubmitted']);
			
			return '/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'];
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
	
	
	
	
	
	
	function check_old_url($headline) {
		// Check if the record exists.
		$this->db->where('headline', $headline);
		$query = $this->db->get('url_map');
		
		// If so, return the story id.
		if($query->num_rows() > 0){
			$row = $query->row();
			return $row->story_id;
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	function comment_has_replies($comment_id) {
		$this->db->select('id');
		$query = $this->db->get_where('comments', array('reply_to' => $comment_id));
		
		return ($query->num_rows() > 0) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	function comment_info($comment_id) {
		$this->db->select('c.*');
		$this->db->select('u.username');
		$this->db->select('(SELECT COUNT(c1.id) FROM comments c1 WHERE c.id = c1.reply_to AND c1.view = 1) AS num_replies');
		$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 2 AND v.type_id = c.id) AS popularity');
		$this->db->from('comments c');
		$this->db->join('users u', 'c.user_id = u.id');
		$this->db->where('c.id', $comment_id);
		$this->db->group_by('c.id');
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->row_array() : array();
	}
	
	
	
	
	
	
	
	
	
	
	function count_comments_where($where=array()){
		$this->db->where($where);
		$this->db->where('view', '1');
		return $this->db->count_all_results('comments');
	}
	
	
	
	
	
	
	
	
	function click($story_id) {
		// Prepare data for insertion.
		$data = array(
					'story_id'		=> $story_id,
					'user_id'		=> $this->session->userdata($this->config->item('session_key').'_usr'),
					'date_viewed'	=> time(),
					'ip'			=> $_SERVER['REMOTE_ADDR'],
					'user_agent'	=> $_SERVER['HTTP_USER_AGENT']
				);
		// Insert data.
		$this->db->insert('story_views', $data);
	}
	
	
	
	
	
	
	
	function create($followup=''){
		
		// Format headline and Location.
		$headline = format_headline($this->util->clean($_POST['headline']));
		$location = format_headline($this->util->clean($_POST['where']));
		
		// Create url safe headline.
		$headline_url = $this->get_headline_url($headline);
		
		// Separate tags into cateogry and sub-category.
		$tags = explode('-', $this->util->clean($_POST['tags']));
		$category = ($tags[1] == 'all') ? $tags[0] : $tags[1];
		
		// Insert url if exists.
		//$url = isset($_POST['url']) ? $this->util->clean($_POST['url']) : '';
		$url = $this->session->userdata('submit_url');
		$this->session->unset_userdata(array(
			'submit_url' => ''
		));
		$url = ($url == '0') ? '' : $url;
		
		// Get time inserted.
		$date_submitted = time();
		$year 	= date('Y', $date_submitted);
		$month 	= date('m', $date_submitted);
		$day 	= date('d', $date_submitted);
		
		// Replace the default caption text.
		$what = normalize_text($_POST['what']);
		
		// Get user_id from session.
		$user_id = $this->session->userdata($this->config->item('session_key').'_usr');
		
		// Prepare data for insertion.
		$data = array(
					'user_id'		=> $user_id,
					'url' 			=> $url,
					'headline_txt' 	=> $headline,
					'headline' 		=> $headline_url,
					'what' 			=> $this->util->clean($what, '<image>'),
					'category'		=> $tags[0],
					'subcat'		=> $tags[1],
					'where'			=> $location,
					'posx'			=> $this->util->clean($_POST['posx']),
					'posy'			=> $this->util->clean($_POST['posy']),
					'datesubmitted'	=> $date_submitted,
					'ip'			=> $_SERVER['REMOTE_ADDR'],
					'user_agent'	=> $_SERVER['HTTP_USER_AGENT'],
					'view'			=> '1'
				);
		
		// Insert data.
		$this->db->insert('stories', $data);
		
		// Get story id incase this is a follow up story.
		$story_id = $this->db->insert_id();
		
		// Assess the story.
		if(empty($url))
			$this->assess($story_id);
		
//		// If this story is part of a chain,
//		if(!empty($followup)){
//		
//			// Get the story chain this story should belong to.
//			// Get story details.
//			$this->db->where('headline', $followup);
//			$story_exists = $this->db->get('stories');
//			
//			// Define Chain ID for later use.
//			$chain_id = '';
//			
//			if($story_exists->num_rows() > 0) {
//				// Get story details.
//				$story = $story_exists->row_array();
//				
//				// If table is empty, return 1.
//				$story_chain_empty = $this->db->get('story_chain');
//				if($story_chain_empty->num_rows() == 0){
//				
//					$chain_id = 1;
//					
//					// Insert row for previous story.
//					$chain_data = array(
//									'story_id' 		=> $story['id'],
//									'chain_id' 		=> $chain_id,
//									'user_id'		=> $user_id,
//									'date_added' 	=> time(),
//									'ip'			=> $_SERVER['REMOTE_ADDR'],
//									'user_agent'	=> $_SERVER['HTTP_USER_AGENT'],
//								  );
//					
//					// Insert row.
//					$this->db->insert('story_chain', $chain_data);
//				} else {
//					// Table has rows.
//					$this->db->where('story_id', $story['id']);
//					$story_chain_exists = $this->db->get('story_chain');
//					
//					if($story_chain_exists->num_rows() > 0){
//						// Get the value of the story chain to link on.
//						$story_chain = $story_chain_exists->row_array();
//						$chain_id = $story_chain['chain_id'];
//					} else {
//						// Look for the max story chain and increment it to get the next id.
//						$this->db->select_max('chain_id');
//						$max_chain_id_query = $this->db->get('story_chain');
//						$max_chain_id_row = $max_chain_id_query->row_array();
//						
//						$chain_id = $max_chain_id_row['chain_id'] + 1;
//						
//						// Insert row for previous story.
//						$chain_data = array(
//										'story_id' 		=> $story['id'],
//										'chain_id' 		=> $chain_id,
//										'user_id'		=> $user_id,
//										'date_added' 	=> time(),
//										'ip'			=> $_SERVER['REMOTE_ADDR'],
//										'user_agent'	=> $_SERVER['HTTP_USER_AGENT'],
//									  );
//						
//						// Insert row.
//						$this->db->insert('story_chain', $chain_data);
//						
//					} // end if($story_chain_exists->num_rows() > 0){
//					
//				} // end if($story_chain_empty->num_rows() == 0){
//				
//			} // end if($story_exists->num_rows() > 0) {
//			
//			
//			// Insert a row in the story_chain table.
//			$story_chain_data = array(
//									'story_id' 		=> $story_id,
//									'chain_id' 		=> $chain_id,
//									'user_id'		=> $user_id,
//									'date_added' 	=> time(),
//									'ip'			=> $_SERVER['REMOTE_ADDR'],
//									'user_agent'	=> $_SERVER['HTTP_USER_AGENT'],
//								);
//								
//			$this->db->insert('story_chain', $story_chain_data);
//			
//		} //end if(!empty($followup)){
		
		
		// Determine category.
		$category = ($tags[1] == 'all') ? $tags[0] : $tags[1];
		
		// Return url.
		return $category.'/'.$year.'/'.$month.'/'.$day.'/'.$headline_url;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function delete($user_id, $story_id, $comment_id='') {
	
		// Determine status.
		$status = '0';
	
		// Get the story or comment info.
		$query = '';
		if(empty($comment_id)){
			$query = $this->db->get_where('stories', array('id' => $story_id));
		} else {
			$query = $this->db->get_where('comments', array('id' => $comment_id));
		}
		
		if($query->num_rows() > 0){
			$row = $query->row_array();
			$status = $row['view'];
		}
		
	
		// Insert a row into the trash table for administrative view, doc and undelete purposes.
		// Prep data for insertion.
		$data = array(
					'story_id' 			=> $story_id,
					'comment_id' 		=> $comment_id,
					'status'			=> $status,
					'user_id' 			=> $user_id,
					'date_registered' 	=> time(),
					'ip'				=> $_SERVER['REMOTE_ADDR'],
					'user_agent'		=> $_SERVER['HTTP_USER_AGENT']
				);
				
		// Insert data.
		$this->db->insert('trash', $data);
	
	
		// Set the view to 2 but don't actually delete the article. Might come in handy.
		$set = array(
					'view' => '2'
			   );
		
		if(empty($comment_id)){	   
			$this->db->where('id', $story_id);
			$this->db->update('stories', $set);
		} else {
			$this->db->where('id', $comment_id);
			$this->db->update('comments', $set);
			
			// Add score to assessment story.
			$this->append_score($story_id, $this->config->item('del_comment_score'));
		}
	}

	
	
	
	
	
	
	
	
	
	
	
	function count_abuse_where($where=array()) {
		$this->db->where($where);
		return $this->db->count_all_results('abuse');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function count_trash_where($where=array()) {
		$this->db->where($where);
		return $this->db->count_all_results('trash');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function count_where($where=array()){
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Filter by date.
		if(!isset($where['datesubmitted'])) {
			$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
			$this->db->where('datesubmitted >= ', $time_ago);
		}
	
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('view', '1');
		return $this->db->count_all_results('stories');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_abuse_where($where=array(), $limit='', $offset='') {
		$this->db->select('a.*');
		$this->db->select('(SELECT u.username FROM users u WHERE u.id = a.user_id) AS reporter_username');
		$this->db->select('(SELECT headline_txt FROM stories s WHERE s.id = a.story_id) AS headline_txt');
		$this->db->select('(SELECT headline FROM stories s WHERE s.id = a.story_id) AS headline');
		$this->db->select('(SELECT category FROM stories s WHERE s.id = a.story_id) AS category');
		$this->db->select('(SELECT subcat FROM stories s WHERE s.id = a.story_id) AS subcat');
		$this->db->select('(SELECT datesubmitted FROM stories s WHERE s.id = a.story_id) AS datesubmitted');
		$this->db->select('(SELECT what FROM stories s WHERE s.id = a.story_id) AS what');
		$this->db->select('(SELECT `where` FROM stories s WHERE s.id = a.story_id) AS `where`');
		$this->db->select('(SELECT `view` FROM stories s WHERE s.id = a.story_id) AS story_view');
		$this->db->select('(SELECT user_id FROM stories s WHERE s.id = a.story_id) AS submitter_id');
		$this->db->select('(SELECT u.username FROM users u, stories s WHERE u.id = s.user_id AND s.id = a.story_id) AS submitter_username');
		$this->db->select('(SELECT u.status FROM users u, stories s WHERE u.id = s.user_id AND s.id = a.story_id) AS submitter_status');
		$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = a.story_id AND c.view = 1) AS num_comments');
		$this->db->select('(SELECT comment FROM comments c WHERE c.story_id = a.story_id AND c.id = a.comment_id) AS comment');
		$this->db->select('(SELECT dateposted FROM comments c WHERE c.story_id = a.story_id AND c.id = a.comment_id) AS comment_posted');
		$this->db->select('(SELECT `view` FROM comments c WHERE c.story_id = a.story_id AND c.id = a.comment_id) AS comment_view');
		$this->db->select('(SELECT u.username FROM users u WHERE u.id = (SELECT user_id FROM comments c WHERE c.id = a.comment_id)) AS commenter_username');
		$this->db->from('abuse a');
		$this->db->where($where);
		$this->db->where('a.ignore', '0');
		$this->db->group_by('a.id');
		$this->db->order_by('a.id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_participants($story_id){
		$this->db->select('username');
		$this->db->distinct();
		$this->db->from('users u');
		$this->db->join('activity a', 'u.id = a.user_id');
		$this->db->where('story_id', $story_id);
		$query = $this->db->get();
		
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_chains($where=array(), $limit='', $offset=''){
	
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				//$this->db->where('category', $where['category']);
				//$this->db->or_where('subcat', $where['category']);
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Filter by date.
		if(!isset($where['datesubmitted'])) {
			$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
			$this->db->where('datesubmitted >= ', $time_ago);
		}
	
		
		// Get story from database.
		$this->db->select('sc.chain_id');
		$this->db->select('(SELECT MIN(s1.datesubmitted) FROM stories s1, story_chain sc1 WHERE s1.id = sc1.story_id AND sc1.chain_id = sc.chain_id) AS start_date');
		$this->db->select('(SELECT MAX(s1.datesubmitted) FROM stories s1, story_chain sc1 WHERE s1.id = sc1.story_id AND sc1.chain_id = sc.chain_id) AS end_date');
		$this->db->select('(SELECT s1.headline_txt FROM stories s1, story_chain sc1 WHERE s1.id = sc1.story_id AND sc1.chain_id = sc.chain_id ORDER BY s1.datesubmitted DESC LIMIT 1) AS start_headline_txt');
		$this->db->select('(SELECT s1.headline_txt FROM stories s1, story_chain sc1 WHERE s1.id = sc1.story_id AND sc1.chain_id = sc.chain_id ORDER BY s1.datesubmitted LIMIT 1) AS end_headline_txt');
		$this->db->select('(SELECT s1.posx FROM stories s1, story_chain sc1 WHERE s1.id = sc1.story_id AND sc1.chain_id = sc.chain_id ORDER BY s1.datesubmitted DESC LIMIT 1) AS posx');
		$this->db->select('(SELECT s1.posy FROM stories s1, story_chain sc1 WHERE s1.id = sc1.story_id AND sc1.chain_id = sc.chain_id ORDER BY s1.datesubmitted DESC LIMIT 1) AS posy');
		$this->db->select('(SELECT COUNT(sc.story_id) FROM story_chain sc1 WHERE sc1.chain_id = sc.chain_id) AS num_stories');
		$this->db->from('story_chain sc');
		$this->db->join('stories s', 'sc.story_id = s.id');
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('s.view', '1');
		$this->db->group_by('sc.chain_id');
		$this->db->order_by('sc.chain_id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function get_comment($id){
		$this->db->where('id', $id);
		$query = $this->db->get('comments');
		
		// if comment exists.
		if($query->num_rows() > 0){
			
			// Return comment info.
			return $query->row_array();
			
		// Else retrun false;
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_comment_votes_where($where=array()) {
	
		$user_id = $this->session->userdata($this->config->item('session_key').'_usr');
		
		if(empty($user_id)) {
			return array();
		} else {
			
			// Get story from database.
			$this->db->select('v.*');
			$this->db->from('votes v');
			$this->db->join('comments c', 'c.id = v.type_id');
			$this->db->where('v.type', '2');
			$this->db->where('v.user_id', $user_id);
			$this->db->where($where);
			if(!isset($where['view']))
				$this->db->where('c.view', '1');
			$this->db->group_by('c.id');
			$query = $this->db->get();
			
			if($query->num_rows() > 0) {
				// Put votes into array.
				$votes = $query->result_array();
				// Create return array.
				$arr = array();
				foreach($votes as $vote) {
					$arr[$vote['type_id']] = $vote['value'];
				}
				
				return $arr;
				
			} else return array();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_comments_where($where=array(), $limit='', $offset=''){
		
		// Get comments from database.
		$this->db->select('c.*');
		$this->db->select('u.username');
		$this->db->select('(SELECT COUNT(c1.id) FROM comments c1 WHERE c.id = c1.reply_to AND c1.view = 1) AS num_replies');
		$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 2 AND v.type_id = c.id) AS popularity');
		$this->db->from('comments c');
		$this->db->join('users u', 'c.user_id = u.id');
		$this->db->where($where);
		$this->db->where('c.view', '1');
		$this->db->group_by('c.id');
		$this->db->order_by('c.id', 'asc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	function get_headline_url($headline){
		
		// Convert anything not a number to an underscore.
		// Remove duplicate underscores and underscores on end.
		// Convert to lowercase.
		$headline = trim(strtolower(preg_replace("/[^a-zA-Z0-9]+/","_", html_entity_decode($headline))), '_');
		
		// Add numbers to the end (for duplicate titles) if necessary.
		$query = $this->db->query("SELECT headline
								   FROM stories
								   WHERE headline like '".$headline."%'
								   UNION
								   SELECT headline
								   FROM url_map
								   WHERE headline like '".$headline."%'
								   ORDER BY headline ASC");		
		
		// if a duplicate title exists.
		if($query->num_rows() > 0){
		
			// Get the result in terms of an array.
			$result = $query->result_array();
			
			// Search for the latest story.
			$latest_headline = '';
			foreach($result as $row){
				if(preg_match('/\b'.$headline.'\b/', $row['headline']) || preg_match('/'.$headline.'_[0-9]{1,}/', $row['headline'])){
					$latest_headline = $row['headline'];
				}
			}
			
			// If there's only one title matching and they're equal.
			if($latest_headline == $headline) {
			
				// Return a second.
				return $headline.'_2';
			
			} else if(is_numeric(substr($latest_headline, strlen($headline.'_')))){
			
				// Get the last increment.
				$last_increment = intval(substr($latest_headline, strlen($headline.'_')));
				
				// Return last value plus one.
				return $headline.'_'.(++$last_increment);
			
			// It's a different title all together.
			} else return $headline;
			
		// Else just return the title.
		} else return $headline;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function get_images_where($where=array(), $limit='', $offset='') {
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				//$this->db->where('category', $where['category']);
				//$this->db->or_where('subcat', $where['category']);
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Filter by date.
		if(!isset($where['datesubmitted'])) {
			$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
			$this->db->where('datesubmitted >= ', $time_ago);
		}
		
		// Get story from database.
		$this->db->select('headline');
		$this->db->select('headline_txt');
		$this->db->select('category');
		$this->db->select('subcat');
		$this->db->select('datesubmitted');
		$this->db->from('stories');
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('view', '1');
		//$this->db->where('s.id NOT IN (SELECT sc.story_id FROM story_chain sc)');
		$this->db->order_by('id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_perspective($where=array(), $limit='', $offset=''){
	
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				//$this->db->where('category', $where['category']);
				//$this->db->or_where('subcat', $where['category']);
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Filter by date.
		if(!isset($where['datesubmitted'])) {
			$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
			$this->db->where('datesubmitted >= ', $time_ago);
		}
	
		
		// Get story from database.
		$this->db->select('s.*');
		$this->db->select('u.username');
		$this->db->select('(SELECT COUNT(sv.id) FROM story_views sv WHERE sv.story_id = s.id) AS num_views');
		$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = s.id AND c.view = 1) AS num_comments');
		$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 1 AND v.type_id = s.id AND v.value >= 1) AS popularity');
		$this->db->from('stories s');
		$this->db->join('users u', 's.user_id = u.id');
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('s.view', '1');
		//$this->db->where('s.id NOT IN (SELECT sc.story_id FROM story_chain sc)');
		$this->db->group_by('s.id');
		$this->db->order_by('s.id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function get_score($story_id) {
		$this->db->select('score');
		$query = $this->db->get_where('story_score', array('story_id' => $story_id));
		
		if($query->num_rows() > 0){
			$row = $query->row();
			return $row->score;
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_stories_in_chain($chain_id){
		$this->db->select('s.*');
		$this->db->select('u.username');
		$this->db->select('(SELECT COUNT(sv.id) FROM story_views sv WHERE sv.story_id = s.id) AS num_views');
		$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = s.id AND c.view = 1) AS num_comments');
		$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 1 AND v.type_id = s.id AND v.value >= 1) AS popularity');
		$this->db->from('stories s');
		$this->db->join('story_chain sc', 's.id = sc.story_id');
		$this->db->join('users u', 'u.id = s.user_id');
		$this->db->where('sc.chain_id', $chain_id);
		$this->db->where('s.view', '1');
		
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_trash_where($where=array(), $limit='', $offset='') {
		$this->db->select('t.*');
		$this->db->select('(SELECT u.username FROM users u WHERE u.id = t.user_id) AS reporter_username');
		$this->db->select('(SELECT headline_txt FROM stories s WHERE s.id = t.story_id) AS headline_txt');
		$this->db->select('(SELECT headline FROM stories s WHERE s.id = t.story_id) AS headline');
		$this->db->select('(SELECT category FROM stories s WHERE s.id = t.story_id) AS category');
		$this->db->select('(SELECT subcat FROM stories s WHERE s.id = t.story_id) AS subcat');
		$this->db->select('(SELECT datesubmitted FROM stories s WHERE s.id = t.story_id) AS datesubmitted');
		$this->db->select('(SELECT what FROM stories s WHERE s.id = t.story_id) AS what');
		$this->db->select('(SELECT `where` FROM stories s WHERE s.id = t.story_id) AS `where`');
		$this->db->select('(SELECT `view` FROM stories s WHERE s.id = t.story_id) AS story_view');
		$this->db->select('(SELECT user_id FROM stories s WHERE s.id = t.story_id) AS submitter_id');
		$this->db->select('(SELECT u.username FROM users u, stories s WHERE u.id = s.user_id AND s.id = t.story_id) AS submitter_username');
		$this->db->select('(SELECT u.status FROM users u, stories s WHERE u.id = s.user_id AND s.id = t.story_id) AS submitter_status');
		$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = t.story_id AND c.view = 1) AS num_comments');
		$this->db->select('(SELECT comment FROM comments c WHERE c.story_id = t.story_id AND c.id = t.comment_id) AS comment');
		$this->db->select('(SELECT dateposted FROM comments c WHERE c.story_id = t.story_id AND c.id = t.comment_id) AS comment_posted');
		$this->db->select('(SELECT `view` FROM comments c WHERE c.story_id = t.story_id AND c.id = t.comment_id) AS comment_view');
		$this->db->select('(SELECT u.username FROM users u WHERE u.id = (SELECT user_id FROM comments c WHERE c.id = t.comment_id)) AS commenter_username');
		$this->db->from('trash t');
		$this->db->where($where);
		$this->db->group_by('t.id');
		$this->db->order_by('t.id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_sitemap_stories($where=array(), $limit='', $offset=''){
	
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Filter by date.
		if(!isset($where['datesubmitted'])) {
			$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
			$this->db->where('datesubmitted >= ', $time_ago);
		}
		
		// Get story from database.
		$this->db->select('s.*');
		$this->db->select('a.activity_time');
		$this->db->from('stories s');
		$this->db->join('activity a', 'a.story_id = s.id');
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('s.view', '1');
		$this->db->group_by('s.id');
		$this->db->order_by('s.id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_story_votes_where($where=array()) {
	
		$user_id = $this->session->userdata($this->config->item('session_key').'_usr');
		
		if(empty($user_id)) {
			return array();
		} else {
			// For filtering stories by category.
			if(isset($where['category'])) {
				if($where['category'] == 'all'){
					unset($where['category']);
				} else {
					$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
					unset($where['category']);
				}
			}
			
			// Filter by date.
			if(!isset($where['datesubmitted'])) {
				$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
				$this->db->where('datesubmitted >= ', $time_ago);
			}
			
			// Get story from database.
			$this->db->select('v.*');
			$this->db->from('votes v');
			$this->db->join('stories s', 's.id = v.type_id');
			$this->db->where('v.type', '1');
			$this->db->where('v.user_id', $user_id);
			$this->db->where($where);
			if(!isset($where['view']))
				$this->db->where('s.view', '1');
			$this->db->group_by('s.id');
			$query = $this->db->get();
			
			if($query->num_rows() > 0) {
				// Put votes into array.
				$votes = $query->result_array();
				// Create return array.
				$arr = array();
				foreach($votes as $vote) {
					$arr[$vote['type_id']] = $vote['value'];
				}
				
				return $arr;
				
			} else return array();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_videos_where($where=array(), $limit='', $offset='') {
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Filter by date.
		if(!isset($where['datesubmitted'])) {
			$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
			$this->db->where('datesubmitted >= ', $time_ago);
		}
		
		// Get story from database.
		$this->db->from('stories');
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('view', '1');
		$this->db->where("(what LIKE '%youtube.com/watch?v=%' OR what LIKE '%video.google.com/videoplay?docid=%' OR what LIKE '%vimeo.com/%' OR what LIKE '%www.facebook.com/%video/video.php?v=%')");
		//$this->db->where('s.id NOT IN (SELECT sc.story_id FROM story_chain sc)');
		$this->db->order_by('id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		$result = $query->result_array();
		
		$videos = array();
		
		foreach($result as $key => $row) {
			preg_match_all('#(http://((www\.)?youtube.com/watch|video.google.com\/videoplay|(www\.)?vimeo.com\/|www.facebook.com/(.*?)video/video.php)[\w\#$&+,\/:;=?@.-]+)[^\w\#$&+,\/:;=?@.-]*?#i', $row['what'], $matches);
			
			if( isset( $matches[ 1 ] ) ) {
				$result[ $key ]['videos'] = $matches[ 1 ];
			}
		}
		
		return $result;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_where($where=array(), $limit='', $offset=''){
	
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				//$this->db->where('category', $where['category']);
				//$this->db->or_where('subcat', $where['category']);
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Filter by date.
		if(!isset($where['datesubmitted'])) {
			$time_ago = time() - (60 * 60 * 24 * intval($this->config->item('max_list_days')));
			$this->db->where('datesubmitted >= ', $time_ago);
		}
	
		
		// Get story from database.
		$this->db->select('s.*');
		$this->db->select('u.username');
		$this->db->select('(SELECT COUNT(sv.id) FROM story_views sv WHERE sv.story_id = s.id) AS num_views');
		$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = s.id AND c.view = 1) AS num_comments');
		$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 1 AND v.type_id = s.id AND v.value >= 1) AS popularity');
		$this->db->from('stories s');
		$this->db->join('users u', 's.user_id = u.id');
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('s.view', '1');
		$this->db->group_by('s.id');
		$this->db->order_by('s.id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_popularity($type, $type_id) {
	
		// Get sum of Votes for. (not against) 
		$this->db->select_sum('value');
		
		// Type 1 = 'story'.
		$this->db->where('type', $this->get_vote_type($type));
		$this->db->where('type_id', $type_id);
		
		if($type == 'story')
			$this->db->where('value >=', '1');
			
		$query = $this->db->get('votes');
		
		if($query->num_rows() > 0) {
			// Get row.
			$row = $query->row();
			
			// Include vote of submitter.
			return ( intval($row->value) + 1 );
		
		// Person who posted the story voted.	
		} else return 1;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function get_report($story_id, $comment_id, $user_id) {
		
		// Find out if the record exists already.
		$this->db->where('story_id', $story_id);
		$this->db->where('comment_id', $comment_id);
		$this->db->where('user_id', $user_id);
		$query = $this->db->get_where('abuse');
		
		// If record exists, return it.
		if($query->num_rows() > 0) 
			return $query->row_array();
		else return false;
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_top_in($where=array(), $limit='', $offset=''){
		// For filtering stories by category.
		if(isset($where['category'])) {
			if($where['category'] == 'all'){
				unset($where['category']);
			} else {
				$this->db->where("(category = '".$where['category']."' OR subcat = '".$where['category']."')");
				unset($where['category']);
			}
		}
		
		// Get story from database.
		$this->db->select('s.*');
		$this->db->select('u.username');
		$this->db->select('(SELECT COUNT(sv.id) FROM story_views sv WHERE sv.story_id = s.id) AS num_views');
		$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = s.id AND c.view = 1) AS num_comments');
		$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 1 AND v.type_id = s.id AND v.value >= 1) AS popularity');
		$this->db->from('stories s');
		$this->db->join('users u', 's.user_id = u.id');
		$this->db->where($where);
		if(!isset($where['view']))
			$this->db->where('s.view', '1');
		$this->db->group_by('s.id');
		$this->db->order_by('popularity', 'desc');
		$this->db->order_by('datesubmitted', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();
		
		return $query->result_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_vote($type, $type_id, $user_id) {
		
		// Find out if the submitter is voting. This is not allowed.
		switch($type) {
			case 'story':
				$this->db->where('user_id', $user_id);
				$this->db->where('id', $type_id);
				$vote = $this->db->get('stories');
				break;
			case 'comment':
				$this->db->where('user_id', $user_id);
				$this->db->where('id', $type_id);
				$vote = $this->db->get('comments');
				break;
		}
	
		if($vote->num_rows() > 0) {
			return array('submitted' => true, 'value' => '1');
		} else {
		
			// Find out if the record exists already.
			$this->db->where('type', $this->get_vote_type($type));
			$this->db->where('type_id', $type_id);
			$this->db->where('user_id', $user_id);
			$query = $this->db->get_where('votes');
			
			// If record exists, return it.
			if($query->num_rows() > 0) 
				return $query->row_array();
			else return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_vote_count($story_id){
		
		$this->db->select_sum('value');
		$this->db->where('type', '1');
		$this->db->where('type_id', $story_id);
		$query = $this->db->get('votes');
		
		if($query->num_rows() > 0) {
			
			$row = $query->row();
			
			return $row->value;
			
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function get_vote_type($str){
		// Type 1=story, 2=comment
		switch($str) {
			case 'story'  : return 1; break;
			case 'comment': return 2; break;
			default: return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function ignore_report($id) {
		// Set the view to 0 but don't actually delete the article. Might come in handy.
		$set = array(
					'ignore' => '1'
			   );
			   
		$this->db->where('id', $id);
		$this->db->update('abuse', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function info($headline) {
		if(empty($headline)){
			return false;
		} else {
			// Query database for article.
			$this->db->select('s.*');
			$this->db->select('u.username');
			$this->db->select('(SELECT COUNT(sv.id) FROM story_views sv WHERE sv.story_id = s.id) AS num_views');
			$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = s.id AND c.view = 1) AS num_comments');
			$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 1 AND v.type_id = s.id AND v.value >= 1) AS popularity');
			$this->db->from('stories s');
			$this->db->join('users u', 's.user_id = u.id');
			$this->db->where('s.headline', $headline);
			//$this->db->where('s.view', '1');
			$query = $this->db->get('stories');
			
			// if article exists.
			if($query->num_rows() > 0){
				
				// Return article info.
				return $query->row_array();
				
			// Else retrun false;
			} else return false;
		}
	}
	
	
	
	
	
	
	
	
	
	function info_from_id($story_id) {
		// Query database for article.
		$this->db->select('s.*');
		$this->db->select('u.username');
		$this->db->select('(SELECT COUNT(sv.id) FROM story_views sv WHERE sv.story_id = s.id) AS num_views');
		$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = s.id AND c.view = 1) AS num_comments');
		$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 1 AND v.type_id = s.id AND v.value >= 1) AS popularity');
		$this->db->from('stories s');
		$this->db->join('users u', 's.user_id = u.id');
		$this->db->where('s.id', $story_id);
		//$this->db->where('s.view', '1');
		$query = $this->db->get('stories');
		
		// if article exists.
		if($query->num_rows() > 0){
			
			// Return article info.
			return $query->row_array();
			
		// Else retrun false;
		} else return false;
	}
	
	
	
	
	
	
	
	function is_author($user_id, $type, $type_id) {
		// If there's no user_id, just forget it.
		if(empty($user_id)){
			return false;
		} else {
			$table = 'stories';
			if($type == 'comment')
				$table = 'comments';
			
			// Get favorites record from db.
			$this->db->where(array('id' => $type_id, 'user_id' => $user_id));
			$query = $this->db->get($table);
			
			// If no rows in the favorites table, then it's not a favorite.
			return ($query->num_rows() == 0) ? false : true;
		}
	}
	
	
	
	
	
	
	
	
	function is_favorite($story_id, $user_id='no_user_id') {
		// If there's no user_id, just forget it.
		if(empty($user_id)){
			return false;
		} else {
			// Determine which user_id to use.
			$real_user_id = ($user_id == 'no_user_id') ? $this->session->userdata($this->config->item('session_key').'_usr') : $user_id;
			
			// Get favorites record from db.
			$this->db->where(array('story_id' => $story_id, 'user_id' => $real_user_id));
			$query = $this->db->get('favorites');
			
			// If no rows in the favorites table, then it's not a favorite.
			return ($query->num_rows() == 0) ? false : true;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	function is_reported($story_id, $comment_id='', $user_id='no_user_id') {
		// If there's no user_id, just forget it.
		if(empty($user_id)){
			return false;
		} else {
			// Determine which user_id to use.
			$real_user_id = ($user_id == 'no_user_id') ? $this->session->userdata($this->config->item('session_key').'_usr') : $user_id;
			
			// Get favorites record from db.
			$this->db->where(array('story_id' => $story_id, 'comment_id' => $comment_id, 'user_id' => $real_user_id));
			$query = $this->db->get('abuse');
			
			// If no rows in the favorites table, then it's not a favorite.
			return ($query->num_rows() == 0) ? false : true;
		}
	}
	
	
	
	
	
	
	
	
	
	
	function map_old_url($story_id, $headline) {
		// Check if the record exists.
		$this->db->where('story_id', $story_id);
		$this->db->where('headline', $headline);
		$query = $this->db->get('url_map');
		
		// of not, create it.
		if($query->num_rows() == 0){
			$data = array(
						'story_id' => $story_id,
						'headline' => $headline
					);
			
			// Insert url map.
			$this->db->insert('url_map', $data);
		}
	}
	
	
	
	
	
	
	
	
	
	
	function promote($story_id) {
		// Prepare data for insertion.
		$set = array(
					'view' => 1,
					'popular' => time()
				);
				
		// update data.
		$this->db->where('id', $story_id);
		$this->db->update('stories', $set);
	}
	
	
	
	
	
	function demote($story_id) {
		// Prepare data for insertion.
		$set = array(
					'view' => 0,
					'popular' => ''
				);
				
		// update data.
		$this->db->where('id', $story_id);
		$this->db->update('stories', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function register_report($story_id, $comment_id, $user_id) {
	
		// Prep data for insertion.
		$data = array(
					// Type: 1=story, 2=comment
					'story_id' 			=> $story_id,
					'comment_id' 		=> $comment_id,
					'user_id' 			=> $user_id,
					'date_registered' 	=> time(),
					'ip'				=> $_SERVER['REMOTE_ADDR'],
					'user_agent'		=> $_SERVER['HTTP_USER_AGENT']
				);
				
		// Insert data.
		$this->db->insert('abuse', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	function register_vote($type, $type_id, $user_id, $value) {
	
		// Prep data for insertion.
		$data = array(
					// Type: 1=story, 2=comment
					'type' 				=> $this->get_vote_type($type),
					'type_id' 			=> $type_id,
					'user_id' 			=> $user_id,
					'value' 			=> $value,
					'date_registered' 	=> time(),
					'ip'				=> $_SERVER['REMOTE_ADDR'],
					'user_agent'		=> $_SERVER['HTTP_USER_AGENT']
				);
				
		// Insert data.
		$this->db->insert('votes', $data);
	}
	
	
	
	
	function count_search_results($tokens=array()){
		if(count((array) $tokens) > 0 && !empty($tokens)){
			$this->db->where('view', '1');
			$search_where = "";
			foreach($tokens as $key => $token)
				$search_where .= " OR headline_txt LIKE '%".$this->db->escape_str($token)."%' OR what LIKE '%".$this->db->escape_str($token)."%'";
			
			$search_where = "(" . substr($search_where, 4) . ")";
			$this->db->where($search_where);
			
			return $this->db->count_all_results('stories');
		} else {
			return 0;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function save_score($story_id, $score){
		$query = $this->db->get_where('story_score', array('story_id' => $story_id));
		
		if($query->num_rows() == 0) {
			// Prepare data for insertion.
			$data = array(
						'story_id'	=> $story_id,
						'score'		=> $score
					);
					
			// Insert data.
			$this->db->insert('story_score', $data);
		} else {
		
			// update score in story_score.
			$set = array(
						'score' => $score
					);
					
			// update data.
			$this->db->where('story_id', $story_id);
			$this->db->update('story_score', $set);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function search_results($tokens=array(), $limit='', $offset='') {
		if(count((array) $tokens) > 0 && !empty($tokens)){
			// Get story from database.
			$this->db->select('s.*');
			$this->db->select('u.username');
			$this->db->select('(SELECT COUNT(c.id) FROM comments c WHERE c.story_id = s.id AND c.view = 1) AS num_comments');
			$this->db->select('(SELECT SUM(v.value)+1 FROM votes v WHERE v.type = 1 AND v.type_id = s.id AND v.value >= 1) AS popularity');
			$this->db->from('stories s');
			$this->db->join('users u', 's.user_id = u.id');
			$this->db->where('s.view', '1');
			
			$search_where = "";
			foreach($tokens as $key => $token)
				$search_where .= " OR s.headline_txt LIKE '%".$this->db->escape_str($token)."%' OR s.what LIKE '%".$this->db->escape_str($token)."%'";
			
			$search_where = "(" . substr($search_where, 4) . ")";
			$this->db->where($search_where);
			
			$this->db->group_by('s.id');
			$this->db->order_by('s.id', 'desc');
			$this->db->limit($limit, $offset);
			$query = $this->db->get();
			
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function too_soon() {
		
		// Get all stories submitted by the user within the last 3 minutes.
		$query = $this->db->get_where('stories', array('datesubmitted >' => (time() - 60 * 3), 'user_id' => $this->session->userdata($this->config->item('session_key').'_usr')));
		
		return ($query->num_rows() == 0) ? false : true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function trash_info($id){
		// Delete from the abuse page.
		$query = $this->db->get_where('trash', array('id' =>  $id));
		if($query->num_rows > 0){
			return $query->row_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function undelete($id, $status, $story_id, $comment_id='') {
		// Set the view to 2 but don't actually delete the article. Might come in handy.
		$set = array(
					'view' => $status
			   );
		
		if(empty($comment_id) || $comment_id == '0'){	   
			$this->db->where('id', $story_id);
			$this->db->update('stories', $set);
		} else {
			$this->db->where('id', $comment_id);
			$this->db->update('comments', $set);
		}
				
		// Delete row from Trash table.
		$this->db->where('id', $id);
		$this->db->delete('trash');
		
		// Optimize the table.
		$this->load->dbutil();
		$this->dbutil->optimize_table('trash');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function update($story_id, $headline_url) {
		
		// Format headline and Location.
		$headline = format_headline($this->util->clean($_POST['headline']));
		$location = format_headline($this->util->clean($_POST['where']));
		
		// Separate tags into cateogry and sub-category.
		$tags = explode('-', $this->util->clean($_POST['tags']));
		
		// Update url if exists.
		$url = isset($_POST['url']) ? $this->util->clean($_POST['url']) : '';
		
		// Replace the default caption text.
		$what = normalize_text($_POST['what']);
				
		// Prepare data for insertion.
		$set = array(
					'headline' 		=> $headline_url,
					'headline_txt' 	=> $headline,
					'url'			=> $url,
					'what' 			=> $this->util->clean($what, '<image>'),
					'category'		=> $tags[0],
					'subcat'		=> $tags[1],
					'where'			=> $location,
					'posx'			=> $this->util->clean($_POST['posx']),
					'posy'			=> $this->util->clean($_POST['posy'])
				);
				
		// update data.
		$this->db->where('id', $story_id);
		$this->db->update('stories', $set);
		
		// Assess the story.
		$this->assess($story_id);
	}
	
	
	
	
	
	
	
	
	
	
	
	function update_comment($comment_id, $comment){
		$set = array(
					'comment' 		=> $this->util->clean($comment)
				);
				
		// update data.
		$this->db->where('id', $comment_id);
		$this->db->update('comments', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	function update_vote($type, $type_id, $user_id, $value) {
	
		// Prep data for insertion.
		$data = array(
					'value' 			=> $value,
					'date_registered'	=> time()
				);
		
		// Set Where clause.
		$this->db->where( array(
					// Type: 1=story, 2=comment
					'type' 				=> $this->get_vote_type($type),
					'type_id' 			=> $type_id,
					'user_id' 			=> $user_id
				) 
		);
		
		// Insert data.
		$this->db->update('votes', $data);
	}
	
	
	
	
	
	
	
	
	function user_has_demoted($user_id, $story_id) {
		// If there's no user_id, just forget it.
		if(empty($user_id) || empty($story_id)){
			return false;
		} else {
			
			// Get favorites record from db.
			$this->db->where(array('type' => '1', 'type_id' => $story_id, 'user_id' => $user_id, 'value' => '-1'));
			$query = $this->db->get('votes');
			
			// If no rows in the favorites table, then it's not a favorite.
			return ($query->num_rows() == 0) ? false : true;
		}
	}
}
?>