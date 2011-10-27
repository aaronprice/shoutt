<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

if ( ! function_exists('get_domain')) {
	function get_domain($url) {
		$host = parse_url($url, PHP_URL_HOST);
		
		return (strtolower(substr($host, 0, 4)) == 'www.') ? substr($host, 4) : $host;
	}
}