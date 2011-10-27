<?php

class Users extends Controller {



	function Users() {
		parent::Controller();
		
		// Load User Model
		$this->load->model('user');
		
		// Load Util Library.
		$this->load->library('util');
		
		// Load the image manipulation library.
		$this->load->library('errors');
		
		// Load Messages Library to pass session messages when required.
		$this->load->library('messages');
		
		// Load the Pagination Library.
		$this->load->library('pagination');
		
		// Load the image helper.
		$this->load->helper('image');
		
		// Load the language file for messages.
		$this->lang->load('error', 'english');
		
		// Redirect to proper domain.
		$this->util->domain_redir();
	}
	
	
	
	
	
	
	
	
	function _remap($method) {
		if(method_exists($this, $method)){
			$this->$method();
		} else {
			$this->index();
		}
	}
	
	
	
	
	
	
	
	
	
	
	function authenticated($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('authenticated', 'The username or password you entered is incorrect.');
		// See if the user exists and their password is correct.
		return ($this->user->is_authentic($_POST['username'], $_POST['pwd'])) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	function banned($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('banned', 'This account has been banned.');
		// See if the user is banned.
		return ($this->user->is_banned($_POST['username'])) ? false : true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function captcha_check() {
		// Then see if a captcha exists:
		$exp = time() - 600;
		$sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($this->input->post('captcha'), $this->input->ip_address(), $exp);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();
		
		// Set Message.
		$this->form_validation->set_message('captcha_check', 'The text you entered is invalid.');
		return ($row->count == 0) ? false : true;
	} 
	
	
	
	
	
	
	
	
	
	
	
	
	
	function favorites(){
		// Get username from URI
		$url_username = $this->uri->segment(2);
		
		// Check if username exists.
		if(empty($url_username)) {
			show_404();
		} else {
			// check if username is legit.
			$user = $this->user->info_from_column('username', $url_username);
			
			if($user === false) {
				$this->errors->set("The page you are looking for is not here.");
				redirect('error');
			} else {
				
				// Register a view on the profile.
				$this->user->click($user['id']);
				
				// Configure Pagination for history.
				$config['base_url'] = '/users/'.$user['username'].'/history/favorites';
				$config['total_rows'] = $this->user->count_activity_where(array('user_id' => $user['id'], 'type' => 'favorite'));
				$config['per_page'] = '15';
				$config['uri_segment'] = '5';
				
				$this->pagination->initialize($config);
			
				$data = array(
							'url_username' 		=> $url_username,
							'user_id'			=> $user['id'],
							'favorites' 		=> $this->user->get_activity_where(array('user_id' => $user['id'], 'type' => 'favorite'), $this->pagination->limit(), $this->pagination->offset()),
							'info'				=> $user,
							'activity_stats'	=> $this->user->get_activity_stats($user['id']),
							'show_ban'			=> ($this->util->user_is_admin() && !$this->util->user_is_admin($user['id']) /* && $user['id'] != $this->session->userdata($this->config->item('session_key').'_usr')*/)
						);
				$this->load->view('users/favorites', $data);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	function history(){
		// Get username from URI
		$url_username = $this->uri->segment(2);
		
		// Check if username exists.
		if(empty($url_username)) {
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		} else {
			// check if username is legit.
			$user = $this->user->info_from_column('username', $url_username);
			
			if($user === false) {
				$this->errors->set("The page you are looking for is not here.");
				redirect('error');
			} else {
				// Register a view on the profile.
				$this->user->click($user['id']);
				
				// Configure Pagination for history.
				$config['base_url'] = '/users/'.$user['username'].'/history';
				$config['total_rows'] = $this->user->count_activity_where(array('user_id' => $user['id']));
				$config['per_page'] = '15';
				$config['uri_segment'] = '4';
				
				$this->pagination->initialize($config);
			
				$data = array(
							'url_username' 		=> $url_username,
							'user_id'			=> $user['id'],
							'activity' 			=> $this->user->get_activity_where(array('user_id' => $user['id']), $this->pagination->limit(), $this->pagination->offset()),
							'info'				=> $user,
							'activity_stats'	=> $this->user->get_activity_stats($user['id']),
							'show_ban'			=> ($this->util->user_is_admin() && !$this->util->user_is_admin($user['id']) /* && $user['id'] != $this->session->userdata($this->config->item('session_key').'_usr')*/)
						);
				$this->load->view('users/history', $data);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	function index(){
		// Get username from URI
		$url_username = $this->uri->segment(2);
		
		// Check if username exists.
		if(empty($url_username)) {
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		} else {
			// check if username is legit.
			$user = $this->user->info_from_column('username', $url_username);
			
			if($user === false) {
				$this->errors->set("The page you are looking for is not here.");
				redirect('error');
			} else {
				
				// Register a view on the profile.
				$this->user->click($user['id']);
				
				// Set data for form.
				$data = array(
							'url_username' 		=> $url_username,
							'user_id'			=> $user['id'],
							'activity' 			=> $this->user->get_activity_where(array('user_id' => $user['id']), 5),
							'favorites'			=> $this->user->get_activity_where(array('user_id' => $user['id'], 'type' => 'favorite'), 3),
							'info'				=> $user,
							'activity_stats'	=> $this->user->get_activity_stats($user['id']),
							'show_ban'			=> ($this->util->user_is_admin() && !$this->util->user_is_admin($user['id']) /* && $user['id'] != $this->session->userdata($this->config->item('session_key').'_usr')*/)
						);
				
				// Display data.		
				$this->load->view('users/profile', $data);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function ip_banned($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('ip_banned', 'This computer has been banned.');
		// See if the user is banned.
		return ($this->user->ip_banned($_SERVER['REMOTE_ADDR'])) ? false : true;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function log(){
		// Get username from URI
		$url_username = $this->uri->segment(2);
		
		// Check if username exists.
		if(empty($url_username)) {
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		} else {
			
			if($this->util->user_is_admin()){
			
				// Declare log form.
				$this->form_validation->set_rules('user_id', 	'User Id', 	'required');
				$this->form_validation->set_rules('report',		'Report', 	'required|min_length[5]');
				$this->form_validation->set_rules('score', 		'Score', 	'required');
				
				// Set error delimiters
				$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
				
				// Run validation rules.
				if($this->form_validation->run() == false){
					
					// Get user details.
					$info = $this->user->info_from_column('username', $url_username);
					
					// Configure Pagination for history.
					$config['base_url'] = '/users/'.$info['username'].'/log';
					$config['total_rows'] = $this->user->count_log_where(array('user_id' => $info['id']));
					$config['per_page'] = '15';
					$config['uri_segment'] = '4';
					
					$this->pagination->initialize($config);
					
				
					// Show Admin
					$data = array(
								'url_username' 	=> $url_username,
								'user_id'		=> $info['id'],
								'info'			=> $info,
								'log'			=> $this->user->get_log_where(array('user_id' => $info['id']), $this->pagination->limit(), $this->pagination->offset()),
								'show_ban'		=> ($this->util->user_is_admin() && !$this->util->user_is_admin($info['id']) /* && $user['id'] != $this->session->userdata($this->config->item('session_key').'_usr')*/)
							);
							
					$this->load->view('users/log', $data);
					
					
				} else {
					// Process form
					$this->user->log($_POST['user_id'], $_POST['report'], $_POST['score']);
					
					// Set message that log was saved.
					$this->messages->add('Log Saved.');
					
					// Redirect to home page.
					redirect('users/'.$url_username.'/log');
				}
				
					
			} else {
				// User doesn't exist.
				$this->errors->set("The page you are looking for is not here.");
				redirect('error');
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function login() {
		
		if($this->user->is_logged_in()){
			// User is not verified. Set message then redirect to home page.
			$this->messages->add("You're already logged in. To switch users, please <a href=\"/logout\">logout</a> first.");
			
			// Redirect to home page.
			redirect();
		} else {
	
			// If you have just arrived on the login page without any attempts to login.
			if(!$_POST && isset($_SERVER['HTTP_REFERER']) && 
										 $_SERVER['HTTP_REFERER'] != 'http://'.$_SERVER['SERVER_NAME'].'/signup' && 
										 $_SERVER['HTTP_REFERER'] != 'http://'.$_SERVER['SERVER_NAME'].'/login' &&
										 $_SERVER['HTTP_REFERER'] != 'http://'.$_SERVER['SERVER_NAME'].'/error') {
				// Set session variable for where to return.
				$this->session->set_userdata(array(
					$this->config->item('session_key').'_ref' => $_SERVER['HTTP_REFERER']
				));
			}
		
			// Set rules for validation
			$this->form_validation->set_rules('username', 	'Username', 'required');
			$this->form_validation->set_rules('pwd', 		'Password', 'required|callback_authenticated|callback_verified|callback_banned');
			
			// Set error delimiters
			$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
			
			// Run validation rules.
			if($this->form_validation->run() == false){
			
				// Validation failed OR form not submitted, show form.
				$this->load->view('users/login');
			} else {
				// Validation passed. Process form.
				
				// Set session vars for user_id and md5($private_key.$email)
				$user_id = $this->user->get_id($_POST['username']);
				
				// If the user has not yet verified their email address. Redirect to the page that will allow them to do so.
				if($this->user->is_verified($_POST['username'])){
					
					$this->session->set_userdata(array(
						$this->config->item('session_key').'_usr' => $user_id,
						$this->config->item('session_key').'_uid' => md5($this->config->item('private_key').$_POST['username']),
						$this->config->item('session_key').'_unm' => $_POST['username']
					));
					
					// Redirect to new page.
					$redir = $this->session->userdata($this->config->item('session_key').'_rdt');
					if(empty($redir)) {
						
						// Get referer.
						$ref = $this->session->userdata($this->config->item('session_key').'_ref');
						$url_arr = explode('/', $ref);
							
						if(isset($url_arr[2]) && $url_arr[2] == $_SERVER['SERVER_NAME']) {
							// Redirect to previous page.
							header('Location: '.$ref);
						} else {
							// Go to main news page. 
							redirect();
						}
						
					} else {
						// Remove the redirect session key. So that you're not redirected everytime.
						$this->session->unset_userdata(array($this->config->item('session_key').'_rdt' => ''));
						// redirect user appropriately.
						redirect($redir);
					}
				} else {
					// User is not verified. Set message then redirect to home page.
					$this->messages->add('You must verify your email address before you get access to your account.');
					
					// Ensure to unset the redirect so that you don't have unexpected redirection.
					$this->session->unset_userdata(array($this->config->item('session_key').'_rdt' => ''));
					
					// Redirect to home page.
					redirect();
				}
			}
		}
	}
	
	
	
	
	
	
	
	function logout() {
		// Unset session valiables for login.
		$this->session->unset_userdata(array(
			$this->config->item('session_key').'_usr' => '',
			$this->config->item('session_key').'_uid' => '',
			$this->config->item('session_key').'_unm' => ''
		));
		// Kill the session.
		$this->session->sess_destroy();
		// Redirect to home page.
		redirect();
	}
	
	
	
	
	
	
	
	
	function recover() {
		// Set rules for validation
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|exists[users.email]');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		// Run validation rules.
		if($this->form_validation->run() == false){
			// Validation failed OR form not submitted, show form.
			$this->load->view('users/login');
		} else {
			
			// Get password from email address
			$user_info = $this->user->info_from_column('email', $_POST['email']);
			
			// Generate Password.
			$this->load->helper('string');
			$new_password = random_string('alnum', 8);
			
			// Reset Password.
			$this->user->set_password($user_info['username'], $new_password);
			
			// Send email to user.
			$this->load->library('email');
			$this->email->clear();
			$this->email->from($this->config->item('system_email'), $this->config->item('title'));
			$this->email->to($_POST['email']);
			$this->email->subject('Re: Password Reset');
			$this->email->message(
									"Hi there,\r\n\n".
									"Here are your new login details:\r\n".
									"Username: {$user_info['username']}\r\n".
									"Password: {$new_password}\r\n\n".
									"Remember to change your password as soon as you login.".
									"Thanks again,\r\n\n".
									$this->config->item('title')."\n".
									$this->config->item('base_url')
								  );
			$this->email->send();
			
			// Get mail address.
			$check_mail = '';
			$email_domain = strtolower(substr(strrchr($_POST['email'], '@'), 1));
			switch($email_domain){
				case 'gmail.com'		: $check_mail = ' Go to: <a href="http://www.gmail.com">http://www.gmail.com</a>.'; break;
				case 'hotmail.com'		: $check_mail = ' Go to: <a href="http://www.hotmail.com">http://www.hotmail.com</a>.'; break;
				case 'hotmail.co.uk'	: $check_mail = ' Go to: <a href="http://www.hotmail.co.uk">http://www.hotmail.co.uk</a>.'; break;
				case 'hotmail.ca'		: $check_mail = ' Go to: <a href="http://www.hotmail.ca">http://www.hotmail.ca</a>.'; break;
				case 'yahoo.com'		: $check_mail = ' Go to: <a href="http://mail.yahoo.com">http://mail.yahoo.com</a>.'; break;
				case 'yahoo.co.uk'		: $check_mail = ' Go to: <a href="http://mail.yahoo.co.uk">http://mail.yahoo.co.uk</a>.'; break;
				case 'yahoo.ca'			: $check_mail = ' Go to: <a href="http://mail.yahoo.ca">http://mail.yahoo.ca</a>.'; break;
				case 'trinidad.net'		: $check_mail = ' Go to: <a href="http://webmail.trinidad.net">http://webmail.trinidad.net</a>.'; break;
				case 'shaw.ca'			: $check_mail = ' Go to: <a href="http://webmail.shaw.ca">http://webmail.shaw.ca</a>.'; break;
				case 'khalsa.com'		: $check_mail = ' Go to: <a href="http://mail.google.com/hosted/khalsa.com">http://mail.google.com/hosted/khalsa.com</a>.'; break;
				case 'pixelstation.com'	: $check_mail = ' Go to: <a href="http://mail.google.com/a/pixelstation.com">http://mail.google.com/a/pixelstation.com</a>.'; break;
				case 'tstt.net.tt'		: $check_mail = ' Go to: <a href="http://webmail.tstt.net.tt/src/login.php">http://webmail.tstt.net.tt/src/login.php</a>.'; break;
				case 'wow.net'			: $check_mail = ' Go to: <a href="http://www.wow.net">http://www.wow.net</a>.'; break;
				default					: $check_mail = ''; break;
			}
			
			
			
			// Get random success.
			$success_quotes = $this->lang->line('success');
			$success_quote = "Hooray!";
			
			if(is_array($success_quotes)) {
				$success_quote = $success_quotes[array_rand($success_quotes)];
			}
		
			// Set message
			$this->messages->add('<h4 class="message-header">'.$success_quote.'</h4><p class="message-detail">Your password has been sent to <strong>'.$_POST['email'].'</strong>.'.$check_mail.'</p>');
			
			// Return to login.
			redirect('/login');
		}
	}
	
	
	
	
	
	
	
	
	

	function signup(){
	
		if($this->user->is_logged_in()){
			// User is not verified. Set message then redirect to home page.
			$this->messages->add("No need to sign up again, you're already logged in. If you'd like to register a new user account, please <a href=\"/logout\">logout</a> first.");
			
			// Redirect to home page.
			redirect();
		} else {
	
		
			// Users must be invited to register.
			$vcode = isset($_POST['vcode']) ? $_POST['vcode'] : $this->uri->segment(2);
			
			// Get invite info.
			$invite = $this->user->invite_info($vcode);
			
//			if(empty($vcode) || !is_array($invite)) {
//				// Set a useful error message.
//				$this->errors->set("This website is not yet open to the public. You must be invited to participate.");
//				redirect('error');
//			} else {
		
				// Load the Captcha library.
				$this->load->library('captcha');
			
				// Set rules for validation
				$this->form_validation->set_rules('username', 	'Username', 		'required|alpha_numeric|min_length[3]|max_length[25]|unique[users.username]|callback_suitable');
				$this->form_validation->set_rules('password', 	'Password', 		'required|min_length[5]');
				$this->form_validation->set_rules('confirm', 	'Confirm Password', 'required|matches[password]');
				$this->form_validation->set_rules('email', 		'Email', 			'required|valid_email|unique[users.email]');
				$this->form_validation->set_rules('day', 		'Day', 				'required');
				$this->form_validation->set_rules('month', 		'Month', 			'required');
				$this->form_validation->set_rules('year', 		'Year', 			'required|callback_valid_date');
				$this->form_validation->set_rules('captcha',	'Captcha', 			'required|callback_captcha_check');
				$this->form_validation->set_rules('ip', 		'IP Address', 		'exact_length[0]|callback_ip_banned');
				
				// Set error delimiters
				$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
				
				// Run validation rules.
				if($this->form_validation->run() == false){
				
					// Define vars for failure.		
					$dati['vcode'] = $vcode;
					$dati['email'] = isset($invite['email']) ? $invite['email'] : '';
				
					// Captcha Code.
					$expiration = time() - 300; // Two hour limit
					$this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);
					$vals = array(
								//'word'     => 'Random word',
								'img_path'   => './captcha/',
								'img_url'    => base_url().'captcha/',
								'font_path'  => './system/fonts/arial.ttf',
								'img_width'  => '208',
								'img_height' => '60',
								'expiration' => '3600'
							);
		
					$cap = $this->captcha->create_captcha($vals);
					
					// Set the data for the page.
					$dati['image'] = $cap['image'];
					//mette nel db
					$data = array(
								'captcha_id'    => '',
								'captcha_time'    => $cap['time'],
								'ip_address'    => $this->input->ip_address(),
								'word'            => $cap['word']
							);
		
					$query = $this->db->insert_string('captcha', $data);
					$this->db->query($query);
				
					// Validation failed OR form not submitted, show form.
					$this->load->view('users/signup', $dati);
				} else {
					// Validation passed. Process form.
					
					// Create user.
					$this->user->create();
					
					// Generate verification code.
					$verification_code = md5($this->config->item('private_key').$_POST['email']);
					
					// Send verification email to user.
					$this->load->library('email');
					$this->email->to($_POST['email']);
					if($_SERVER['REMOTE_ADDR'] != '127.0.0.1') $this->email->bcc('price.aaron@gmail.com');
					$this->email->from($this->config->item('system_email'), $this->config->item('title'));
					$this->email->subject('Email Verification');
					$this->email->message(
						"Hi there,\r\n\r\n".
						"Please verify your email by clicking on the following link:\r\n".
						"http://".$_SERVER['SERVER_NAME']."/users/".$_POST['username']."/verify/".$verification_code."\r\n\r\n".
						"Thank you,\r\n\r\n".
						$this->config->item('title')."\n".
						$this->config->item('base_url')
					);
					
					$this->email->send();
					
					
					// Get mail address.
					$check_mail = '';
					$email_domain = strtolower(substr(strrchr($_POST['email'], '@'), 1));
					switch($email_domain){
						case 'gmail.com'		: $check_mail = ' Go to: <a href="http://www.gmail.com">http://www.gmail.com</a>.'; break;
						case 'hotmail.com'		: $check_mail = ' Go to: <a href="http://www.hotmail.com">http://www.hotmail.com</a>.'; break;
						case 'hotmail.co.uk'	: $check_mail = ' Go to: <a href="http://www.hotmail.co.uk">http://www.hotmail.co.uk</a>.'; break;
						case 'hotmail.ca'		: $check_mail = ' Go to: <a href="http://www.hotmail.ca">http://www.hotmail.ca</a>.'; break;
						case 'yahoo.com'		: $check_mail = ' Go to: <a href="http://mail.yahoo.com">http://mail.yahoo.com</a>.'; break;
						case 'yahoo.co.uk'		: $check_mail = ' Go to: <a href="http://mail.yahoo.co.uk">http://mail.yahoo.co.uk</a>.'; break;
						case 'yahoo.ca'			: $check_mail = ' Go to: <a href="http://mail.yahoo.ca">http://mail.yahoo.ca</a>.'; break;
						case 'trinidad.net'		: $check_mail = ' Go to: <a href="http://webmail.trinidad.net">http://webmail.trinidad.net</a>.'; break;
						case 'shaw.ca'			: $check_mail = ' Go to: <a href="http://webmail.shaw.ca">http://webmail.shaw.ca</a>.'; break;
						case 'khalsa.com'		: $check_mail = ' Go to: <a href="http://mail.google.com/hosted/khalsa.com">http://mail.google.com/hosted/khalsa.com</a>.'; break;
						case 'pixelstation.com'	: $check_mail = ' Go to: <a href="http://mail.google.com/a/pixelstation.com">http://mail.google.com/a/pixelstation.com</a>.'; break;
						case 'tstt.net.tt'		: $check_mail = ' Go to: <a href="http://webmail.tstt.net.tt/src/login.php">http://webmail.tstt.net.tt/src/login.php</a>.'; break;
						case 'wow.net'			: $check_mail = ' Go to: <a href="http://www.wow.net">http://www.wow.net</a>.'; break;
						default					: $check_mail = ''; break;
					}
					
					
					// Get random success.
					$success_quotes = $this->lang->line('success');
					$success_quote = "Hooray!";
					
					if(is_array($success_quotes)) {
						$success_quote = $success_quotes[array_rand($success_quotes)];
					}
					
					
					// User is not verified. Set message then redirect to home page.
					$this->messages->add('<h4 class="message-header">'.$success_quote.'</h4><p class="message-detail">Sign up was successful. An email has been sent to <strong>'.$_POST['email'].'</strong> for verification of your email address.'.$check_mail.'</p>');
					
					// Redirect to verification page.
					redirect();
				}
//			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function suitable($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('suitable', '"'.$field.'" cannot be used.');
		
		$unsuitable = array('admin', 'administrator', 'anon', 'anonymous', 'shoutt', 
							'user', 'users', 'util', 'errors', 'error', 'messages', 
							'message', 'pagination', 'image', '_remap', 'authenticated', 
							'banned', 'captcha_check', 'captcha', 'favorite', 'favorites', 
							'history', 'index', 'ip_banned', 'log', 'login', 'logout', 
							'recover', 'signup', 'suitable', 'tech_details', 'toggle_ban', 
							'banned', 'ban', 'valid_date', 'verified', 'verify', 'settings', 
							'config', 'configuration', 'join', 'register', 'delete',
							'all', 'public', 'service', 'awareness', 'crime', 'murder', 
							'kidnapping', 'burglary', 'robbery', 'assault', 'other_crime', 
							'politics', 'elections', 'business', 'economy', 'finance', 
							'lifestyle', 'culture', 'health', 'safety', 'environment', 
							'pollution', 'opinion', 'features', 'people', 'arts', 'entertainment', 
							'sports', 'cricket', 'soccer', 'other_sports', 'misc', 'miscellaneous', 
							'comedy', 'delete_image', 'vote', 'article', 'assess', 'comment', 
							'compose', 'delete', 'delete_image', 'edit', 'favorite', 'filter', 
							'images', 'period_of_time', 'perspective', 'popular', 'report_abuse', 
							'submit', 'top_in', 'undelete', 'upcoming', 'upload', 'test',
							/*'example', */'demo', 'inactive');
		
		$function_filter = word_censor($field, $unsuitable, '*');
		$profanity_filter = word_censor($field, $this->config->item('profanity_words'), '*');
		
		return (strpos($function_filter, '*') !== false || 
				strpos($profanity_filter, '*') !== false) ? false : true;
	}
	
	
	
	

	
	
	
	
	
	
	
	
	
	function tech_details() {
		// Get username from URI
		$url_username = $this->uri->segment(2);
		
		// Check if username exists.
		if(empty($url_username)) {
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		} else {
			
			if($this->util->user_is_admin()){
			
				// Get user details.
				$info = $this->user->info_from_column('username', $url_username);
			
				// Show Admin
				$data = array(
							'url_username' 	=> $url_username,
							'user_id'		=> $info['id'],
							'info'			=> $info,
							'tech_report'	=> $this->user->get_tech_report_where(array('user_id' => $info['id'])),
							'show_ban'		=> ($this->util->user_is_admin() && !$this->util->user_is_admin($info['id']) /* && $user['id'] != $this->session->userdata($this->config->item('session_key').'_usr')*/)
						);
						
				$this->load->view('users/tech_details', $data);
			} else {
				// User doesn't exist.
				$this->errors->set("The page you are looking for is not here.");
				redirect('error');
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function toggle_ban() {
		// Get username from post.	
		$user_id = $_POST['user_id'];
		
		$user = $this->user->info_from_column('id', $user_id);
		
		if(empty($user_id)){
			echo "Sorry, I just don't feel like it.";
		} else {
		
			if(count((array) $user) > 0){
				// Delete the story.
				$this->user->toggle_restrict_access($user_id);
				
				// Return message to the user.
				echo '';
			} else {
				// User has no business here.
				echo "I can't seem to find this user anywhere.";
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function valid_date($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('valid_date', 'Please choose a valid date.');
		
		$date = $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];
		
		if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $date, $regs)) {
			if ($regs[2] > 12) {
				return false;
			} else if (($regs[2] == '09' || $regs[2] == '04' || $regs[2] == '06' || $regs[2] == '11') && $regs[3] > '30') {
				return false;
			} else if (($regs[2] == '01' || $regs[2] == '03' || $regs[2] == '05' || $regs[2] == '07' || $regs[2] == '08' || $regs[2] == '10' || $regs[2] == '12') && $regs[3] > '31') {
				return false;
			} else if($regs[2] == '02' && $regs[3] > '29') {
				return false;
			} else if($regs[2] == '02' && $regs[3] > '28' && ($regs[1] % 4 > 0)) {
				return false;
			} else return true;
			
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function verified($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('verified', 'Please verify your email address before you login.');
		// See if the user exists and their password is correct.
		return ($this->user->is_verified($_POST['username'])) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function verify() {
		
		// Get username from URI.
		$url_username = $this->uri->segment(2);
		// Attempt to get verification code from URI. Only happens when user clicks on email.
		$verification_code = $this->uri->segment(4);
		
		// Don't verify users more than once.
		if($this->user->is_verified($url_username)) {
			// Set session vars.
			$user = $this->user->info_from_column('username', $url_username);
			
			$this->session->set_userdata(array(
				$this->config->item('session_key').'_usr' => $user['id'],
				$this->config->item('session_key').'_uid' => md5($this->config->item('private_key').$user['username']),
				$this->config->item('session_key').'_unm' => $user['username']
			));
		
			// Send them straigth to their profile.
			redirect('users/'.$url_username);
		} else {
			// Check code is correct.
			if($this->user->entered_correct_verification_code($url_username, $verification_code)) {
				
				// Verify user.
				$this->user->verify_user($url_username);
				
				// Set session vars.
				$user = $this->user->info_from_column('username', $url_username);
				
				$this->session->set_userdata(array(
					$this->config->item('session_key').'_usr' => $user['id'],
					$this->config->item('session_key').'_uid' => md5($this->config->item('private_key').$user['username']),
					$this->config->item('session_key').'_unm' => $user['username']
				));
				
				
				// Get random success.
				$success_quotes = $this->lang->line('success');
				$success_quote = "Hooray!";
				
				if(is_array($success_quotes)) {
					$success_quote = $success_quotes[array_rand($success_quotes)];
				}
				
				// User is not verified. Set message then redirect to home page.
				$this->messages->add('<h4 class="message-header">'.$success_quote.'</h4><p class="message-detail">Your email address has been verified and you are already logged in, so go ahead, start shoutting.</p>');
				
				// Send user to the profile.
				redirect('users/'.$url_username);
			} else {
				// Wrong code.
				$this->errors->set('The verification code you entered is incorrect.');
				redirect('error');
			}
		}
	}
}
?>