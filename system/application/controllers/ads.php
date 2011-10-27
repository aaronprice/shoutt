<?php 

class Ads extends Controller {

	function Ads() {
		parent::Controller();
		
		// Load user model for user related activities.
		$this->load->model('user');
		
		// Load user model for user related activities.
		$this->load->model('ad');
		
		// Load the hash library.
		$this->load->library('hash');
		
		// Load the util Library.
		$this->load->library('util');
		
		// Load Messages Library to pass session messages when required.
		$this->load->library('messages');
		
		// Load the image manipulation library.
		$this->load->library('image_lib');
		
		// Load the image helper.
		$this->load->helper('image');
		
		// Load the image manipulation library.
		$this->load->library('errors');
		
		// Load the Pagination Library.
		$this->load->library('pagination');
		
		// Load the image helper.
		$this->load->helper('image');
		
	}
	
	
	
	
	
	
	function index() {
		echo "You've got ads.";
	}
	
	
	
	
	
	function check_end_date($field, $param='') {
		// Set the failure message if required.
		$this->form_validation->set_message('check_end_date', 'End date must be filled in.');
		// See if the user exists and their password is correct.
		return ($field == '2' && empty($_POST[$param])) ? false : true;
	}
	
	
	
	
	
	
	
	
	function create() {
		
		// Check user is logged in.
		$this->util->check_user_logged_in();
		
		// Get username from session.
		$session_username = $this->session->userdata($this->config->item('session_key').'_unm');
		
		// Look out for restricted users.
		$info = $this->user->info_from_column('username', $session_username);
	
		$this->form_validation->set_rules('url', 			'URL',		 	'trim|required|max_length[255]|valid_url|xss_clean');
		$this->form_validation->set_rules('title', 			'Title', 		'trim|required|max_length[25]|xss_clean');
		$this->form_validation->set_rules('description',	'Description', 	'trim|required|max_length[135]|xss_clean');
		$this->form_validation->set_rules('img_name',		'Image', 		'');
		$this->form_validation->set_rules('budget',			'Budget', 		'trim|required|numeric|greater_than_zero|xss_clean');
		$this->form_validation->set_rules('campaign_type',	'Campaign Type','trim|required|is_natural_no_zero|xss_clean');
		$this->form_validation->set_rules('duration',		'Duration', 	'trim|required|is_natural_no_zero|callback_check_end_date[end_date]|xss_clean');
		$this->form_validation->set_rules('end_date',		'End Date', 	'trim|valid_date|future_date|xss_clean');
		
		
		// Set error delimiters
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		// Vars needed on page.
		$data = array(
					'img_folder' => 'ad_'.md5($session_username)
				);
		
		// Run validation rules.
		if($this->form_validation->run() == false){
		
			// Show the submit form.
			$this->load->view('ads/create', $data);
		} else {
			// Passed Validation
			
			// Get the max hash value from the ads table.
			$max_hash = $this->ad->get_max_hash();
			
			// Get the next hash value for ads.
			$hash = $this->hash->get_next_increment($max_hash);
			
			// Check to see if there are images associated with this story.
			$session_path = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$data['img_folder'];
			
			if(file_exists($session_path)) {
				// Create a folder for files associated with this story can be stored.
				$abs_path = $_SERVER['DOCUMENT_ROOT'].'/img/ads/'.$hash;
				$this->util->mkrdir($abs_path);
				
				// Move files uploaded (if any) to permanent folder.
				$this->util->move_files($session_path, $abs_path, false);
				
				// Delete the orignal folder.
				$this->util->delete_dir($session_path);
			}
		
			// Submit ad for approval.
			$this->ad->create($hash);
			
			
			
			// If the user has enough money to cover the cost of the Ad.
			
			
			// Set message that the ad has been created.
			$this->messages->add('<h4 class="message-header">'.$success_quote.'</h4><p class="message-detail">Your ad has been created.</p>');
			// Redirect to Ad Mananger
			
			// Else
			
			// Redirect to payment form (display how much is required).
			redirect('/ads');
		}
	}
	
	
	
	
	
	
	function click() {
		
	}
	
	
	
	
	
	
	
	
	
	
	function delete_image() {
		
		// Declare vars we'll need. story_id and user_id (cuz we don't have access to the session).
		$tmp_folder	= $_POST['path'];
		
		// Parse the id to create the real filename
		
		if(empty($tmp_folder)){
			echo "Sorry, I just don't feel like it.";
		} else {
		
			// Delete the original and .
			$this->util->delete_dir($_SERVER['DOCUMENT_ROOT'].$tmp_folder.'/');
				
			// Return message as necessary.	
			echo '';
		}
	}
	
	
	
	
	
	
	
	
	
	
	function upload() {
		// Check upload.
		if($_FILES){
		
			// Define flash vars.
			$path = $_GET['folder'].'/';
			$ext = strrchr($_FILES['Filedata']['name'], '.');
			
			// Create the folder.
			$this->util->mkrdir($_SERVER['DOCUMENT_ROOT'].$path);
			
			// Set the filename.
			$new_filename = 'a';
			
			// Delete ad if exists.
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$new_filename.$ext)) {
				unlink($_SERVER['DOCUMENT_ROOT'].$path.$new_filename.$ext);
			}
			
			
			// Initialize config options for upload.
			$config['file_name'] = $new_filename;
			$config['upload_path'] = '.'.$path;
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '0';
			$config['max_width']  = '0';
			$config['max_height']  = '0';
			$config['is_flash_upload']  = true;
			$this->load->library('upload', $config);
			
			if (!$this->upload->do_upload('Filedata')){
				//$data['images_error'] .= $this->upload->display_errors();
				// Display errors.
				$errors = $this->upload->display_errors();
				if(!empty($errors))
					echo $errors;
					
			} else {
				// Get upload data.
				$upload_data = $this->upload->data();
				
				// Prepare to Resize Image.
				$config['image_library'] = 'gd2';
				$config['source_image'] = $upload_data['full_path'];
				$config['new_image'] = $upload_data['full_path']; //$upload_data['file_path'].'thm/'.$new_filename.$upload_data['file_ext'];
				$config['maintain_ratio'] = true;
				$config['width'] = 110;
				$config['height'] = 80;
				
				// Initialize the object with the following settings.
				$this->image_lib->initialize($config);
				
				// Resize the image.
				$this->image_lib->resize();
				
				// Clear the settings in preparation for other images.
				$this->image_lib->clear();
				
			} // End if upload success.
			
			
			// Display files.
			if(empty($upload_data['file_name'])) {
				echo '1';
			} else {
				// Define vars to return as JSON.
				$imgpath 	= $path.$new_filename.$upload_data['file_ext'];
				$filename 	= basename($imgpath);
				$id 		= str_replace('.', '_', $filename);
			
				echo '{ '. 
						'filename: "'.$filename.'", '.
						'id: "'.$id.'", '.
						'imgpath: "'.$imgpath.'" '.
					 '}';
			}

		} // End if files.
	}
}
?>