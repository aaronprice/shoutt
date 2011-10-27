<?php 
class Feeds extends Controller {

	function Feeds() {
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
	}
	
	
	
	
	
	
	
	function index() {
		$this->rss();
	}
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	function rss() {
		$this->load->helper('xml');
		
		$category = $this->uri->segment(2);
		$popular = $this->uri->segment(3);
		$top_in = $this->uri->segment(4);
		$data['encoding'] = 'utf-8';
        $data['feed_url'] = 'http://'.$_SERVER['SERVER_NAME'].'';
        $data['feed_name'] = $this->config->item('title').' - All News';
		$data['page_description'] = 'User Generated News for Trinidad and Tobago.';
        $data['page_language'] = 'en-us';
		
		if(!empty($category)){
			$data['stories'] = $this->story->get_where(array('category' => $category));
			$data['feed_name'] = $this->config->item('title').' - '.$this->lang->line($category);
		} else {
			$data['stories'] = $this->story->get_where();
		}
        
        header("Content-Type: application/rss+xml");
        $this->load->view('feeds/rss', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function sitemap() {
		$data['stories'] = $this->story->get_sitemap_stories();
		header("Content-Type: application/xml");
		$this->load->view('feeds/sitemap', $data);
	}
}
?>