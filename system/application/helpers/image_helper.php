<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

function image_thumb($image_path, $height=100, $width=100, $alt='', $crop=false) {
	
	// Get the CodeIgniter super object
	$CI =& get_instance();
	
	if(is_dir($_SERVER['DOCUMENT_ROOT'].$image_path))
		return '';
	else {
	
		// Define vars for naming.
		// Full filename: "filename.ext"
		$full_filename = substr(strrchr($image_path, '/'), 1);
		// Raw filename: "filename"
		$raw_filename = substr($full_filename, 0, strrpos($full_filename, '.'));
		// Extension: ".ext"
		$ext = strrchr($image_path, '.');
		
		// Path to image thumbnail
		$image_thumb = dirname($image_path).'/thm/'.$raw_filename.'_'.$height.'_'.$width.$ext;
	
		if( ! file_exists($_SERVER['DOCUMENT_ROOT'].$image_thumb)) {
			
			// Create Thumbs Directory if it doesn't exist.
			$CI->load->library('util');
			$CI->util->mkrdir(dirname($_SERVER['DOCUMENT_ROOT'].$image_path).'/thm/');
		
			// LOAD LIBRARY
			$CI->load->library('image_lib');
			
			if($crop == true && function_exists('getimagesize')) {
				
				if (false !== ($dimensions = @getimagesize($_SERVER['DOCUMENT_ROOT'].$image_path))){
				
					$image_width		= $dimensions['0'];
					$image_height		= $dimensions['1'];
					
					// Prepare to Resize Image.
					$config['image_library'] = 'gd2';
					$config['source_image'] = $_SERVER['DOCUMENT_ROOT'].$image_path;
					$config['new_image'] = $_SERVER['DOCUMENT_ROOT'].$image_thumb;
					$config['maintain_ratio'] = true;
					if($image_height > $image_width){
						$config['width'] = $width + 1;
						$config['height'] = $height * 100;
					} else {
						$config['width'] = $width * 100;
						$config['height'] = $height + 1;
					}
					
					// Initialize the object with the following settings.
					$CI->image_lib->initialize($config);
					
					// Resize the image.
					$CI->image_lib->resize();
					
					// Clear the settings in preparation for other images.
					$CI->image_lib->clear();
					
					
					// Prepare to crop image.
					$config['image_library'] = 'gd2';
					$config['source_image'] = $_SERVER['DOCUMENT_ROOT'].$image_thumb;
					$config['new_image'] = $_SERVER['DOCUMENT_ROOT'].$image_thumb;
					$config['width'] = $width;
					$config['height'] = $height;
					$config['maintain_ratio'] = false;
					$config['x_axis'] = '0';
					$config['y_axis'] = '0';
					
					// Initialize the object with the following settings.
					$CI->image_lib->initialize($config);
					
					// Crop the image.
					$CI->image_lib->crop();
					
					// Clear the settings in preparation for other images.
					$CI->image_lib->clear();
					
				}
			} else {
			
				if (false !== ($dimensions = @getimagesize($_SERVER['DOCUMENT_ROOT'].$image_path))){
				
					$image_width		= $dimensions['0'];
					$image_height		= $dimensions['1'];
					
					if($image_width > $width) {			
						// Resize image.
						$config['image_library']	= 'gd2';
						$config['source_image']		= $_SERVER['DOCUMENT_ROOT'].$image_path;
						$config['new_image']		= $_SERVER['DOCUMENT_ROOT'].$image_thumb;
						$config['maintain_ratio']	= TRUE;			
						$config['width'] = $width;
						$config['height'] = $height;
						$CI->image_lib->initialize($config);
						$CI->image_lib->resize();
						$CI->image_lib->clear();
					} else {
						// Copy.
						@copy($_SERVER['DOCUMENT_ROOT'].$image_path, $_SERVER['DOCUMENT_ROOT'].$image_thumb);
					}
				}
			}
		}
	
		return '<img src="'.$image_thumb.'" alt="'.$alt.'"/>';
	}
}

/* End of file image_helper.php */
/* Location: ./application/helpers/image_helper.php */
?>