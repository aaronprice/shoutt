<?php 

class Error extends Controller {
	
	function Error() {
		parent::Controller();
		
		// Load the session class.
		$this->load->library('session');
		$this->load->library('util');
		$this->lang->load('error', 'english');
	}
	
	
	function index() {
	
		// Get Heading.
		$headings = $this->lang->line('error');
		// Set default heading.
		$heading = 'Oops!';
		if(is_array($headings)) {
			$heading = $headings[array_rand($headings)];
		}
		
		// Get Message.
		$message = $this->session->userdata('error_message');
		// Set default message.
		if(empty($message)) {
			session_start();
		
			$message = isset($_SESSION['error_message']) ? 
				$_SESSION['error_message'] :
				"I'm not really sure what to tell you, but try <a href=\"/\">starting over</a> or <a href=\"/contact\">report a bug</a>.";
		}
		
		$data = array(
					'message' => $message,
					'heading' => $heading
				);
				
		// Redirect to proper domain after the error message is set.
		$this->util->domain_redir();
		
		$this->load->view('error', $data);
	}
}
?>