<?php 

class Terms extends Controller {


	function Terms() {
		parent::Controller();
		
		// Load the util Library.
		$this->load->library('util');
	}
	
	
	function index() {
		$this->load->view('legal/terms');
	}
}
?>