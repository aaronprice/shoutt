<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Util {
	
	function Util() {}
	
	
	
	
	
	
	
	
	function check_user_logged_in(){
		
		// Instantiate CodeIgniter
		$CI =& get_instance();
		$CI->load->model('user');
		
		if(!$CI->user->is_logged_in()){
			// Set session variable for where to return.
			$CI->session->set_userdata(array(
				$CI->config->item('session_key').'_rdt' => $_SERVER['REQUEST_URI']
			));
			
			// Redirect user to login page.
			redirect('login');
		}
	}
	
	
	
	
	
	
	
	
	function user_is_admin($user_id='no_user_id'){
		
		if(empty($user_id)){
			return false;
		} else {
			
			// Instantiate CodeIgniter
			$CI =& get_instance();
			$CI->load->model('user');
			
			$user = ($user_id == 'no_user_id') ? 
						$CI->user->info_from_column('id', $CI->session->userdata($CI->config->item('session_key').'_usr')) :
						$CI->user->info_from_column('id', $user_id);
			
			if(isset($user['type']) && !empty($user['type'])){
				// Check if user has the correct privileges.
				return ($CI->user->is_authorized($user['type'])) ? true : false;
			} else return false;
		}
	}
	
	
	
	
	
	
	
	
	
	function check_user_status() {
	
		// Instantiate CodeIgniter
		$CI =& get_instance();
		$CI->load->model('user');
	
		 if($CI->user->is_banned()){
			// Redirect user to login page.
			show_error('This account has been banned. As a result you are not allowed to access this page. Sorry.');
		}
	}
	
	
	
	
	
	
	
	
	
	function clean($value, $allowable_tags='', $length=0){
		
		// Instantiate CodeIgniter
		$CI =& get_instance();
		
		$value = strip_tags($value, $allowable_tags);
		$value = $CI->input->xss_clean($value);
			
		// Cut the string to match DB.
		if($length > 0){
			$value = substr($value, 0, $length);
		}
		
		// Return clean value.
		return trim($value);
	}
	

	
	
	
	
	
	
	
	
	
	
	function get_tokens($q){
		// Check parameters.
		if (!isset($q) || false === $q || !is_string($q)) return false;

        // Get the tokens from the query.
		$x = trim($q);
		if ('' === $x) return array();

		$chars = str_split($x);
		$mode = 'normal';
		$token = '';
		$tokens = array();
		$numChars = count($chars);
		for ($i = 0; $i < $numChars; ++$i) {
			switch ($mode) {
				case 'normal':
					if (  '"' == $chars[$i]  ) {
						if ( '' != $token) $tokens[] = $token;
						$token = '';
						$mode = 'quoting';
					} else if (  ' ' == $chars[$i] || "\t" == $chars[$i] || "\n" == $chars[$i]  ) {
						if ( '' != $token) $tokens[] = $token;
						$token = '';
					} else $token .= $chars[$i];	
				break;
				case 'quoting':
					if (  '"' == $chars[$i]  ) {
						if ( '' != $token) $tokens[] = $token;
						$token = '';
						$mode = 'normal';
					} else $token .= $chars[$i];
				break;
			} // switch
		} // foreach
		if ( '' != $token) $tokens[] = $token;
		return $tokens;
    }
	
	
	
	
	
	/**
	 * Creates a directory structure recursively.
	 * Obtained from: http://www.php.net/manual/en/function.mkdir.php#81656
	 * 
	 * @param string $pathname Directory structure to be created.
	 * @param int $mode UNIX Permissions
	 */
	function mkrdir($pathname, $mode=0777){
	    is_dir(dirname($pathname)) || $this->mkrdir(dirname($pathname), $mode);
	    return is_dir($pathname) || @mkdir($pathname, $mode);
	}
	
	
	
	
	
	
	
	
	/**
	 * Move all files in a directory to another directory.
	 * Obtained from: http://www.php.happycodings.com/Algorithms/code24.html
	 *
	 * @param string $src Source directory.
	 * @param string $dst Destination directory.
	 */
	function move_files($src, $dst, $recursive=true) {
		
		if(is_dir($src)){
			$handle = opendir($src);
			
			if (!is_dir($dst)) 
				// Make dest dir recursively.
				$this->mkrdir($dst);
			
			while ($file = readdir($handle)) {
				// Skips '.' and '..' dirs
				if (($file != ".") && ($file != "..")) {
					$srcm = $src."/".$file;
					$dstm = $dst."/".$file;
					
					// If another dir is found
					if (@is_dir($srcm) && $recursive) {
						// calls itself - recursive WTG
						$this->move_files($srcm, $dstm);
					} else {
						@copy($srcm, $dstm);
						// If just a copy procedure is needed
						// comment out this line
						@unlink($srcm);
					}                                             
				}
			}
			
			@closedir($handle);
			// and this one also :)
			@rmdir($src); 
		}                         
	}
	
	
	
	
	
	
	/**
	 * Used to delete an entire directory.
	 * 
	 * @param string $dirPath Directory Path
	 * @param boolean $empty Set to true if you want to keep the directory and delete everything in it.
	 * @return boolean Whether or not the delete was sucessfull.
	 */
	function delete_dir($dirPath, $empty=false) {
		if(substr($dirPath,-1) == '/')
			$dirPath = substr($dirPath,0,-1);

		@chmod($dirPath, 0777);
		if(!file_exists($dirPath) || !is_dir($dirPath)) return false;
		else if(!is_readable($dirPath)) return false;
		else {
			$handle = opendir($dirPath);
			while (false !== ($item = readdir($handle))) {
				if($item != '.' && $item != '..') {
					$path = $dirPath.'/'.$item;
					@chmod($path, 0777);
					if(is_dir($path)) $this->delete_dir($path, $empty);
					else unlink($path);
				}
			}
			closedir($handle);

			if(!$empty)
				if(!rmdir($dirPath))
					return false;
			
			return true;
		}
	}
	
	
	
	
	
	
	
	
	
	function delete_empty_dirs($dir){
		if(is_dir($dir)){
			$files = $this->list_files_and_dirs($dir);
			
			if(count($files) > 0) {
				foreach($files as $file) {
					$folder_name = str_replace('//', '/', $dir.'/'.$file);
					if(is_dir($folder_name))
						$this->delete_empty_dirs($folder_name);
				}
			} else if(count($files) == 0) {
				$this->delete_dir($dir);
				$this->delete_empty_dirs(dirname($dir));
			} else return true;
		} else return true;
	}
	
	
	
	
	
	
	
	
	
	function empty_thm_folder($folder){
		if(is_dir($folder) && $folder != $_SERVER['DOCUMENT_ROOT']){
			if ($handle = opendir($folder)) {
			
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && is_dir($folder.'/'.$file)) {
						if($file == 'thm') {
							$this->delete_dir($folder.'/'.$file);
						} else {
							$this->empty_thm_folder($folder.'/'.$file);
						}                                    
					} 
				}
				
				@closedir($handle);
			}
			return true;
		} else return false;
	}
	
	
	
	
	
	
	
	
	
	function get_random_filename($path, $ext, $length=1, $tries=1){
		$name = strtolower(random_string('alnum', $length));
		if($length == 1 && $tries >= 35) {
			return (file_exists($path.$name.$ext)) ? $this->get_random_filename($path, $ext, $length+1, $tries+1) : $name;
		} else {
			return (file_exists($path.$name.$ext)) ? $this->get_random_filename($path, $ext, $length, $tries+1) : $name;
		}
	}
	
	
	
	
	
	
	/**
	 * Returns a multi-dimensional array containing the name, size, and modified date of all files in a given directory.
	 *
	 * @param string $dir Path to directory.
	 * @return array Multi-dimensional array of files.
	 */
	function list_files($dir){
		$files = array();
		if(is_dir($dir)){
			if ($handle = opendir($dir)) {
				$i = 0;
			    while (false !== ($file = readdir($handle))) {
			        if ($file != "." && $file != ".." && !is_dir($dir.'/'.$file)) {
			            $files[$i]['name'] = $file;
			            $files[$i]['size'] = round(filesize($dir.$file));
        				$files[$i]['modified'] = filemtime($dir.$file);
        				++$i;
			        }
			    }
			    closedir($handle);
			}
		}
		
		return $files;
	}
	
	function list_files_and_dirs($dir){
		$files = array();
		if(is_dir($dir)){
			if ($handle = opendir($dir)) {
				$i = 0;
			    while (false !== ($file = readdir($handle))) {
			        if ($file != "." && $file != "..") {
			            $files[$i] = $file;
        				++$i;
			        }
			    }
			    closedir($handle);
			}
		}
		
		return $files;
	}
	
	
	
	
	
	
	function get_browser_from_user_agent($ua){
	
		$userAgent = array();
		$agent = $ua;
		$products = array();
		
		$pattern  = "([^/[:space:]]*)" . "(/([^[:space:]]*))?"
		."([[:space:]]*\[[a-zA-Z][a-zA-Z]\])?" . "[[:space:]]*"
		."(\\((([^()]|(\\([^()]*\\)))*)\\))?" . "[[:space:]]*";
		
		while( strlen($agent) > 0 ){
			if ($l = ereg($pattern, $agent, $a)){
				// product, version, comment
				array_push($products, array($a[1],    // Product
				$a[3],    // Version
				$a[6]));  // Comment
				$agent = substr($agent, $l);
			} else {
				$agent = "";
			}
		}
		
		// Directly catch these
		foreach($products as $product){
			switch($product[0]){
				case 'Firefox':
				case 'Netscape':
				case 'Safari':
				case 'Camino':
				case 'Mosaic':
				case 'Galeon':
				case 'Opera':
					$userAgent[0] = $product[0];
					$userAgent[1] = $product[1];
					break;
			}
		}
		
		if (count($userAgent) == 0){
			// Mozilla compatible (MSIE, konqueror, etc)
			if ($products[0][0] == 'Mozilla' && !strncmp($products[0][2], 'compatible;', 11)){
				$userAgent = array();
				if ($cl = ereg("compatible; ([^ ]*)[ /]([^;]*).*", $products[0][2], $ca)){
					$userAgent[0] = $ca[1];
					$userAgent[1] = $ca[2];
				} else {
					$userAgent[0] = $products[0][0];
					$userAgent[1] = $products[0][1];
				}
			} else {
				$userAgent = array();
				$userAgent[0] = $products[0][0];
				$userAgent[1] = $products[0][1];
			}
		}
		
		return $userAgent;
	}
	
	
	function get_os_from_user_agent($ua){
		$OSList = array
		  (
				  // Match user agent string with operating systems
				  'Windows 3.11' => 'Win16',
				  'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
				  'Windows 98' => '(Windows 98)|(Win98)',
				  'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
				  'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
				  'Windows Server 2003' => '(Windows NT 5.2)',
				  'Windows Vista' => '(Windows NT 6.0)',
				  'Windows 7' => '(Windows NT 7.0)',
				  'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
				  'Windows ME' => 'Windows ME',
				  'Open BSD' => 'OpenBSD',
				  'Sun OS' => 'SunOS',
				  'Linux' => '(Linux)|(X11)',
				  'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
				  'QNX' => 'QNX',
				  'BeOS' => 'BeOS',
				  'OS/2' => 'OS/2',
				  'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
		  );
		   
		  // Loop through the array of user agents and matching operating systems
		  foreach($OSList as $CurrOS => $Match)
		  {
				  // Find a match
				  if (eregi($Match, $ua))
				  {
						  // We found the correct match
						  break;
				  }
		  }
		  // You are using Windows Vista
		 return $CurrOS;
	}
	
	
	function get_age($dob)	{
		$dob = strtotime($dob);
		$age = 0;
		while( time() > $dob = strtotime('+1 year', $dob)){
			++$age;
		}
		return $age;
	}
	
	
	
	
	function domain_redir() {
		if($_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
			if($_SERVER['SERVER_NAME'] == 'www.shou.tt')
				header('Location: http://shou.tt'.$_SERVER['REQUEST_URI']);
			
			if($_SERVER['REMOTE_ADDR'] == '88.80.10.1')
				header('Location: http://www.google.com');
		}
	}
}
?>