<?php 

class Privacy extends Controller {

	function Privacy() {
		parent::Controller();
		
		
		// Load the util Library.
		$this->load->library('util');
	}
	
	
	
	
	
	
	function index() {
		$this->load->view('legal/privacy');
	}
}
?>