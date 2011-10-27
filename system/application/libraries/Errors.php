<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Errors {
	
	function Errors() {}
	
	
	
	
	
	
	function set($error_message='I have no idea what to tell you.', $error_type='error') {
		// Instantiate CodeIgniter
		$CI =& get_instance();
		// Ensure session library is loaded.
		$CI->load->library('session');
		
		// Put the required message and type into the session.
		$CI->session->set_userdata('error_message', $error_message);
		$CI->session->set_userdata('error_type', $error_type);
	}
}
?>