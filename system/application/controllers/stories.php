<?php

class Stories extends Controller {
	
	
	var $user_info		= array();
	var $url_info		= array();
	
	
	
	function Stories() {
		parent::Controller();
		
		// Load story model.
		$this->load->model('story');
		
		// Load user model for user related activities.
		$this->load->model('user');
		
		// Load the util Library.
		$this->load->library('util');
		
		// Load Messages Library to pass session messages when required.
		$this->load->library('messages');
		
		// Load the image manipulation library.
		$this->load->library('image_lib');
		
		// Load the image manipulation library.
		$this->load->library('errors');
		
		// Load the Pagination Library.
		$this->load->library('pagination');
		
		// Load the image helper.
		$this->load->helper('image');
		
		// Load the image helper.
		$this->load->helper('text');
		
		// Load the language for Categories
		$this->lang->load('category', 'english');
		
		// Redirect to proper domain.
		$this->util->domain_redir();
		
		
		
		// Commonly used variables.
		
		// User Info
		$this->user_info = array(
								 'id' 					=> $this->session->userdata($this->config->item('session_key').'_usr'),
								 'user_is_admin' 		=> $this->util->user_is_admin(),
								 'user_is_logged_in' 	=> $this->user->is_logged_in()
								 );
		
		
		// Define segments
		$seg1 = $this->uri->segment(1);
		$seg2 = $this->uri->segment(2);
		$seg3 = $this->uri->segment(3);
		$seg4 = $this->uri->segment(4);
		
		$category = ($seg1 == 'perspective' || $seg1 == 'images') ? 'all' : $seg1;
		$time_period = ($seg3 == 'perspective' || $seg3 == 'images' || substr($seg3, 0, 4) == 'page' || $seg3 == '' || strlen($seg3) == 2 ) ? '' : '/'.$seg3;
		$data_view = '';
		if($seg1 == 'perspective' || $seg2 == 'perspective' || $seg3 == 'perspective' || $seg4 == 'perspective') {
			$data_view = '/perspective';
		} elseif($seg1 == 'images' || $seg2 == 'images' || $seg3 == 'images' || $seg4 == 'images') {
			$data_view = '/images';
		} elseif($seg1 == 'videos' || $seg2 == 'videos' || $seg3 == 'videos' || $seg4 == 'videos') {
			$data_view = '/videos';
		}
		
		// URL Info. To help with menu, etc.
		$this->url_info = array(
							'upcoming' 				=> ($seg2 == 'upcoming') ? '/upcoming' : '',
							'category' 				=> $category,
							'news_type' 			=> ($seg2 == 'upcoming') ? '/upcoming' : '/popular',
							'news_type_link' 		=> (empty($category) || $seg1 == 'all' || substr($seg1, 0, 4) == 'page') ? '/all' : '/'.$category,
							'time_period' 			=> $time_period,
							'perspective_news_type' => ($seg2 == 'upcoming') ? '/upcoming' : (empty($time_period) ? '' : '/popular'),
							'data_view' 			=> $data_view
							);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function _remap($method) {
	
		if(substr($method, 0, 4) == 'page'){
			$this->index();
		} else {
			$this->$method();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function index() {

		$config['base_url'] = '/';
		$config['total_rows'] = $this->story->count_where();
		$config['per_page'] = $this->config->item('num_stories_per_page');
		$config['uri_segment'] = '1';
		
		$this->pagination->initialize($config);
	
		// Prepare article list for display.
		$data = array(
			'stories' 			=> $this->story->get_where(array(), $this->pagination->limit(), $this->pagination->offset()),
			'votes'				=> $this->story->get_story_votes_where(),
			'title'	 			=> 'All News',
			'empty_message'		=> "No-one's posted anything yet, but feel free to start things up.",
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
		
		// Show article list.
		$this->load->view('stories/list', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function article() {
	
		// Get category for paging.
		$category = $this->uri->segment(1);
		
		// Get date from URL.
		$year 	= $this->uri->segment(2);
		$month 	= $this->uri->segment(3);
		$day 	= $this->uri->segment(4);
	
		// Get the article
		$headline = $this->uri->segment(5);
		
		// Check if the story is has been edited.
		$old_story_id = $this->story->check_old_url($headline);
		
		// If no article specified.
		if(empty($headline)) {
			// Show 404 error.
			//show_404();
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
			
		} else if($old_story_id !== false) {
		
			// Check for old url.
			$old_story = $this->story->info_from_id($old_story_id);
			$old_category = ($old_story['subcat'] == 'all') ? $old_story['category'] : $old_story['subcat'];
			
			$old_year 		= date('Y', $old_story['datesubmitted']);
			$old_month 		= date('m', $old_story['datesubmitted']);
			$old_day 		= date('d', $old_story['datesubmitted']);
			
			redirect('/'.$old_category.'/'.$old_year.'/'.$old_month.'/'.$old_day.'/'.$old_story['headline'], 'location', 301);
		} else {
			
			// Check if article exists.
			$story = $this->story->info($headline);
			
			// If article does not exist.
			if($story === false){
				// Show 404 error.
				
				$this->errors->set("The page you are looking for is not here.");
				redirect('error');
			
					// Check if the category is correct.
			} else if(($story['subcat'] != $category && $story['subcat'] != 'all') || 
						($story['category'] != $category && $story['subcat'] == 'all') ||
						(date('Y', $story['datesubmitted']) != $year) ||
						(date('m', $story['datesubmitted']) != $month) ||
						(date('d', $story['datesubmitted']) != $day)){
				
				// Get what the category should be.
				$real_category = ($story['subcat'] == 'all') ? $story['category'] : $story['subcat'];
				
				// Get real date.
				$real_year 		= date('Y', $story['datesubmitted']);
				$real_month 	= date('m', $story['datesubmitted']);
				$real_day 		= date('d', $story['datesubmitted']);
				
				// Redirect user to the correct story.
				redirect($real_category.'/'.$real_year.'/'.$real_month.'/'.$real_day.'/'.$headline);
				
			
					// Check if the story has been approved.
			} else if($story['view'] == '2' && ! $this->util->user_is_admin()) {
				// Story has been deleted.
				$this->errors->set('This story has been deleted.');
				redirect('error');
				
					// Else show the article.
			} else {
					
				$config['base_url'] = '/'.$category.'/'.$year.'/'.$month.'/'.$day.'/'.$headline;
				$config['total_rows'] = $this->story->count_comments_where(array('story_id' => $story['id']));
				$config['per_page'] = '50';
				$config['uri_segment'] = '6';
				
				$this->pagination->initialize($config);
			
				// Register a view on the story.
				$this->story->click($story['id']);
				
				// Get Vote.
				$vote = $this->story->get_vote('story', $story['id'], $this->session->userdata($this->config->item('session_key').'_usr'));
				
				// Prepare to display the page.
				$data = array(
					'session_username' 	=> $this->session->userdata($this->config->item('session_key').'_unm'),
					'story' 			=> $story,
					'user_vote'			=> isset($vote['value']) ? $vote['value'] : '0',
					'comments' 			=> $this->story->get_comments_where(array('story_id' => $story['id'], 'reply_to' => '0'), $this->pagination->limit(), $this->pagination->offset()),
					'comment_votes'		=> $this->story->get_comment_votes_where(array('story_id' => $story['id']), $this->pagination->limit(), $this->pagination->offset()),
					'images'			=> '',
					'category'			=> ($story['subcat'] == 'all') ? $story['category'] : $story['subcat'],
					'year' 				=> date('Y', $story['datesubmitted']),
					'month' 			=> date('m', $story['datesubmitted']),
					'day'				=> date('d', $story['datesubmitted']),
					'user_info'			=> $this->user_info,
					'url_info'			=> $this->url_info
				);
				
				// Load the page with details.
				$this->load->view('stories/'.(empty($story['url']) ? 'article' : 'submission'), $data);
				
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function check_url($field, $param='') {
		
		// Get domain from url. 
		$domain = parse_url($field, PHP_URL_HOST);
		
		switch($domain) {
			case 'www.trinidadexpress.com':
				// Check for valid article url.
				// http://www.trinidadexpress.com/index.pl/nart?id=161601961
				$this->form_validation->set_message('check_url', 'URL should lead directly to the story.');
				return (preg_match('#http://(www\.)?trinidadexpress.com/index.pl/nart\?id=([0-9]*)$#', $field) == 0) ? false : true;
				break;
			case 'guardian.co.tt':
				// Check for valid article url.
				// http://guardian.co.tt/features/entertainment/2010/02/05/living-life
				// http://guardian.co.tt/commentary/cartoon/bury-remains
				// http://guardian.co.tt/business/business/2010/03/02/tesheira-urges-caricom-unity-global-finance
				$this->form_validation->set_message('check_url', 'URL should lead directly to the story.');
				return (preg_match('#http://guardian.co.tt/(.*)?/(.*)?/(.*)$#', $field, $matches) == 0 || strtolower(substr($matches[1], 0, 5)) == 'files') ? false : true;
				break;
			case 'newsday.co.tt':
			case 'www.newsday.co.tt':
				// Check for valid article url.
				// http://www.newsday.co.tt/news/0,116593.html
				// http://www.newsday.co.tt/sport/0,116612.html
				// http://www.newsday.co.tt/crime_and_court/0,116589.html
				$this->form_validation->set_message('check_url', 'URL should lead directly to the story.');
				return (preg_match('#http://(www\.)?newsday.co.tt/(.*)?/([0-9]),([0-9]*)?.html$#', $field) == 0) ? false : true;
				break;
			default:
				// Set the failure message if required.
				$this->form_validation->set_message('check_url', 'The domain "'.$domain.'" is not supported at is time.');
				return false;
				break;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function comment () {
		// Determine if comment is a reply.
		$is_reply = empty($_POST['rt']) ? false : true;
		$reply_to = $_POST['rt'];
		
		// Get user_id from session.
		$user_id 	= $this->user_info['id'];
		// Get story_id
		$story_id = $_POST['si'];
	
		// Check user is logged in.
		if(!$this->user->is_logged_in()) {
			
			echo '{ id: "", html: "", message: "<div class=\"error\">Your session has expired, please login and try again.</div>" }';
		
		} elseif($this->user->is_restricted($user_id)){
			
			echo '{ id: "", html: "", message: "<div class=\"error\">You have been banned, you are not allowed to post comments.</div>" }';
			
		} else {
					
			// Set rules for validation
			$this->form_validation->set_rules('comment', 	'Message', 	'trim|required|not_undefined|xss_clean');
			
			// Set error delimiters
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			// Run validation rules.
			if($this->form_validation->run() == false){
				// Validation failed.
				echo '{ id: "", html: "", message: "'.addslashes(form_error('comment')).'" }';
				
			} else {
				// Validation passed. Process form.
				
				// Save comment to database.
				$comment_id = $this->story->add_comment();
				// Get comment info.
				$comment = $this->story->comment_info($comment_id);
				
				// Variable for checking reply author.
				$comment_author_is_story_author = false;
				
				// Send email to story author (if applicable and comment author NOT story author).
				$story_author = $this->user->get_applicable_author_info('story', $story_id);
				
				// Send email to Comment Author (IF REPLY).
				if($is_reply) {
					
					// Send email to comment author (if applicable).
					$comment_author = $this->user->get_applicable_author_info('comment', $reply_to);
					
					if(count((array) $comment_author) > 0) {
						// If you are not the comment author. send email.
						if($comment_author['user_id'] != $user_id) {
							// Prepare to display story URL.
							$category 	= ($story_author['subcat'] == 'all') ? $comment_author['category'] : $comment_author['subcat']; 
							$year 		= date('Y', $story_author['datesubmitted']);
							$month 		= date('m', $story_author['datesubmitted']);
							$day 		= date('d', $story_author['datesubmitted']);
						
							// Send email to user.
							$this->load->library('email');
							$this->email->clear();
							$this->email->from($this->config->item('system_email'), $this->config->item('title'));
							$this->email->to($comment_author['email']);
							$this->email->subject('Reply from '.$comment['username'].' on your comment in '.$comment_author['headline_txt']);
							$this->email->message(
													"Hi there,\r\n\r\n".
													"\"".$comment['username']."\" replied to your comment in ".$comment_author['headline_txt'].":\r\n\r\n".
													"--------------------------------\r\n".
													$comment['comment']."\r\n--------------------------------\r\n\r\n".
													"View story here: http://".$_SERVER['SERVER_NAME']."/".$category."/".$year."/".$month."/".$day."/".$comment_author['headline']."\r\n\r\n\r\n".
													"If you no longer wish to receive these emails, change your preferences here: http://".$_SERVER['SERVER_NAME']."/settings/email\r\n\r\n".
													"Thank you,\r\n\r\n".
													$this->config->item('title')."\n".
													$this->config->item('base_url')
												  );
							$this->email->send();
						}
						
						// If reply author is story author, only send one email.
						if($comment_author['user_id'] == $story_author['user_id'])
							$comment_author_is_story_author = true;
					}
				}
				
				// Send email to Story author.
				if(count((array) $story_author) > 0) {
					// Don't notify yourself that you've posted a comment. Doesn't make any sense.
					if($story_author['user_id'] != $user_id && $comment_author_is_story_author == false) {
					
						// Prepare to display story URL.
						$category 	= ($story_author['subcat'] == 'all') ? $story_author['category'] : $story_author['subcat']; 
						$year 		= date('Y', $story_author['datesubmitted']);
						$month 		= date('m', $story_author['datesubmitted']);
						$day 		= date('d', $story_author['datesubmitted']);
					
						// Send email to user.
						$this->load->library('email');
						$this->email->clear();
						$this->email->from($this->config->item('system_email'), $this->config->item('title'));
						$this->email->to($story_author['email']);
						$this->email->subject('Comment from '.$comment['username'].' on '.$story_author['headline_txt']);
						$this->email->message(
												"Hi there,\r\n\r\n".
												"\"".$comment['username']."\" commented on your story (".$story_author['headline_txt']."):\r\n\r\n".
												"--------------------------------\r\n".
												$comment['comment']."\r\n--------------------------------\r\n\r\n".
												"View story here: http://".$_SERVER['SERVER_NAME']."/".$category."/".$year."/".$month."/".$day."/".$story_author['headline']."\r\n\r\n\r\n".
												"If you no longer wish to receive these emails, change your preferences here: http://".$_SERVER['SERVER_NAME']."/settings/email\r\n\r\n".
												"Thank you,\r\n\r\n".
												$this->config->item('title')."\n".
												$this->config->item('base_url')
											  );
						$this->email->send();
					}
				}
				
				// Display comment.
				$html = '<li id="comment_'.$comment['id'].'">'.
							'<div class="comment_container author">'.
								'<div class="com-vote">'.
									'<div id="comment-pop-'.$comment['id'].'">1</div>'.
									'<a id="promote_comment_'.$comment['id'].'" class="vote promoted" href="#">+</a> <a id="demote_comment_'.$comment['id'].'" class="vote" href="#">-</a>'.
								'</div>'.
								'<div><a href="/users/'.$comment['username'].'">'.$comment['username'].'</a> says:</div>'.
								'<div class="com-time">'.
									'<abbr class="timeago" title="'.date($this->config->item('date_format')).'"></abbr>'.
								'</div>'.
								'<div>'.
									'<div id="comment_detail_'.$comment['id'].'">'.display_comment($comment['comment']).'</div>'.
									'<div class="comment_options">'.
										//' <a id="del_comment_'.$comment['id'].'" class="del">Delete</a>';
										' <a id="edit_comment_'.$comment['id'].'" class="edit" href="#">Edit</a>';
										
									if($is_reply == false) {
										$html .= ' | <a id="r_'.$comment['id'].'" class="reply" href="#">Reply</a>';
									}
									
									$html .= '</div>'.
								'</div>'.
							'</div>'.
						'</li>';
				
				$html = str_replace("\n", "", $html);
				
				echo '{ id: "'.$comment_id.'", html: "'.addslashes($html).'", message: "" }';
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function compose(){
		// Check user is logged in.
		$this->util->check_user_logged_in();
		
		// Get username from session.
		$session_username = $this->session->userdata($this->config->item('session_key').'_unm');
		
		if($this->user->is_restricted($this->user_info['id'])) {
			$this->errors->set('Your account has been restricted, you are not allowed to post stories.');
			redirect('error');
		} else {
		
			// Set data for form.
			$data = array(
						'img_folder'	=> md5($session_username)
					);
					
			// Set rules for validation
			$this->form_validation->set_rules('headline', 	'headline', 	'trim|required|max_length[60]|xss_clean');
			$this->form_validation->set_rules('what', 		'what', 		'trim|required|xss_clean');
			$this->form_validation->set_rules('tags', 		'category',		'trim|required|min_length[3]|max_length[100]|xss_clean');
			$this->form_validation->set_rules('where', 		'where', 		'trim|min_length[3]|max_length[40]|xss_clean');
			$this->form_validation->set_rules('posx',	 	'position X', 	'');
			$this->form_validation->set_rules('posy',	 	'position Y', 	'');
			
			// Set error delimiters
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			// Run validation rules.
			if($this->form_validation->run() == false){
			
				// Show the submit form.
				$this->load->view('stories/compose', $data);
			} else {
				// Validation passed. Process form.
				
				// Check for story already submitted.
				if($this->story->already_submitted($_POST['headline'])){
					// User is not verified. Set message then redirect to home page.
					$this->messages->add("Story should be here already. Check in <a href=\"/all/upcoming\">More News</a>.");
				
					// redirect to home page.
					redirect();
					
				} elseif($this->story->too_soon()) {
					// User is not verified. Set message then redirect to home page.
					$this->messages->add("Hold up on submitting this story for a little while.");
					
					// redirect to home page.
					redirect();
				} else {
				
					// Save article to database. Return headline url for redirection.
					$headline_url = $this->story->create($followup);
					
					// Check to see if there are images associated with this story.
					$session_path = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$data['img_folder'];
					
					if(file_exists($session_path)) {
					
						// Delete images that aren't in the story.
					
						// Create a folder for files associated with this story can be stored.
						$abs_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$headline_url;
						$this->util->mkrdir($abs_path);
						
						// Move files uploaded (if any) to permanent folder.
						$this->util->move_files($session_path, $abs_path, false);
						
						// Delete the orignal folder.
						$this->util->delete_dir($session_path);
					}
					
					// Send an email to the administrator.
					$this->load->library('email');
					$this->email->clear();
					$this->email->from($this->config->item('system_email'), $this->config->item('title'));
					$this->email->to('price.aaron@gmail.com');
					$this->email->subject('New: '.$_POST['headline']);
					$this->email->message(
											"Hey there,\n\n".
											"Check out the story here:\n".
											"http://shou.tt/".$headline_url."\n\n".
											"Posted by: http://shou.tt/users/".$session_username."\n\n".
											"Thanks again,\n\n".
											$this->config->item('title')."\n".
											$this->config->item('base_url')
										  );
					$this->email->send();
					
					
					
					// This is the way it should work, unfortunately, the approval process is being used for now.
					// Redirect to article.
					redirect('/'.$headline_url);
				}
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function delete() {
	
		// Anything destructive should happen through post.
		// Hence we use AJAX
		
		// Declare vars we'll need. story_id and user_id (cuz we don't have access to the session).
		$del_string = $_POST['id'];
		$abuse = isset($_POST['abuse']) ? true : false;
		
		$del_arr = explode('_', $del_string);
		$id = $del_arr[2];
		
		// Get user_id from session.
		$user_id 	= $this->user_info['id'];
		
		$user = $this->user->info_from_column('id', $user_id);
		
		// Check user is logged in.
		if(!$this->user->is_logged_in()) {
			// User not logged in.
			echo "Sorry your session has expired, please login and try again.";
			
		} elseif(!$this->user->is_authorized($user['type']) && !$this->story->is_author($user_id, $del_arr[1], $id)){
			
			// User has no business here.
			echo "I just can't do it captain... I haven't got the power.";
			
		} else {
			// Get abuse info.
			$abuse_info = ($abuse == true) ? $this->story->abuse_info($id) : array();
			
			// Get info for database.
			$user_id 	= $this->session->userdata($this->config->item('session_key').'_usr');
			$story_id 	= ($abuse == true) ? $abuse_info['story_id'] : $id;
			$comment_id = '';
			
			// If it's a comment.
			if($del_arr[1] == 'comment') {
				// Set the comment id.
				$comment_id = ($abuse == true) ? $abuse_info['comment_id'] : $id;
				
				// Get the story id from comments table.
				$comment = $this->story->get_comment($comment_id);
				$story_id = $comment['story_id'];
			}
			
			// Delete the story or comment.
			$this->story->delete($user_id, $story_id, $comment_id);
			
			// Return message to the user.
			echo '';
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function delete_image() {
	
		// Anything destructive should happen through post.
		// Hence we use AJAX
		
		// Declare vars we'll need. story_id and user_id (cuz we don't have access to the session).
		$filename 	= $_POST['id'];
		$tmp_folder	= $_POST['path'];
		
		// Parse the id to create the real filename
		$real_filename = str_replace('_', '.', $filename);
		
		if(empty($tmp_folder) || empty($filename)){
			echo "Sorry, I just don't feel like it.";
		} else {
		
			// Delete the original and thumbnail.
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$tmp_folder.'/'.$real_filename)){
				@unlink($_SERVER['DOCUMENT_ROOT'].$tmp_folder.'/'.$real_filename);
				@unlink($_SERVER['DOCUMENT_ROOT'].$tmp_folder.'/thm/'.$real_filename);
			} else {
				echo "This file doesn't exist.";
			}
				
			// Return message as necessary.
			//echo "I just can't do it captain... I haven't got the power.";	
			echo '';
		}
	}
	
	
	
	
	
	
	
	
	
	function edit(){
		
		
		$headline = $this->uri->segment(2);
		
		if(!$_POST && empty($headline)){
			
			$this->errors->set('The page you are looking for is not here.');
			redirect('error');
		} else {
		
			if(empty($headline)) 
				$headline = $_POST['headline_url'];
			
			
			if($this->user->is_restricted($this->user_info['id'])) {
				$this->errors->set('Your account has been restricted, you are not allowed to edit stories.');
				redirect('error');
			} else {
			
				// Get story info.
				$story = $this->story->info($headline);
				
				// Check user is logged in.
				if($this->util->user_is_admin() || $this->story->is_author($this->user_info['id'], 'story', $story['id'])) {
				
					// Determine the type of the story.
					$story_type = empty($story['url']) ? 'post' : 'submission';
					
					// Set data for form.
					$data = array(
								'story'	=> $story
							);
							
					// Set rules for validation
					if($story_type == 'submission')	{
						$this->form_validation->set_rules('url', 'url',	'trim|required|max_length[255]|valid_url|callback_check_url|xss_clean');
						$this->form_validation->set_rules('what', 'what', 'trim|required|max_length[210]|xss_clean');
					} else {
						$this->form_validation->set_rules('what', 'what', 'trim|required|xss_clean');
					}
					$this->form_validation->set_rules('headline', 	'headline', 	'trim|required|max_length[60]|xss_clean');
					$this->form_validation->set_rules('tags', 		'tags', 		'trim|required|min_length[3]|max_length[100]|xss_clean');
					$this->form_validation->set_rules('where', 		'where', 		'trim|min_length[3]|max_length[40]|xss_clean');
					$this->form_validation->set_rules('posx',	 	'position x', 	'');
					$this->form_validation->set_rules('posy',	 	'position y', 	'');
					
					// Set error delimiters
					$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
					
					// Run validation rules.
					if($this->form_validation->run() == false){
					
						// Show the submit form.
						$this->load->view('stories/edit_'.$story_type, $data);
					} else {
						// Validation passed. Process form.
						
						// Separate tags into cateogry and sub-category.
						$tags 			= explode('-', $this->util->clean($_POST['tags']));
						$category 		= $tags[0];
						$subcat 		= $tags[1];
						$url_cat		= ($subcat == 'all') ? $category : $subcat;
						$db_cat			= ($story['subcat'] == 'all') ? $story['category'] : $story['subcat'];
						$headline_url 	= $story['headline'];
						$year 			= date('Y', $story['datesubmitted']);
						$month 			= date('m', $story['datesubmitted']);
						$day 			= date('d', $story['datesubmitted']);
						$formatted_headline = trim(strtolower(preg_replace("/[^a-zA-Z0-9]+/","_", html_entity_decode($_POST['headline']))), '_');
						// $year.'/'.$month.'/'.$day.'/'.		
						
						
						// if title or category changed, move files to current url.
						if($url_cat != $db_cat || $formatted_headline != $story['headline']) {
						
							// if title changed, create URL Map. (id, old_url)
							if($formatted_headline != $story['headline']) {
								
								$this->story->map_old_url($story['id'], $story['headline']);
								
								// Get headline url.
								$headline_url = $this->story->get_headline_url($this->util->clean($_POST['headline']));
							}
							
							// Define old dir.
							$old_dir = $_SERVER['DOCUMENT_ROOT'].'/img/'.$db_cat.'/'.$year.'/'.$month.'/'.$day.'/'.$story['headline'];
							
							if(file_exists($old_dir) && !empty($db_cat) && !empty($story['headline'])) {
							
								// Create folder if it doesn't exists.
								$abs_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$url_cat.'/'.$year.'/'.$month.'/'.$day.'/'.$headline_url;
								$this->util->mkrdir($abs_path);
								
								// Move files to current url.
								$this->util->move_files($old_dir, $abs_path, false);
								
								// Delete old folder.
								$this->util->delete_dir($old_dir);
							}
							
						}
						
						
						// Save article to database. Return headline url for redirection.
						$this->story->update($story['id'], $headline_url);
						
						
						// This is the way it should work, unfortunately, the approval process is being used for now.
						// Redirect to article.
						redirect('/'.$url_cat.'/'.$year.'/'.$month.'/'.$day.'/'.$headline_url);
					}
				} else {
					// Non-admins shouldn't see this kinda ting.
					$this->errors->set('The page you are looking for is not here.');
					redirect('error');
				}
			}
		} 
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function edit_comment() {
		
		// Get user_id from session.
		$user_id 	= $this->user_info['id'];
		// Get comment_id
		$comment_id = $_POST['ci'];
		// Get comment
		$comment = $_POST['comment'];
	
		// Check user is logged in.
		if(!$this->user->is_logged_in()) {
			
			echo '{ id: "", html: "", message: "<div class=\"error\">Your session has expired, please login and try again.</div>" }';
		
		} elseif($this->user->is_restricted($user_id)){
			
			echo '{ id: "", html: "", message: "<div class=\"error\">You have been banned, you are not allowed to post comments.</div>" }';
			
		} else {
					
			// Set rules for validation
			$this->form_validation->set_rules('comment', 	'Message', 	'trim|required|not_undefined|xss_clean');
			
			// Set error delimiters
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			// Run validation rules.
			if($this->form_validation->run() == false){
				// Validation failed.
				echo '{ id: "", html: "", message: "'.addslashes(form_error('comment')).'" }';
				
			} else {
				// Validation passed. Process form.
				
				// Save comment to database.
				$this->story->update_comment($comment_id, $comment);
				
				// Display comment.
				$html = '<div id="comment_detail_'.$comment_id.'">'.display_comment($comment).'</div>';
				
				$html = str_replace("\n", "", $html);
				
				echo '{ id: "'.$comment_id.'", html: "'.addslashes($html).'", message: "" }';
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function favorite() {
		// Declare vars we'll need. story_id and user_id (cuz we don't have access to the session).
		$story_id = $_POST['id'];
		
		// Get user_id from session.
		$user_id 	= $this->user_info['id'];
		
		// Check user is logged in.
		if(!$this->user->is_logged_in()) {
			
			echo 'Your session has expired, please login and try again.';
			
		} elseif($this->story->is_favorite($story_id, $user_id)){
			// Check to see if this story is a favorite already.
			echo 'This story is already your favorite. You must really like it.';
		
		} else {
			// Add to favorites.
			$this->story->add_to_favorites($story_id, $user_id);
			
			// Return no errors.
			echo '';
		}
	}
	
	
	
	
	
	
	
	
	
	function filter() {
		
		$category = $this->uri->segment(1);
	
		$config['base_url'] = '/'.$category;
		$config['total_rows'] = $this->story->count_where(array('category' => $category, 'view' => '1'));
		$config['per_page'] = $this->config->item('num_stories_per_page');
		$config['uri_segment'] = '2';
		
		$this->pagination->initialize($config);
	
		// Prepare article list for display.
		$data = array(
			'stories' 			=> $this->story->get_where(array('category' => $category, 'view' => '1'), $this->pagination->limit(), $this->pagination->offset()),
			'votes'				=> $this->story->get_story_votes_where(array('category' => $category, 'view' => '1')),
			'title'	 			=> $this->lang->line($category),
			'empty_message'		=> 'No news in "'.$this->lang->line($category).'".',
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
		
		// Show article list.
		$this->load->view('stories/list', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function images() {

		// Get the category.
		$category = ($this->uri->segment(1) == 'images') ? 'all' : $this->uri->segment(1);
		
		// Story type.
		$story_type = ($this->uri->segment(2) == 'upcoming') ? 0 : 1;
		
		// Title.
		$title = 'Images';
		$title .= (empty($category) || $category == 'all') ? '' : ' in '.$this->lang->line($category);
		
		$top_in = $this->uri->segment(3);
		
		$where = array(
					'category' 			=> $category,
					'view' 				=> $story_type
				 );
		
		if(!empty($top_in) && $top_in != 'images'){
			$where['datesubmitted >= '] = $this->period_of_time($this->uri->segment(3));
		}
		
		// Prepare article list for display.
		$data = array(
			'images'			=> $this->story->get_images_where($where),
			'title'	 			=> $title,
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
		
		// Show article list.
		$this->load->view('stories/images', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function participants() {
		// Get story_id
		$story_id = $_GET['sid'];
		
		// Get Activity in the story.
		$activity = $this->story->get_participants($story_id);
		
		$num_participants = count((array) $activity);
		
		// Set default html value.
		$html = 'Participants: ';
		if($num_participants == 1) {
			$html .= '<a href="/users/'.$activity[0]['username'].'">'.$activity[0]['username'].'</a>';
		} else {
			foreach($activity as $key => $a){
				if($key != ($num_participants - 1)) {
					$html .= '<a href="/users/'.$a['username'].'">'.$a['username'].'</a>, ';
				} else {
					$html = rtrim($html, ', ');
					$html .= ' and <a href="/users/'.$a['username'].'">'.$a['username'].'</a>.';
				}
			}
		}
		
		echo $html;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function period_of_time($period_of_time) {
		// returns where clause.
		// Extrapulate period of time from string.
		preg_match('/([0-9]+)([A-Za-z]+)/i', $period_of_time, $matches);
		
		// Catch invalid format error.
		if(count((array) $matches) != 3) {
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		}
		
		// Convert to singular time for converstion purposes.
		$singular_time = strtolower(rtrim($matches[2], 's'));
		
		// Define time values for multiplication of time.
		$time_values = array(
							'second' 	=> 1,
							'minute' 	=> 60,
							'hour'		=> 3600,
							'day'		=> 86400,
							'week'		=> 604800,
							'month'		=> 2629800,
							'year'		=> 31557600
					   );
		// Catch time value does exist error.
		if(!isset($time_values[$singular_time])){
			
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		}
					   
		// Finally determine the time to minus from where clause.
		return ( time() - ($matches[1] * $time_values[$singular_time]) );
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function perspective() {

		// Get the category.
		$category = ($this->uri->segment(1) == 'perspective') ? 'all' : $this->uri->segment(1);
		
		// Story type.
		$story_type = ($this->uri->segment(2) == 'upcoming') ? 0 : 1;
		
		// Title.
		$title = 'Perspective';
		$title .= (empty($category) || $category == 'all') ? '' : ' on '.$this->lang->line($category);
		
		$top_in = $this->uri->segment(3);
		
		$where = array(
					'category' 			=> $category,
					'view' 				=> $story_type
				 );
		
		if(!empty($top_in) && $top_in != 'perspective'){
			$where['datesubmitted >= '] = $this->period_of_time($this->uri->segment(3));
		}
		
		
		// Prepare article list for display.
		$data = array(
			'stories'			=> $this->story->get_perspective($where),
			'votes'				=> $this->story->get_story_votes_where($where),
			'chains'			=> $this->story->get_chains($where),
			'title'	 			=> $title,
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
		
		// Show article list.
		$this->load->view('stories/perspective', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function popular() {

		// Get the category.
		$category = $this->uri->segment(1);
		
		// Story type.
		$story_type = $this->uri->segment(2);
		
		$config['base_url'] = '/'.$category.'/'.$story_type;
		$config['total_rows'] = $this->story->count_where();
		$config['per_page'] = $this->config->item('num_stories_per_page');
		$config['uri_segment'] = '3';
		
		$this->pagination->initialize($config);
		
		$title = (empty($category) || $category == 'all') ? 'All News' : $this->lang->line($category);
	
		// Prepare article list for display.
		$data = array(
			'stories' 			=> $this->story->get_where(array(), $this->pagination->limit(), $this->pagination->offset()),
			'votes'				=> $this->story->get_story_votes_where(),
			'title'	 			=> $title,
			'empty_message'		=> "No-one's posted anything yet, but feel free to start things up.",
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
		
		// Show article list.
		$this->load->view('stories/list', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function processing() {
	
		// Check user is logged in.
		$this->util->check_user_logged_in();
		
		
		if($this->session->userdata($this->config->item('session_key').'_headline') != '' ||
			$this->session->userdata($this->config->item('session_key').'_article')) {
			
			// Redirect to step 2.
			redirect('/submit/step2');
		} else {
		
			// URL Stored in session.
			$url = $this->session->userdata('submit_url');
			
			// Load the crawler.
			$this->load->helper('simple_html_dom');
			
			// Get domain from url. 
			$domain = parse_url($url, PHP_URL_HOST);
			
			// Get HTML from page and parse.
			$html = file_get_html($url);
			
			// Declare array for holding values.
			$arr = array();
			
			switch($domain) {
				case 'www.trinidadexpress.com':
					// Set arr values.
					
					$headline = '';
					$font_tags = @$html->find('font');
					
					foreach($font_tags as $key => $t) {
						if(@$t->class == "MRED" && @$t->style == "color:#b50f00;font-size: 22px;")
							$headline = @$t->find('b', 0)->innertext;
					}
					
					$arr['headline'] = display_headline(str_replace("\n", "", strip_tags($headline)));
					
					//$arr['subheadline'] = strip_tags(@$html->find(".subheadline", 0)->innertext);
					//$arr['author'] = strip_tags(@$html->find(".byline", 0)->innertext);
					//$arr['date'] = strip_tags(@$html->find(".dateline", 0)->innertext);
					$arr['article'] = '';
					
					// Article text is split up in to divs.
					$text = @$html->find('.texte');
					foreach($text as $t) {
						if(@$t->tag == 'div')
							$arr['article'] .= strip_tags(@$t->innertext).' ';
					}
					
					$arr['article'] = display_preview($arr['article'], 200);
					
					// Get images in the form of an array.
					$imgs = $html->find('hbe', 0)->find('img');
					
					foreach($imgs as $i)
						if(substr(@$i->src, -3) == 'jpg')
							$arr['images'][] = str_replace(' ', '%20', @$i->src);
					break;
				case 'guardian.co.tt':
					// Set arr values.
					$arr['headline'] = display_headline(str_replace("\n", "", strip_tags(@$html->find(".title", 0)->innertext)));
					//$arr['subheadline'] = strip_tags(@$html->find(".drop", 0)->innertext);
					//$arr['author'] = strip_tags(@$html->find(".node-author", 0)->innertext);
					//$arr['date'] = strip_tags(@$html->find(".date-display-single", 0)->innertext);
					$arr['article'] = '';
					
					// Article text is split up in to p's.
					$text = @$html->find('.node-body', 0)->find('p');
					foreach($text as $t) {
						$arr['article'] .= strip_tags(@$t->innertext)."\n\n";
					}
					$arr['article'] = display_preview($arr['article'], 200);
					
					// Get images in the form of an array.
					$imgs = @$html->find('.node-body', 0)->find('img');
					foreach($imgs as $i)
						if(substr(@$i->src, -3) == 'png' && substr(@$i->src, 0, 4) == 'http')
							$arr['images'][] = str_replace(' ', '%20', parse_url(@$i->src, PHP_URL_PATH));
					
					break;
				case 'newsday.co.tt':
				case 'www.newsday.co.tt':
					
					// Set arr values.
					$arr['headline'] = display_headline(str_replace("\n", "", strip_tags(@$html->find(".bigtitle", 0)->innertext)));
					//$arr['subheadline'] = strip_tags(@$html->find(".article", 0)->innertext);
					//$arr['author'] = strip_tags(@$html->find(".node-author", 0)->innertext);
					//$arr['date'] = strip_tags(@$html->find(".date-display-single", 0)->innertext);
					$arr['article'] = '';
					
					// Article text is split up in to p's.
					$text = @$html->find('.article', 0)->find('p');
					foreach($text as $t) {
						$arr['article'] .= strip_tags(@$t->innertext)."\n\n";
					}
					$arr['article'] = display_preview($arr['article'], 200);
					
					// Get images in the form of an array.
					$imgs = @$html->find('.article', 0)->find('img');
					foreach($imgs as $i)
						if(strtolower(substr(@$i->src, -3)) == 'jpg')
							$arr['images'][] = str_replace(' ', '%20', substr(@$i->src, 1));
							
					break;
				default:
					break;
			}
			
			// If an image is available.
			if(count((array) $arr['images']) > 0){
				
				// Get image folder.
				$img_folder = '/tmp/'.md5('submit'.$this->session->userdata($this->config->item('session_key').'_unm'));
				$ext = strrchr($arr['images'][0], '.');
				
				// Create the folder.
				$this->util->mkrdir($_SERVER['DOCUMENT_ROOT'].$img_folder);
				$this->util->mkrdir($_SERVER['DOCUMENT_ROOT'].$img_folder.'/thm/');
				
				// Set the filename.
				$new_filename = $this->util->get_random_filename($_SERVER['DOCUMENT_ROOT'].$img_folder, $ext);
				
				// Get domain from url. 
				$url_arr = parse_url($url);
				$prefix = $url_arr['scheme'].'://'.$url_arr['host'];
				
				// Get ONE image from server.
				$contents = file_get_contents($prefix.$arr['images'][0]);
				$full_path = $_SERVER['DOCUMENT_ROOT'].$img_folder.'/'.$new_filename.$ext;
				$fp = fopen($full_path, 'w');
				fwrite($fp, $contents);
				fclose($fp); 
				
				
				// Get image size.
				if (false !== ($dimensions = @getimagesize($full_path))){
					
					// Set Dimensions.
					$image_width		= $dimensions['0'];
					$image_height		= $dimensions['1'];
				
					// Prepare to Resize Image.
					$config['image_library'] = 'gd2';
					$config['source_image'] = $full_path; //$upload_data['full_path'];
					$config['new_image'] = $full_path; //$upload_data['file_path'].'thm/'.$new_filename.$ext;
					$config['maintain_ratio'] = true;
					if($image_height > $image_width){
						$config['width'] = 101;
						$config['height'] = 10000;
					} else {
						$config['width'] = 10000;
						$config['height'] = 101;
					}
					
					// Initialize the object with the following settings.
					$this->image_lib->initialize($config);
					
					// Resize the image.
					$this->image_lib->resize();
					
					// Clear the settings in preparation for other images.
					$this->image_lib->clear();
					
					// Prepare to crop image.
					$config['image_library'] = 'gd2';
					$config['source_image'] = $full_path; //$upload_data['file_path'].'thm/'.$new_filename.$ext;
					$config['new_image'] = $full_path; //$upload_data['file_path'].'thm/'.$new_filename.$ext;
					$config['width'] = 100;
					$config['height'] = 100;
					$config['maintain_ratio'] = false;
					$config['x_axis'] = '0';
					$config['y_axis'] = '0';
					
					// Initialize the object with the following settings.
					$this->image_lib->initialize($config);
					
					// Crop the image.
					$this->image_lib->crop();
					
					// Clear the settings in preparation for other images.
					$this->image_lib->clear();
					
					// Copy file to thumbs folder.
					@copy($full_path, $_SERVER['DOCUMENT_ROOT'].$img_folder.'/thm/'.$new_filename.$ext);
				}
	
			}
			
			
			// Put values in the session.
			$this->session->set_userdata(array(
				'headline' => iconv('UTF-8', 'ISO-8859-1//IGNORE', $arr['headline']),
				'article' => iconv('UTF-8', 'ISO-8859-1//IGNORE', $arr['article'])
			));
			
			// Redirect to step 2.
			redirect('/submit/step2');
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function report_abuse() {
		
		// You need the type (story / comment), story or comment, user_id, value
		$report_str = $_POST['id'];
		// Get user_id from session.
		$user_id 	= $this->user_info['id'];
		
		// if somebody stumbles on the page for no reason. return nothing.
		if(empty($report_str)) 
			echo '';
		
		else if(empty($user_id))
			echo '{ value: "", message: "You must be logged in to report." }';
			
		else {
			// Extract info from report string.
			$report_info = explode('_', $report_str);
			
			$type = $report_info[1];
			$story = array();
			$comment = array();
			
			if($type == 'story')
				$story = $this->story->info_from_id($report_info[2]);
			else if($type == 'comment'){
				$comment = $this->story->comment_info($report_info[2]);
				$story = $this->story->info_from_id($comment['story_id']);
			}
			
			$comment_id = isset($comment['id']) ? $comment['id'] : '';
		
			// Check for duplicates.
			// Get report.
			$report = $this->story->get_report($story['id'], $comment_id, $user_id);
			
			if($report !== false) {
				echo '{ value: "", message: "You already reported this '.$report_info[1].'." }'; 
			} else {
			
				if($this->user->is_restricted($user_id)){
					// Can't demote anything.
					echo '{ value: "", message: "You are not allowed to report anything." }'; 
				} else {
					
					// Register report.
					$this->story->register_report($story['id'], $comment_id, $user_id);
					echo '{ value: "", message: "" }';
				}
				
			} // End if report exist.
			
		} // End if someone stubles on page for no reason.
		
	} // End function report_abuse.
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function step2() {
	
		// Check user is logged in.
		$this->util->check_user_logged_in();
		
		// Set data for form.
		$data = array(
					'img_folder'	=> md5('submit'.$this->session->userdata($this->config->item('session_key').'_unm')),
					'headline' 		=> ($_POST) ? $_POST['headline'] : $this->session->userdata('headline'),
					'article' 		=> ($_POST) ? $_POST['what'] : $this->session->userdata('article'),
					'url'			=> $this->session->userdata('submit_url')
				);
		
		// Set rules for validation
		$this->form_validation->set_rules('headline', 	'headline', 	'trim|required|max_length[60]|xss_clean');
		$this->form_validation->set_rules('what', 		'description', 	'trim|required|max_length[210]|xss_clean');
		$this->form_validation->set_rules('tags', 		'category',		'trim|required|min_length[3]|max_length[100]|xss_clean');
		$this->form_validation->set_rules('where', 		'where', 		'trim|min_length[3]|max_length[40]|xss_clean');
		$this->form_validation->set_rules('posx',	 	'position X', 	'');
		$this->form_validation->set_rules('posy',	 	'position Y', 	'');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		// Run validation rules.
		if($this->form_validation->run() == false){
		
			// Show the submit form.
			$this->load->view('stories/step2', $data);
		} else {
			// Validation passed. Process form.
			
			$this->session->unset_userdata(array(
				'headline' => '',
				'article' => ''
			));	
			
			
			// Check for story already submitted.
			if($this->story->already_submitted($_POST['headline'])){
				// User is not verified. Set message then redirect to home page.
				$this->messages->add("Story should be here already. Check in <a href=\"/all/upcoming\">More News</a>.");
				
				// redirect to home page.
				redirect();
				
			} elseif($this->story->too_soon()) {
				// User is not verified. Set message then redirect to home page.
				$this->messages->add("Hold up on submitting this story for a little while.");
				
				// redirect to home page.
				redirect();
			} else {
			
				// Save article to database. Return headline url for redirection.
				$headline_url = $this->story->create();
				
				// Check to see if there are images associated with this story.
				$session_path = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$data['img_folder'];
				
				if(file_exists($session_path)) {
				
					// Delete images that aren't in the story.
				
					// Create a folder for files associated with this story can be stored.
					$abs_path = $_SERVER['DOCUMENT_ROOT'].'/img/'.$headline_url;
					$this->util->mkrdir($abs_path);
					
					// Move files uploaded (if any) to permanent folder.
					$this->util->move_files($session_path, $abs_path, false);
					
					// Delete the orignal folder.
					$this->util->delete_dir($session_path);
				}
				
				// Send an email to the administrator.
				$this->load->library('email');
				$this->email->clear();
				$this->email->from($this->config->item('system_email'), $this->config->item('title'));
				$this->email->to('price.aaron@gmail.com');
				$this->email->subject('Submitted: '.$_POST['headline']);
				$this->email->message(
										"Hey there,\n\n".
										"Check out the story here:\n".
										"http://shou.tt/".$headline_url."\n\n".
										"Submitted by: http://shou.tt/users/".$this->session->userdata($this->config->item('session_key').'_unm')."\n\n".
										"Thanks again,\n\n".
										$this->config->item('title')."\n".
										$this->config->item('base_url')
									  );
				$this->email->send();
				
				// This is the way it should work, unfortunately, the approval process is being used for now.
				// Redirect to article.
				redirect('/'.$headline_url);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function story_deleted() {
		// Get random success.
		$success_quotes = $this->lang->line('success');
		$success_quote = "Hooray!";
		
		if(is_array($success_quotes)) {
			$success_quote = $success_quotes[array_rand($success_quotes)];
		}
	
		// Set message
		$this->messages->add('<h4 class="message-header">'.$success_quote.'</h4><p class="message-detail">Your story has been deleted.</p>');
		
		// Return to login.
		redirect();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function submit(){
		
		// Check user is logged in.
		$this->util->check_user_logged_in();
		
		// Check if user is restricted.
		if($this->user->is_restricted($this->user_info['id'])) {
			$this->errors->set('Your account has been restricted, you are not allowed to post stories.');
			redirect('error');
		} else {
			
			// Set rules for validation
			$this->form_validation->set_rules('url', 'url',	'trim|required|max_length[255]|valid_url|callback_check_url|xss_clean');
			
			// Set error delimiters
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			// Run validation rules.
			if($this->form_validation->run() == false){
			
				// Show the submit form.
				$this->load->view('stories/submit');
			} else {
				// Validation passed. Process form.
				
				// Don't duplicate stories.
				$story_url = $this->story->check_duplicate_submission($_POST['url']);
				
				if($story_url === false) {
				
					// Unset values for _article and _headline
					$this->session->unset_userdata(array(
						$this->config->item('session_key').'_headline' => '',
						$this->config->item('session_key').'_article' => ''
					));	
					
					// Delete files in /tmp/
					@$this->util->delete_dir($_SERVER['DOCUMENT_ROOT'].'/tmp/'.md5('submit'.$this->session->userdata($this->config->item('session_key').'_unm')));
					
					// Store the URL in the session.
					$this->session->set_userdata(array(
						'submit_url' => $_POST['url']
					));
					
					// Redirect to processing url for processing.
					redirect('/submit/processing');
				} else {
					// Add message that the story already exists.
					$this->messages->add("This story has already been submitted.");
					
					// Redirect to story.
					redirect($story_url);
				}
			}
		}
	}
	
	
	
	
	
	
	
	
	
	function top_in() {
		// Get category
		$category = $this->uri->segment(1);
	
		// Get story type (popular or upcoming)
		$story_type = $this->uri->segment(2);
		
		// Get view for where clause.
		$view = ($story_type == 'popular') ? 1 : 0;
		
		$period_of_time = $this->uri->segment(3);
		
		// Display top stories in a given period of time.
		$after = $this->period_of_time($period_of_time);
		
		// Extrapulate period of time from string.
		preg_match('/([0-9]+)([A-Za-z]+)/i', $period_of_time, $matches);
		
		$where = array(
					'category' 			=> $category,
					'view' 				=> $view,
					'datesubmitted >= ' => $after
				 );
		
		// Set up pagination
		$config['base_url'] = '/'.$category.'/'.$story_type.'/'.$period_of_time;
		$config['total_rows'] = $this->story->count_where($where);
		$config['per_page'] = $this->config->item('num_stories_per_page');
		$config['uri_segment'] = '4';
		
		$this->pagination->initialize($config);
	
		// Prepare article list for display.
		$data = array(
			'stories' 			=> $this->story->get_top_in($where, $this->pagination->limit(), $this->pagination->offset()),
			'votes'				=> $this->story->get_story_votes_where($where),
			'title'	 			=> 'All News',
			'empty_message'		=> 'Seems like nothing was posted in the last '.$matches[1].' '.strtolower($matches[2]).', try <a href="/">starting over</a>.',
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
				
		// Show article list.
		$this->load->view('stories/list', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function undelete() {
		// Declare vars we'll need. story_id and user_id (cuz we don't have access to the session).
		$del_string = $_POST['id'];
		
		$del_arr = explode('_', $del_string);
		$id = $del_arr[2];
		$user_id = $_POST['token'];
		
		$user = $this->user->info_from_column('id', $user_id);
		
		if(empty($user_id)){
			echo "Sorry, I just don't feel like it.";
		} else {
		
			if($this->user->is_authorized($user['type'])){
				// Get abuse info.
				$trash_info = $this->story->trash_info($id);
				
				// Get info for database.
				$user_id 	= $this->session->userdata($this->config->item('session_key').'_usr');
				$story_id 	= $trash_info['story_id'];
				$comment_id = '';
				
				// If it's a comment.
				if($del_arr[1] == 'comment') {
					// Set the comment id.
					$comment_id = $trash_info['comment_id'];
					
					// Get the story id from comments table.
					$comment = $this->story->get_comment($comment_id);
					$story_id = $comment['story_id'];
				}
				
				// Delete the story or comment.
				$this->story->undelete($id, $trash_info['status'], $story_id, $comment_id);
				
				// Return message to the user.
				echo '';
			} else {
				// User has no business here.
				echo "I just can't do it captain... I haven't got the power.";
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function upcoming() {
	
		// Get the category.
		$category = $this->uri->segment(1);
		
		// Story type.
		$story_type = $this->uri->segment(2);
		
		$config['base_url'] = '/'.$category.'/'.$story_type;
		$config['total_rows'] = $this->story->count_where(array('category' => $category, 'view' => '0'));
		$config['per_page'] = $this->config->item('num_stories_per_page');
		$config['uri_segment'] = '3';
		
		$this->pagination->initialize($config);
		
		$title_app = (empty($category) || $category == 'all') ? '' : ' in '.$this->lang->line($category);
	
		// Prepare article list for display.
		$data = array(
			'stories' 			=> $this->story->get_where(array('category' => $category, 'view' => '0'), $this->pagination->limit(), $this->pagination->offset()),
			'votes'				=> $this->story->get_story_votes_where(array('category' => $category, 'view' => '0')),
			'title'	 			=> 'Upcoming'.$title_app,
			'empty_message'		=> 'No upcoming news in "'.$this->lang->line($category).'".',
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
		
		// Show article list.
		$this->load->view('stories/list', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function upload() {
		
		// Check upload.
		if($_FILES){
			// Define flash vars.
			$path = $_GET['folder'].'/';
			$ext = strrchr($_FILES['Filedata']['name'], '.');
			
			// Create the folder.
			$this->util->mkrdir($_SERVER['DOCUMENT_ROOT'].$path);
			$this->util->mkrdir($_SERVER['DOCUMENT_ROOT'].$path.'/thm/');
			
			// Set the filename.
			$new_filename = $this->util->get_random_filename($_SERVER['DOCUMENT_ROOT'].$path, $ext);
			
			// Initialize config options for upload.
			$config['file_name'] = $new_filename;
			$config['upload_path'] = '.'.$path;
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '0';
			$config['max_width']  = '0';
			$config['max_height']  = '0';
			$config['is_flash_upload']  = true;
			$this->load->library('upload', $config);
			
			if (!$this->upload->do_upload('Filedata')){
				//$data['images_error'] .= $this->upload->display_errors();
				// Display errors.
				$errors = $this->upload->display_errors();
				if(!empty($errors))
					echo $errors;
					
			} else {
				// Get upload data.
				$upload_data = $this->upload->data();
				
				// Prepare to Resize Image.
				$config['image_library'] = 'gd2';
				$config['source_image'] = $upload_data['full_path'];
				$config['new_image'] = $upload_data['file_path'].'thm/'.$new_filename.$upload_data['file_ext'];
				$config['maintain_ratio'] = true;
				if($upload_data['image_height'] > $upload_data['image_width']){
					$config['width'] = 101;
					$config['height'] = 10000;
				} else {
					$config['width'] = 10000;
					$config['height'] = 101;
				}
				
				// Initialize the object with the following settings.
				$this->image_lib->initialize($config);
				
				// Resize the image.
				$this->image_lib->resize();
				
				// Clear the settings in preparation for other images.
				$this->image_lib->clear();
				
				// Prepare to crop image.
				$config['image_library'] = 'gd2';
				$config['source_image'] = $upload_data['file_path'].'thm/'.$new_filename.$upload_data['file_ext'];
				$config['new_image'] = $upload_data['file_path'].'thm/'.$new_filename.$upload_data['file_ext'];
				$config['width'] = 100;
				$config['height'] = 100;
				$config['maintain_ratio'] = false;
				$config['x_axis'] = '0';
				$config['y_axis'] = '0';
				
				// Initialize the object with the following settings.
				$this->image_lib->initialize($config);
				
				// Crop the image.
				$this->image_lib->crop();
				
				// Clear the settings in preparation for other images.
				$this->image_lib->clear();
				
				// Resize really large images if necessary.
				if($upload_data['image_height'] > 700 || $upload_data['image_width'] > 1000){
					$config['image_library'] = 'gd2';
					$config['source_image'] = $upload_data['full_path'];
					$config['new_image'] = $upload_data['full_path'];
					$config['maintain_ratio'] = true;
					$config['height'] = 700;
					$config['width'] = 1000;
					
					// Initialize the object with the following settings.
					$this->image_lib->initialize($config);
					
					// Resize the image.
					$this->image_lib->resize();
					
					// Clear the settings in preparation for other images.
					$this->image_lib->clear();
				} // End if resize.
				
			} // End if upload success.
			
			
			// Display files.
			if(empty($upload_data['file_name'])) {
				echo '1';
			} else {
				// Define vars to return as JSON.
				$thumbpath 	= $path.'thm/'.$new_filename.$upload_data['file_ext'];
				$imgpath 	= $path.$new_filename.$upload_data['file_ext'];
				$filename 	= basename($thumbpath);
				$id 		= str_replace('.', '_', $filename);
				$html		= '<div id="im_'.$id.'">'.
									'<a id="thm_'.$id.'" class="group" rel="group" href="'.$imgpath.'">'.
										'<img src="'.$thumbpath.'" alt="">'.
									'</a>'.
									'<span class="filename">'.$filename.'</span>'.
									'<a id="'.$id.'" class="dim">Delete</a>'.
							   '</div>';
			
				echo '{ '. 
						'filename: "'.$filename.'", '.
						'id: "'.$id.'", '.
						'thumbpath: "'.$thumbpath.'", '.
						'imgpath: "'.$imgpath.'", '.
						'html: "'.addslashes($html).'" '.
					 '}';
			}

		} // End if files.
	} // End function
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function videos() {

		// Get the category.
		$category = ($this->uri->segment(1) == 'videos') ? 'all' : $this->uri->segment(1);
		
		// Story type.
		$story_type = ($this->uri->segment(2) == 'upcoming') ? 0 : 1;
		
		// Title.
		$title = 'Videos';
		$title .= (empty($category) || $category == 'all') ? '' : ' in '.$this->lang->line($category);
		
		$top_in = $this->uri->segment(3);
		
		$where = array(
					'category' 			=> $category,
					'view' 				=> $story_type
				 );
		
		if(!empty($top_in) && $top_in != 'images'){
			$where['datesubmitted >= '] = $this->period_of_time($this->uri->segment(3));
		}
		
		// Prepare article list for display.
		$data = array(
			'videos'				=> $this->story->get_videos_where($where),
			'title'	 			=> $title,
			'user_info'			=> $this->user_info,
			'url_info'			=> $this->url_info
		);
		
		// Show article list.
		$this->load->view('stories/videos', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function vote() {
		
		// You need the type (story / comment), story or comment, user_id, value
		$vote_str 	= $_POST['id'];
		// Get user_id from session.
		$user_id 	= $this->user_info['id'];
		
		// Extract info from vote string.
		$vote_info = explode('_', $vote_str);
		
		// if somebody stumbles on the page for no reason. return nothing.
		if(empty($vote_str)) 
			echo '';
		
		elseif(empty($user_id))
			echo '{ value: "", vote_count: "", message: "You must be logged in to vote." }';
		
		elseif($this->user->is_restricted($user_id))
			echo '{ value: "", vote_count: "", message: "You are not allowed to vote." }'; 
		
		elseif( !isset($vote_info[0]) || empty($vote_info[0]) ||
				!isset($vote_info[1]) || empty($vote_info[1]) ||
				!isset($vote_info[2]) || empty($vote_info[2]) )
			echo '{ value: "", vote_count: "", message: "Invalid vote." }';
			
		else {
			
			// Check for duplicates.
			// Get vote.
			$vote = $this->story->get_vote($vote_info[1], $vote_info[2], $user_id);
			
			if($vote !== false) {
			
				// Submitters can't change votes.
				if(isset($vote['submitted'])) {
					// Send appropriate message.
					echo '{ value: "", vote_count: "", message: "You submitted this '.$vote_info[1].', you cannot change your vote." }'; 
				} else {
					
					if($vote_info[1] == 'story' && $vote['value'] == '0'){	
						// Submit this vote to the almightly algorithm.
						// to determine eligability for promotion.
						$this->story->assess($vote_info[2]);
					}
			
					// Check if the action is the same (i.e.: promote / demote)
					switch($vote_info[0]) {
						case 'promote': 
							if($vote['value'] == '1') {
								// Can't promote twice.
								echo '{ value: "", vote_count: "", message: "You already promoted this '.$vote_info[1].'." }'; 
							} else {
								
								$this->story->update_vote($vote_info[1], $vote_info[2], $user_id, ( intval($vote['value']) + 1 ));
								$popularity = $this->story->get_popularity($vote_info[1], $vote_info[2]);
								
								echo '{ value: "'.($vote['value'] + 1).'", vote_count: "'.$popularity.'", message: "" }';
							}
							break;
						case 'demote':
							if($vote['value'] == '-1') {
								// Can't demote twice.
								echo '{ value: "", vote_count: "", message: "You already demoted this '.$vote_info[1].'." }'; 
							} else {
								$this->story->update_vote($vote_info[1], $vote_info[2], $user_id, ( intval($vote['value']) - 1 ));
								$popularity = $this->story->get_popularity($vote_info[1], $vote_info[2]);
								echo '{ value: "'.($vote['value'] - 1).'", vote_count: "'.$popularity.'", message: "" }';
							}
							break;
					}
				}
			} else {
				
				// Figure out whether it's a up or down.
				switch($vote_info[0]){
					case 'promote': $value = 1; break;
					case 'demote': $value = -1; break;
					default: $value = 0; break;
				}
				
				// Register vote.
				$this->story->register_vote($vote_info[1], $vote_info[2], $user_id, $value);
				
				if($vote_info[1] == 'story'){
					// Submit this vote to the almightly algorithm 
					// to determine eligability for promotion.
					$this->story->assess($vote_info[2]);
				}
			
				// Return the value value of the story after the vote. (DO NOT INCLUDE NEGATIVE VOTES)
				$popularity = $this->story->get_popularity($vote_info[1], $vote_info[2]);
				echo '{ value: "'.$value.'", vote_count: "'.$popularity.'", message: "" }';
				
			}
		}
	}
}
?>