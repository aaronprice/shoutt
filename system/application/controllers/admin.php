<?php 

class Admin extends Controller {
	
	function Admin() {
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
		
		// Find out of the current user (session) has access to this. 
		if(!$this->util->user_is_admin()){
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		}
	}
	
	
	
	function index() {
	
		// Get username from session
		$session_username = $this->session->userdata($this->config->item('session_key').'_unm');
	
		$data = array(
					'session_username' => $session_username
				);
	
		$this->load->view('admin/profile', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function abuse() {
		$config['base_url'] = '/admin/abuse';
		$config['total_rows'] = $this->story->count_abuse_where();
		$config['per_page'] = '5';
		$config['uri_segment'] = '3';
		
		$this->pagination->initialize($config);
		
		// Prepare article list for display.
		$data = array(
			'reports' => $this->story->get_abuse_where(array(), $this->pagination->limit(), $this->pagination->offset()),
			'user_id' => $this->session->userdata($this->config->item('session_key').'_usr')
		);
		
		// Show article list.
		$this->load->view('admin/abuse', $data);
	}
	
	
	
	
	
	
	
	
	function delete_thumbnails() {
		
		$this->util->empty_thm_folder($_SERVER['DOCUMENT_ROOT'].'/img');
	
		echo 'Thumbnails deleted.';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function ignore_report() {
	
		$user_id = $_POST['token'];
		$id_str = $_POST['id'];
		
		$id_arr = explode('_', $id_str);
		$id = $id_arr[2];
		
		$user = $this->user->info_from_column('id', $user_id);
		
		if(empty($user_id)){
			echo "Sorry, I just don't feel like it.";
		} else {
		
			if($this->user->is_authorized($user['type'])){
				// Ignore report.
				$this->story->ignore_report($id);
				
				// Return message to the user.
				echo '';
			} else {
				// User has no business here.
				echo "I just can't do it captain... I haven't got the power.";
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function replenish_invites() {
		$this->user->replenish_invites();
		
		echo 'Invites replenished.';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function trash() {
		$config['base_url'] = '/admin/trash';
		$config['total_rows'] = $this->story->count_trash_where();
		$config['per_page'] = '5';
		$config['uri_segment'] = '3';
		
		$this->pagination->initialize($config);
		
		// Prepare article list for display.
		$data = array(
			'stories' => $this->story->get_trash_where(array(), $this->pagination->limit(), $this->pagination->offset()),
			'user_id' => $this->session->userdata($this->config->item('session_key').'_usr')
		);
		
		// Show article list.
		$this->load->view('admin/trash', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function users() {
		$config['base_url'] = '/admin/users';
		$config['total_rows'] = $this->user->count_where();
		$config['per_page'] = '50';
		$config['uri_segment'] = '3';
		
		$this->pagination->initialize($config);
		
		// Prepare article list for display.
		$data = array(
			'users' => $this->user->get_where(array(), $this->pagination->limit(), $this->pagination->offset())
		);
		
		// Show article list.
		$this->load->view('admin/users', $data);
	}
}
?>