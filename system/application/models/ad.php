<?php 

class Ad extends Model {

	function Ad() {
		parent::Model();
	}
	
	
	
	function create($hash) {
	
		// Prepare data for insertion.
		$data = array(
					'hash'			=> $hash,
					'user_id'		=> $this->session->userdata($this->config->item('session_key').'_usr'),
					'url'			=> $_POST['url'],
					'title'			=> $_POST['title'],
					'description'	=> $_POST['description'],
					'budget'		=> $_POST['budget'],
					'used'			=> 0,
					'date_created'	=> time(),
					'end_date'		=> $_POST['end_date'],
					'camp_type'		=> $_POST['campaign_type'],
					'approved'		=> '0'
				);
		// Insert data.
		$this->db->insert('ads', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function get_max_hash() {
		$this->db->select('hash');
		$this->db->where('id = (SELECT MAX(id) FROM ads)');
		$query = $this->db->get_where('ads');
			
		// If record exists, return it.
		if($query->num_rows() > 0) {
			$row = $query->row_array();
			return $row['hash'];
		} else return '';
	}
}
?>