<?php

class Settings extends Controller {

	var $user_id = '';
	var $username = '';

	function Settings() {
		parent::Controller();
		
		// Load User Model
		$this->load->model('user');
		
		// Load the util Library.
		$this->load->library('util');
		
		// Load Messages Library to pass session messages when required.
		$this->load->library('messages');
		
		// Redirect to proper domain.
		$this->util->domain_redir();
		
		// Check user is logged in.
		$this->util->check_user_logged_in();
		
		// Get username from session
		$this->user_id = $this->session->userdata($this->config->item('session_key').'_usr');
		$this->username = $this->session->userdata($this->config->item('session_key').'_unm');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function about() {
		// Set rules for validation
		$this->form_validation->set_rules('name', 		'Full Name', 		'');
		$this->form_validation->set_rules('gender', 	'Gender', 			'');
		$this->form_validation->set_rules('day', 		'Day', 				'required');
		$this->form_validation->set_rules('month', 		'Month', 			'required');
		$this->form_validation->set_rules('year', 		'Year', 			'required|callback_valid_date');
		$this->form_validation->set_rules('location',	'Location', 		'');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		// Get user info
		$user = $this->user->info_from_column('id', $this->user_id);
		
		// Set data
		$data = array(
			'user_info'	=> $user
		);
		
		// Run validation rules.
		if($this->form_validation->run() == false){
		
			// Validation failed OR form not submitted, show form.
			$this->load->view('settings/about', $data);
		} else {
			// Validation passed.
			
			// Update User Info.
			$this->user->save_info($this->user_id);
			
			// Set message for user.
			$this->messages->add('Your information has been saved.');
			
			// Redirect to profile will message.
			redirect('settings/about');
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function check_password($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('check_password', 'Password you entered is incorrect.');
		// See if the user exists and their password is correct.
		return ($this->user->is_authentic($this->username, $field)) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function delete_invite(){
		
		$id = $_POST['id'];
		
		if(empty($id)) {
			echo "Sorry, I just don't feel like it.";
		} else {
			// Delete the invitation.
			$this->user->delete_invite();
			
			echo '';
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function email() {
	
		// Set rules for validation
		$this->form_validation->set_rules('on_comment', 'comment', 	'required');
		$this->form_validation->set_rules('on_reply', 	'reply', 	'required');
		$this->form_validation->set_rules('on_news', 	'news', 	'required');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$email_settings = $this->user->settings('email', $this->session->userdata($this->config->item('session_key').'_usr'));
		
		// Get user info
		$user = $this->user->info_from_column('id', $this->user_id);
		
		// Put the username in the session for the menu.
		$data = array(
			'settings'	=> $email_settings,
			'user_info'	=> $user
		);
		
		// Run validation rules.
		if($this->form_validation->run() == false){
			$this->load->view('settings/email', $data);
		} else {
			// Passed validation, save settings
			
			// Save changes to database
			$this->user->save_email_settings();
			
			// Acknowledge that the settings have been saved.
			$this->messages->add('Email Preferences have been saved.');
			
			// Redirect user to same page.
			redirect('settings/email');
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function index() {
		$this->about();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function invite() {
		
		// Get inviter user_id
		$user_id = $this->user_id;
		
		// Get Sender email address.
		$sender_info = $this->user->info_from_column('id', $user_id);
		
		// Get email addresses to be sent.
		$email = trim($_POST['email'], ',');
		
		$emails = explode(',', $email);
		
		foreach($emails as $i => $e) {
			if(trim($e) == ''){
				unset($emails[$i]);
			}
		}
		
		$emails = array_values($emails);
	
		// Define Validation rules.
		$this->form_validation->set_rules('email', 'Email', 'required|valid_emails|callback_valid_remaining['.(count($emails) - 1).']');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		
		// Run validation rules.
		if($this->form_validation->run() == false){
			// Get invites remaining for user.
			$remaining = $this->user->invites_remaining($user_id);
			
			// Display error message.
			echo '{ remaining: "'.$remaining.'", message: "'.addslashes(form_error('email')).'" }';
		} else {
			// Validation passed, process form.
			
			// Catch errors.
			$invalid_email = array();
			$registered = array();
			$message = '';
			
			// Send email to user.
			$this->load->library('email');
			
			foreach($emails as $e) {
			
				$email = trim($e);
				
				if(!$this->form_validation->valid_email($email)){
					array_push($invalid_email, $email);
				} else if(!$this->form_validation->unique($email, 'users.email')){
					array_push($registered, $email);
				} else {
			
					// Insert invite record in Database.
					$vcode = $this->user->invite($email);
					
					$this->email->clear();
					$this->email->from($this->config->item('system_email'), $this->config->item('title'));
					$this->email->to($email);
					$this->email->subject('Invitation to shoutt!');
					$this->email->message(
											"Hi there,\r\n\n".
											$sender_info['email']." has invited you to join shoutt!\r\n\r\n".
											"It's a social news service for Trinidad and Tobago. ".
											"It lets you become your own reporter of the issues affecting ".
											"you on a daily basis in Trinidad and Tobago.\r\n\r\n".
											"Pinpoint the location of your story, upload photos and share videos. ".
											"Also read and vote on stories that other people have posted.\r\n\r\n".
											"Sign up here: http://".$_SERVER['SERVER_NAME']."/signup/".$vcode."\r\n\r\n".
											"This invitation expires in 30 days (on ".date('M d, Y', mktime(0, 0, 0, date("m")  , date("d")+30, date("Y"))).").\r\n\r\n".
											$this->config->item('title')."\n".
											$this->config->item('base_url')
										);
					$this->email->send();
				}
			}
			
			$num_invalid_emails = count($invalid_email);
			$num_registered = count($registered);
			
			if($num_invalid_emails > 0) {
				foreach($invalid_email as $key => $i){
					
					$message .= '"'.$i.'"';
					$message .= ($key == ($num_invalid_emails - 2)) ? ' and ' : ', ';
					
				}
				
				$isare = ($num_invalid_emails == 1) ? ' is ' : ' are ';
				$message = rtrim($message, ', ').$isare.' invalid';
				$message .= ($num_invalid_emails > 0 && $num_registered > 0) ? ' and ' : '. ';
			}
			
			if($num_registered > 0) {
				foreach($registered as $key => $i){
					
					$message .= '"'.$i.'"';
					$message .= ($key == ($num_registered - 2)) ? ' and ' : ', ';
					
				}
				
				$isare = ($num_registered == 1) ? ' is' : ' are';
				$message = rtrim($message, ', ').$isare.' already registered. ';
			}
			
			if(count((array) $emails) > ($num_registered + $num_invalid_emails) && ($num_registered + $num_invalid_emails > 0))
				$message .= 'All other invitations were sent.';
			
			if(empty($message))
				$message = 'Sent.';
			
			
			// Get invites remaining for user.
			$remaining = $this->user->invites_remaining($user_id);
			echo '{ remaining: "'.$remaining.'", message: "'.addslashes($message).'" }';
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function invitations() {
		
		// Get user info
		$user = $this->user->info_from_column('id', $this->user_id);
		
		// Set data
		$data = array(
			'user_info'	=> $user,
			'invitations'	=> $this->user->get_invitations($user['id'])
		);
		
		$this->load->view('settings/invitations', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function password() {
		// Set rules for validation
		$this->form_validation->set_rules('old', 		'Username', 		'required|callback_check_password');
		$this->form_validation->set_rules('new', 		'Password', 		'required|min_length[5]');
		$this->form_validation->set_rules('confirm',	'Confirm Password', 'required|matches[new]');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		// Get user info
		$user = $this->user->info_from_column('id', $this->user_id);
		
		// Set data
		$data = array(
			'user_info'	=> $user
		);
		
		// Run validation rules.
		if($this->form_validation->run() == false){
		
			// Validation failed OR form not submitted, show form.
			$this->load->view('settings/password', $data);
		} else {
			// Validation passed.
			
			// Set password.
			$this->user->set_password($this->user_id, $_POST['new']);
			
			// Set message for user.
			$this->messages->add('Your password has been changed.');
			
			// Redirect to profile will message.
			redirect('users/'.$this->username);
		}
	}
	
	
	
	
	
	
	
	
	
	
	function resend_invite() {
		
		// Get invite id from post.
		$inv_str = $_POST['inv_id'];
		
		$inv_id = explode('_', $inv_str);
		
		// Get invite record from database.
		$invite = $this->user->get_invite($inv_id[1]);
		
		// Get Sender email address.
		$sender_info = $this->user->info_from_column('id', $invite['user_id']);
		
		// Send email to user.
		$this->load->library('email');
		$this->email->clear();
		$this->email->from($this->config->item('system_email'), $this->config->item('title'));
		$this->email->to($invite['email']);
		$this->email->subject('Invitation to shoutt!');
		$this->email->message(
								"Hi there,\r\n\n".
								$sender_info['email']." has invited you to join shoutt!\r\n\r\n".
								"It's a social news service for Trinidad and Tobago. ".
								"It lets you become your own reporter of the issues affecting ".
								"you on a daily basis in Trinidad and Tobago.\r\n\r\n".
								"Pinpoint the location of your story, upload photos and share videos. ".
								"Also read and vote on stories that other people have posted.\r\n\r\n".
								"Sign up here: http://".$_SERVER['SERVER_NAME']."/signup/".$invite['vcode']."\r\n\r\n".
								"This invitation expires in 30 days (on ".date('M d, Y', mktime(0, 0, 0, date("m"), date("d")+30, date("Y"))).").\r\n\r\n".
								$this->config->item('title')."\n".
								$this->config->item('base_url')
							);
		$this->email->send();
		
		// Set the date send to right now.
		$this->user->update_invite_date($inv_id[1]);
		
		echo 'Sent';
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function valid_remaining($field, $param='0') {
		// Set the failure message if required.
		$this->form_validation->set_message('valid_remaining', "You don't have enough invites.");
		// Check that number of invites remaining is greater than 0.
		return ($this->user->has_invites($_POST['user_id'], $param)) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function viewing() {
	
		// Set rules for validation
		$this->form_validation->set_rules('profanity', 		'profanity', 			'required');
		$this->form_validation->set_rules('openextlinks', 	'open external links', 	'required');
		$this->form_validation->set_rules('openstorylinks', 'open story links', 	'required');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$view_settings = $this->user->settings('view', $this->session->userdata($this->config->item('session_key').'_usr'));
		
		// Get user info
		$user = $this->user->info_from_column('id', $this->user_id);
		
		// Put the username in the session for the menu.
		$data = array(
			'settings'	=> $view_settings,
			'user_info'	=> $user
		);
		
		// Run validation rules.
		if($this->form_validation->run() == false){
			$this->load->view('settings/viewing', $data);
		} else {
			// Passed validation, save settings
			
			// Save changes to database
			$this->user->save_view_settings();
			
			// Acknowledge that the settings have been saved.
			$this->messages->add('Viewing Preferences have been saved.');
			
			// Redirect user to same page.
			redirect('settings/viewing');
		}
	}
}
?>