<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

	/* Comment for testing purposes only. remove --> */

	function show_404($page='') {
		session_start();
		$_SESSION['error_message'] = 'The page you are looking for cannot be found.';
		header('Location: /error');
	}
	
	
	function show_error($heading, $message, $template = 'error_general') {
	
		// Make sure there's a session.
		session_start();
		
		// Set a message for all to see.
		$error_message = "The administrator is going to hate you, but it's too late, I've already sent a report of the situation.";
		
		// Show the real error only on the local server.
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
			$error_message .= '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';
		} else {
			// User Info (IP, User Agent)
			$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$self = (substr($_SERVER['REQUEST_URI'], 0, 4) == 'http') ? $_SERVER['REQUEST_URI'] : 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$user_details = "\r\n\r\nIP: ".$_SERVER['REMOTE_ADDR']."\r\nUser Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\nReferer: ".$referer."\r\nSelf: ".$self;
			@mail('price.aaron@gmail.com', 'Error', '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>'.$user_details, "From: \"SHOUTT!\" <noreply@shou.tt>\r\n");
		}
		
		// Set the error in the session.
		$_SESSION['error_message'] = $error_message;
		
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER['SERVER_NAME'].'/error')
			echo parent::show_error($heading, $message, $template);
		else
			// Redirect appropriately.
			header('Location: /error');
	}
	
	
	function show_php_error($severity, $message, $filepath, $line){
				
		// Make sure there's a session.
		session_start();
		
		// Set the message for all users to see.
		$error_message = "Something just went very wrong here, but don't panic. I know you're seeing this and I'll fix it as soon as possible.";
		
		
		// If you're testing locally, show the real message.
		if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
			$error_message .= "<p>Severity: ".$severity."<br/>".
							  "Message: ".$message."<br/>".
							  "Filepath: ".$filepath."<br/>".
							  "Line Number: ".$line."<br/>".
							  "</p>";
		} else {
			// User Info (IP, User Agent)
			$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$self = (substr($_SERVER['REQUEST_URI'], 0, 4) == 'http') ? $_SERVER['REQUEST_URI'] : 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$user_details = "\r\n\r\nIP: ".$_SERVER['REMOTE_ADDR']."\r\nUser Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\nReferer: ".$referer."\r\nSelf: ".$self;
			@mail('price.aaron@gmail.com', 'Error', "<p>Severity: ".$severity."<br/>".
							  "Message: ".$message."<br/>".
							  "Filepath: ".$filepath."<br/>".
							  "Line Number: ".$line."<br/>".
							  "</p>", "From: \"SHOUTT!\" <noreply@shou.tt>\r\n".
							  $user_details);
		}
		
		// Set the message in the session.
		$_SESSION['error_message'] = $error_message;
		
		// Redirect and show error.
		header('Location: /error');
		//echo $error_message;
	}
	/* End comment for testing. */
}

?>