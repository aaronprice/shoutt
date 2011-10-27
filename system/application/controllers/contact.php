<?php 
class Contact extends Controller {
	
	function Contact() {
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
		
		// Load the Pagination Library.
		$this->load->library('pagination');
		
		// Load the image helper.
		$this->load->helper('image');
		
		// Load the image helper.
		$this->load->helper('text');
		
		// Redirect to proper domain.
		$this->util->domain_redir();
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function index(){
		
		// Load the Captcha library.
		$this->load->library('captcha');
		
		$user_logged_in = false;
		$user = array();
		
		if($this->user->is_logged_in()) {
			$user_logged_in = true;
			$user = $this->user->info_from_column('id', $this->session->userdata($this->config->item('session_key').'_usr'));
		}
		
		if($user_logged_in == false){
			$this->form_validation->set_rules('email', 			'email', 	'trim|required|valid_email|max_length[150]|xss_clean');
			$this->form_validation->set_message('exact_length', 'This smells like spam.');
			$this->form_validation->set_rules('hv', 			'spam', 	'exact_length[0]');
			$this->form_validation->set_rules('captcha',		'Captcha', 	'required|callback_captcha_check');
		}
		$this->form_validation->set_rules('name',		'name',		'trim|required|max_length[100]|xss_clean');
		$this->form_validation->set_rules('message', 	'message', 	'trim|required|xss_clean');
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		// Data for page.
		$data = array(
					'user_logged_in' 	=> $user_logged_in,
					'name'				=> isset($user['name']) ? $user['name'] : ''
				);
		
		// Run validation rules.
		if($this->form_validation->run() == false){
		
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
			$data['image'] = $cap['image'];
			//mette nel db
			$cap_data = array(
						'captcha_id'    => '',
						'captcha_time'    => $cap['time'],
						'ip_address'    => $this->input->ip_address(),
						'word'            => $cap['word']
					);

			$query = $this->db->insert_string('captcha', $cap_data);
			$this->db->query($query);
		
		
			// Show the submit form.
			$this->load->view('contact/form', $data);
		} else {
			// Validation passed. Process form.
			
			$user_info = '';
			
			// Get info based on whether logged in or not.
			if($user_logged_in == true) {
				$name = $_POST['name'];
				if(empty($name)) {
					$name = empty($user['name']) ? $user['username'] : $user['name'];
				}
				$email = $user['email'];
				$user_info = "----------------------------------------\r\n".
							 "Information from profile:\r\n".
							 "----------------------------------------\r\n".
							 $user['name']." (".$user['username'].")\r\n".
							 "http://".$_SERVER['SERVER_NAME']."/users/".$user['username']."\r\n".
							 "----------------------------------------\r\n".
							 "\r\n";
			} else {
				$name = $_POST['name'];
				$email = $_POST['email'];
			}
			
			
			// Send an email to the administrator.
			$this->load->library('email');
			$this->email->clear();
			$this->email->from('noreply@shou.tt', $name);
			$this->email->reply_to($email, $name);
			if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
				$this->email->to('newuser@localhost');
			} else {
				$this->email->to('price.aaron@gmail.com');
			}
			$this->email->subject('Contact from Shou.tt');
			$this->email->message($user_info.$_POST['message']);
			$this->email->send();
			
			
			// Redirect to success page.
			//$this->load->view('contact/success');
			
			// User is not verified. Set message then redirect to home page.
			$this->messages->add('Your message was successfully sent.');
			
			// Redirect to home page.
			redirect();
		}
	}
}
?>