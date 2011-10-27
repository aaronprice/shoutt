<?php 

class Hash {

	function Hash() {}

	function increment_value($arr, $str, $pos){
		// If the character is the last value of the array. (e.g.: d7hUnFZ)
		if($pos >= 0 && $str{$pos} == $arr[count($arr) - 1]) {
			// Increment the character in the previous position and
			// reset the character at this position to the first character in the array. (e.g.: d7hUnGb)
			return $this->increment_value($arr, substr($str, 0, $pos).$arr[0].substr($str, $pos + 1), $pos - 1);
			
		// Else the character is not the last character in the array (e.g.: d7hUnE)
		} else if($pos >= 0) {
			// Increment the character to the next value in the array. (e.g.: d7hUnF)
			return substr($str, 0, $pos).$arr[array_search($str{$pos}, $arr) + 1].substr($str, $pos + 1);
			
		// Else there is no previous character. (e.g.: ZZZ)
		} else {
			// So add a start over with a new dimension. (e.g.: bbbb)
			return $str.$arr[0];
		}
		
	}
	
	
	
	
	
	
	function get_next_increment($str) {
		// Declare the array. Removed all vowels so the hash doesn't make words.
		$arr = array(
						'b','c','d','f','g','h','j','k','l','m','n','p','q','r','s','t','v','w','x','y','z',
						'1','2','3','4','5','6','7','8','9',
						'B','C','D','F','G','H','J','K','L','M','N','P','Q','R','S','T','V','W','X','Y','Z'
					);
					
		// Return the next value in the sequence via recursion.	
		return $this->increment_value($arr, $str, strlen($str) - 1);
	}
}
?>