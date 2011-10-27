<?php

class About extends Controller {

	function About() {
		parent::Controller();
		
		// Load the util Library.
		$this->load->library('util');
	}
	
	
	function index() {
		$this->load->view('about/about');
	}
}
?>