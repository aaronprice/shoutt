<?php
class User extends Model {





	function User(){
		// Call parent class. Required by PHP if contructor is Instantiated.
		parent::Model();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function click($profile_user_id) {
		// Prepare data for insertion.
		$data = array(
					'profile_user_id'		=> $profile_user_id,
					'viewer_user_id'		=> $this->session->userdata($this->config->item('session_key').'_usr'),
					'date_viewed'	=> time(),
					'ip'			=> $_SERVER['REMOTE_ADDR'],
					'user_agent'	=> $_SERVER['HTTP_USER_AGENT']
				);
		// Insert data.
		$this->db->insert('profile_views', $data);
	}
	
	
	
	
	
	
	
	
	
	

	
	
	
	
	
	function create() {
		// Prepare inputs for insertion into database.
		$data = array(
					'username' 			=> $_POST['username'],
					'pwd' 				=> md5($_POST['password']),
					'email' 			=> $_POST['email'],
					'dob'				=> $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'],
					'type' 				=> 100,
					'status' 			=> 0,
					'dateregistered' 	=> time(),
					'ip'				=> $_SERVER['REMOTE_ADDR'],
					'user_agent'		=> $_SERVER['HTTP_USER_AGENT']
				);
				
		// Insert values into database and get id for furture inserts.
		$this->db->insert('users', $data);
		$user_id = $this->db->insert_id();
		
		
		// Create View Settings
		$view = array(
					'user_id' 			=> $user_id,
					'profanity' 		=> '0',
					'openextlinks' 		=> '0',
					'openstorylinks' 	=> '0'
				);
				
		// Insert values into database.
		$this->db->insert('view_settings', $view);
		
				
		// Create Email Settings
		$email = array(
					'user_id' 			=> $user_id,
					'on_comment' 		=> '1',
					'on_reply' 			=> '1',
					'on_news' 			=> '1'
				);
				
		// Insert values into database.
		$this->db->insert('email_settings', $email);
		
		
		// Create Invites
		$invites = array(
					'user_id' 			=> $user_id,
					'remaining' 		=> $this->config->item('num_invites_per_person')
				);
				
		// Insert values into database.
		$this->db->insert('user_invites', $invites);
		
		
		// Delete row from Trash table.
		$this->db->where('email', $_POST['email']);
		$this->db->delete('invites');
		
		// Optimize the table.
		$this->load->dbutil();
		$this->dbutil->optimize_table('invites');
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function delete_invite() {
		// Get the user's record.
		$query = $this->db->get_where('invites', array('id' => $_POST['id']));
		
		// If the record exists, proceed.
		if($query->num_rows() > 0){
			// Get the first row (should be the only one).
			$row = $query->row_array();
			
			// Give the user back his/her invite.
			$this->db->query("UPDATE user_invites
							  SET remaining = remaining + 1
							  WHERE user_id = '".$row['user_id']."'");
			
			// Delete the invitation.
			$this->db->where('id', $row['id']);
			$this->db->delete('invites');
		
			// Optimize the table.
			$this->load->dbutil();
			$this->dbutil->optimize_table('invites');
			
		} else return false;
			
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	function entered_correct_verification_code($username, $verification_code){
		// Get the user's record.
		$query = $this->db->get_where('users', array('username' => $username));
		
		// If the record exists, proceed.
		if($query->num_rows() > 0){
			// Get the first row (should be the only one).
			$row = $query->row();
			
			// Verify code and return.
			return ($verification_code == md5($this->config->item('private_key').$row->email)) ? true : false;
			
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	function get_id($username){
		// Get the id of a user given their username
		$this->db->select('id');
		$query = $this->db->get_where('users', array('username' => $username));
		
		$row = $query->row();
		return $row->id;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function count_activity_where($where=array()) {
		$this->db->where($where);
		return $this->db->count_all_results('activity');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function count_log_where($where=array()) {
		$this->db->where($where);
		return $this->db->count_all_results('user_log');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function count_where($where=array()) {
		$this->db->where($where);
		return $this->db->count_all_results('users');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_activity_where($where=array(), $limit='', $offset='') {
		
		$this->db->where($where);
		$this->db->where('view <', '2');
		$this->db->limit($limit, $offset);
		$query = $this->db->get('activity');
		
		if($query->num_rows() > 0){
			return $query->result_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	function get_invite($inv_id) {
		// Get record for user.
		$query = $this->db->get_where('invites', array('id' => $inv_id));
		
		if($query->num_rows() > 0) {
			return $query->row_array();
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	function get_invitations($user_id) {
		$this->db->where('user_id', $user_id);
		$this->db->order_by('email');
		$this->db->group_by('email');
		$query = $this->db->get('invites');
		
		if($query->num_rows() > 0){
			return $query->result_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_log_where($where=array(), $limit='', $offset='') {
		$this->db->where($where);
		$this->db->limit($limit, $offset);
		$this->db->order_by('id', 'desc');
		$query = $this->db->get('user_log');
		
		if($query->num_rows() > 0){
			return $query->result_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_activity_stats($user_id) {
		
		$this->db->select('type');
		$this->db->select('COUNT(*) AS activity_count');
		$this->db->where('user_id', $user_id);
		$this->db->group_by('type');
		$query = $this->db->get('activity');
		
		// Declare final array.
		$arr = array(
					 'comment' 		=> 0,
					 'submission' 	=> 0,
					 'vote' 		=> 0,
					 'favorite' 	=> 0
					 );
		
		if($query->num_rows() > 0){
			$result_array = $query->result_array();
			
			foreach($result_array as $row){
				$arr[$row['type']] = $row['activity_count'];
			}
			return $arr;
		} else return $arr;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_applicable_author_info($type, $type_id) { 
		$table = '';
	
		switch($type) {
			case 'story':
				$this->db->join('email_settings', 'email_settings.user_id = stories.user_id');
				$this->db->join('users', 'users.id = stories.user_id');
				$this->db->group_by('stories.id');
				$this->db->where('email_settings.on_comment', '1');
				$this->db->where('stories.id', $type_id);
				$table = 'stories';
				break;
			case 'comment':
				$this->db->join('email_settings', 'email_settings.user_id = stories.user_id');
				$this->db->join('comments', 'comments.story_id = stories.id');
				$this->db->join('users', 'users.id = comments.user_id');
				$this->db->group_by('comments.id');
				$this->db->where('email_settings.on_reply', '1');
				$this->db->where('comments.id', $type_id);
				$table = 'stories';
				break;
			default: break;
		}
		
		$query = $this->db->get($table);
		
		if($query->num_rows() > 0){
			// Get email address
			return $query->row_array();
		} return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_where($where=array(), $limit='', $offset='') {
	
		$this->db->select('u.*');
		$this->db->select('(SELECT MAX(a.activity_time) FROM activity a WHERE a.user_id = u.id) AS activity_time');
		$this->db->select('(SELECT SUM(l.score) FROM user_log l WHERE l.user_id = u.id) AS score');
		$this->db->from('users u');
		$this->db->where($where);
		$this->db->group_by('u.id');
		$this->db->order_by('u.id', 'desc');
		$this->db->limit($limit, $offset);
		$query = $this->db->get('users');
		
		if($query->num_rows() > 0){
			return $query->result_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function get_tech_report_where($where, $limit='', $offset=''){
		$this->db->distinct();
		$this->db->where($where);
		$this->db->limit($limit, $offset);
		$query = $this->db->get('tech_report');
		
		if($query->num_rows() > 0){
			return $query->result_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function has_invites($user_id, $greater_than='0') {
		$query = $this->db->get_where('user_invites', array('user_id' => $user_id, 'remaining >' => $greater_than));
		return ($query->num_rows() > 0) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function info_from_column($column, $value){
		// Get record for user.
		$query = $this->db->get_where('users', array($column => $value));
		
		if($query->num_rows() > 0) {
			return $query->row_array();
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function invite($email) {
		// Enter an invite in the invite table and return the VCode.
		$vcode = md5($email);
		
		// Prepare inputs for insertion into database.
		$data = array(
					'user_id'	=> $_POST['user_id'],
					'email' 	=> $email,
					'vcode'		=> $vcode,
					'datesent' 	=> time()
				);
				
		// Insert values into database and get id for furture inserts.
		$this->db->insert('invites', $data);
		
		// Remove one from the number of invites left.		
		$this->db->query("UPDATE user_invites 
						  SET remaining = remaining - 1 
						  WHERE user_id = '".$_POST['user_id']."'");
		
		
		// Return vcode for email.
		return $vcode;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function invite_info($vcode) {
		// Get Invite info.
		$query = $this->db->get_where('invites', array('vcode' => strval($vcode)));
		return $query->row_array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function invites_remaining($user_id) {
		$this->db->select('remaining');
		$query = $this->db->get_where('user_invites', array('user_id' => $user_id));
		$row = $query->row_array();
		return $row['remaining'];
	}
	
	
	
	
	
	
	
	
	
	
	
	

	
	
	
	
	function is_authentic($username, $password){
		// See if there are any records in the database with a given username and password.
		$where = array(
					'username' => $username,
					'pwd' => md5($password)
				 );
				 
		$query = $this->db->get_where('users', $where);
		
		if($query->num_rows() == 1) {
			// Get the row for comparison.
			$row = $query->row();
			
			// Compare using PHP because case sensativity matters.
			return ($username === $row->username && $row->pwd === md5($password)) ? true : false;
			
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function is_authorized($type, $str_type='admin') {
		// Determine if persons should be authorized to admin functions.
		switch($str_type){
			case 'admin' : return ($type == '1') ? true : false; break;
			default: return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function ip_banned($ip){
		
		$this->db->from('users u');
		$this->db->join('tech_report tr', 'u.id = tr.user_id', 'inner');
		$this->db->where('u.status', '2');
		$this->db->where('tr.ip', $ip);
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function is_banned($username){
		// See if there are any records in the database with a given username and password.
		$where = array(
					'username' => $username,
					'status' => 2
				 );
				 
		$query = $this->db->get_where('users', $where);
		
		return ($query->num_rows() == 1) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function is_logged_in(){
		// Get session values for comparison.
		$user_id 			= $this->session->userdata($this->config->item('session_key').'_usr');
		$username_unique_id = $this->session->userdata($this->config->item('session_key').'_uid');
		
		// if Session empty, you're not logged in.
		if(empty($user_id) || empty($username_unique_id))
			return false;
		else {
			// Get values from database
			$query = $this->db->get_where('users', array('id' => $user_id));
			
			// If user could not be found, you don't even exist in the database, far less logged in.
			if($query->num_rows() == 0)
				return false;
			else {
				// Get info from db for comparison.
				$row = $query->row();
				
				// Compare db value to session, and return final result.
				return ($username_unique_id == md5($this->config->item('private_key').$row->username)) ? true : false;
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function is_restricted() {
		// Get session values for comparison.
		$user_id = $this->session->userdata($this->config->item('session_key').'_usr');
		
		// Query database for user info.
		$query = $this->db->get_where('users', array('id' => $user_id));
		
		// Check if user exists.
		if($query->num_rows() > 0) {
			// Get info.
			$row = $query->row();
			
			// Return true if user is banned. else return false.
			return ((int) $row->status == 2) ? true : false;
			
		// User does not exist.	
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function is_verified($username){
		// Check the status of the user's record. Should not be 0.
		$query = $this->db->get_where('users', array(
												'username' => $username,
												'status >' => 0 
											   ));
		
		return ($query->num_rows() == 1) ? true : false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function log($user_id, $report, $score) {
		$data = array(
					'user_id' 			=> $_POST['user_id'],
					'report' 			=> $_POST['report'],
					'score' 			=> $_POST['score'],
					'date_added'		=> time()
				);
				
		// Insert values into database and get id for furture inserts.
		$this->db->insert('user_log', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function replenish_invites() {
		// Prepare for update into database.
		$set = array(
					'remaining'	=> $this->config->item('num_invites_per_person')
				);
				
		// Update values in database.
		$this->db->where('remaining <', $this->config->item('num_invites_per_person'));
		$this->db->update('user_invites', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function save_email_settings(){	
		
		$set  = array(
					'on_comment' 		=> (($_POST['on_comment'] == 'no') ? 0 : 1),
					'on_reply'		=> (($_POST['on_reply'] == 'no') ? 0 : 1),
					'on_news'	=> (($_POST['on_news'] == 'no') ? 0 : 1)
				);
		
		$this->db->where('user_id', $this->session->userdata($this->config->item('session_key').'_usr'));
		$this->db->update('email_settings', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function save_view_settings(){	
		
		$set  = array(
					'profanity' 		=> (($_POST['profanity'] == 'on') ? 0 : 1),
					'openextlinks'		=> (($_POST['openextlinks'] == 'current') ? 0 : 1),
					'openstorylinks'	=> (($_POST['openstorylinks'] == 'current') ? 0 : 1)
				);
		
		$this->db->where('user_id', $this->session->userdata($this->config->item('session_key').'_usr'));
		$this->db->update('view_settings', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function settings($type, $user_id) {
		// Get Settings by type.
		$query = $this->db->get_where($type.'_settings', array('user_id' => $user_id));
		
		if($query->num_rows() > 0){
			return $query->row_array();
		} else return array();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function toggle_restrict_access($user_id) {
		// Restricted users can only promote stories and comments, can't demote or post stories or comments.
		$this->db->select('status');
		$query = $this->db->get_where('users', array('id' => $user_id));
		
		if($query->num_rows() > 0) {
			// Get the first row.
			$row = $query->row();
		
			// Find out whether to turn on or off.
			if($row->status == '1'){	
				// Prepare inputs for insertion into database.
				$data = array(
							'status' 			=> '2'
						);
						
				// Set Conditions.
				$this->db->where('id', $user_id);
				// Update values into database.
				$this->db->update('users', $data);
				
			} elseif($row->status == '2') {
				// Prepare inputs for insertion into database.
				$data = array(
							'status' 			=> '1'
						);
						
				// Set Conditions.
				$this->db->where('id', $user_id);
				// Update values into database.
				$this->db->update('users', $data);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function set_password($username, $password) {
		// Set data.
		$data = array(
					'pwd' => md5($password)
				);
		
		// Set condition.
		$this->db->where('username', $username);
		// Do update.
		$this->db->update('users', $data);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function save_info($id) {
		// Prepare for update into database.
		$set = array(
					'name' 		=> $_POST['name'],
					'gender' 	=> $_POST['gender'],
					'dob'		=> $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'],
					'location'	=> $_POST['location']
				);
				
		// Update values in database.
		$this->db->where('id', $id);
		$this->db->update('users', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function update_invite_date($invite_id){
		// Prepare for update into database.
		$set = array(
					'datesent'	=> time()
				);
				
		// Update values in database.
		$this->db->where('id', $invite_id);
		$this->db->update('invites', $set);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function verify_user($username) {
		// Set Status to 1
		$data = array(
					'status' => 1
				);
		// Where given username is equal.
		$this->db->where('username', $username);
		// Do update.
		$this->db->update('users', $data);
	}
}
?>