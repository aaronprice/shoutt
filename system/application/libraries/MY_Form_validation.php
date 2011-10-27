<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
	
	function MY_Form_validation() {
		parent::CI_Form_validation();
	}
	
	/**
	 * Validates string to URL format.
	 *
	 * @access public
	 * @param string $url URL
	 * @return boolean True if URL is valid, otherwise false.
	 */
	function valid_url($str) {
		return (preg_match('/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}((:[0-9]{1,5})?\/.*)?$/i' ,$str)) ? true : false;
	}
	
	
	/**
	 * Overwrites CodeIgniter's valid_email function and caters for local addresses and testing.
	 *  
	 * @access public
	 * @param string
	 * @return bool
	 */
	function valid_email($str){
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
			return (($str != 'newuser@localhost') && ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str))) ? false : true;
		} else {
			return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? false : true;
		}
	}
	
	
	/**
	 * Check for values that should exist.
	 *  
	 * @access public
	 * @param string
	 * @param string
	 * @return bool
	 */
	function exists($str, $val){
	
		// Instantiate CodeIgniter.
		$CI =& get_instance();

		// Split param into table and column.
		list($table, $column) = split("\.", $val, 2);

		// Find out if record exists and return result.
		$query = $CI->db->query("SELECT id 
								 FROM $table 
								 WHERE $column = '$str'");
		return ($query->num_rows == 0) ? false : true;
	}
	
	/**
	 * Check for values that should be unique.
	 *  
	 * @access public
	 * @param string
	 * @param string
	 * @return bool
	 */
	function unique($str, $val){
	
		// Instantiate CodeIgniter.
		$CI =& get_instance();

		// Split param into table and column.
		list($table, $column) = split("\.", $val, 2);

		// Find out if record exists and return result.
		$query = $CI->db->query("SELECT id 
								 FROM $table 
								 WHERE $column = '$str'");
		return ($query->num_rows == 0) ? true : false;
	}
	
	
	/**
	 * Checks for valid date based on YYYY-MM-DD format.
	 *  
	 * @access public
	 * @param string
	 * @return bool
	 */
	 function valid_date($str) {
		if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $str, $regs)) {
			if ($regs[2] > 12) {
				return false;
			} else if (($regs[2] == '09' || $regs[2] == '04' || $regs[2] == '06' || $regs[2] == '11') && $regs[3] > '30') {
				return false;
			} else if (($regs[2] == '01' || $regs[2] == '03' || $regs[2] == '05' || $regs[2] == '07' || $regs[2] == '08' || $regs[2] == '10' || $regs[2] == '12') && $regs[3] > '31') {
				return false;
			} else if($regs[2] == '02' && $regs[3] > '29') {
				return false;
			} else if($regs[2] == '02' && $regs[3] > '28' && ($regs[1] % 4 > 0)) {
				return false;
			} else return true;
			
		} else return false;
	}
	
	
	
	/**
	 * Checks if number supplied is greater than 0.
	 *  
	 * @access public
	 * @param string
	 * @return bool
	 */
	 function greater_than_zero($str) {
		if (is_numeric($str)) {
			return ($str > 0) ? true : false;
		} else return false;
	}
	
	
	
	
	
	
	/**
	 * Checks if date is in future.
	 *  
	 * @access public
	 * @param string
	 * @return bool
	 */
	 function future_date($str) {
		return (strtotime($str) > time()) ? true : false;
	}
	
	
	/**
	 * Checks if value is undefined.
	 *  
	 * @access public
	 * @param string
	 * @return bool
	 */
	 function not_undefined($str) {
		return (trim(strtolower($str)) == 'undefined') ? false : true;
	}
}
?>