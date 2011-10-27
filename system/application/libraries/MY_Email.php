<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class MY_Email extends CI_Email {

	function MY_Email() {
		parent::CI_Email();
	}
	
	
	function send(){
	
		// Get the to array for processing.
		$to = array_merge((array) $this->_recipients, (array) $this->_cc_array, (array) $this->_bcc_array);
		
		// Localhost addresses are different cuz I'm limited to internal email addresses.
		if($_SERVER['REMOTE_ADDR'] != '127.0.0.1'){
			
			// Go ahead and send the email.
			parent::send();
				
		} else if(count($to) == 1 && $to[0] == 'newuser@localhost') {
			// Go ahead and send the email.
			parent::send();
		}
	}
}
?>