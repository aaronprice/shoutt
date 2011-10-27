<?php 

class Search extends Controller {

	function Search() {
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
		
		// Load the language for Categories
		$this->lang->load('category', 'english');
		
		// Redirect to proper domain.
		$this->util->domain_redir();
		
		
		// User Info
		$this->user_info = array(
								 'id' 					=> $this->session->userdata($this->config->item('session_key').'_usr'),
								 'user_is_admin' 		=> $this->util->user_is_admin(),
								 'user_is_logged_in' 	=> $this->user->is_logged_in()
								 );
	}
	
	
	
	
	
	
	
	
	function _remap($method) {
		if(substr($method, 0, 4) == 'page'){
			$this->index();
		} else {
			$this->$method();
		}
	}
	
	
	
	
	
	
	
	
	
	function index(){
		
		$query = $this->input->get('q');
		$tokens = $this->util->get_tokens($query);
		
		$config['base_url'] = '/search';
		$config['total_rows'] = $this->story->count_search_results($tokens);
		$config['per_page'] = $this->config->item('num_stories_per_page');
		$config['uri_segment'] = '2';
		
		$this->pagination->initialize($config);
	
		$data = array(
					'q' 				=> $query,
					'stories'			=> $this->story->search_results($tokens, $this->pagination->limit(), $this->pagination->offset()),
					'votes'				=> $this->story->get_story_votes_where(array('view' => '1'), $this->pagination->limit(), $this->pagination->offset()),
					'user_info'			=> $this->user_info
				);
		
		$this->load->view('search/list', $data);
	}
}
?>