<?php 

class Test extends Controller {


	function Test(){
		parent::Controller();
		
		
		if(!in_array($_SERVER['REMOTE_ADDR'], $this->config->item('testing_ips'))) {
			
			// Load the errors library.
			$this->load->library('errors');
			
			$this->errors->set("The page you are looking for is not here.");
			redirect('error');
		}
		
	}
	
	
	function index(){
		
		
		$url = 'http://www.trinidadexpress.com/shared/images/2010/03/04/n2.jpg';
		$ext = strrchr($url, '.');
		echo $ext;
		
		
		
//		$this->load->helper('simple_html_dom');
//		
//		$url = 'http://www.trinidadexpress.com/index.pl/article_news?id=161603349';
//		
//		$html = file_get_html($url);
//		
//		$arr = array();
//		
//		$arr['headline'] = strip_tags(@$html->find(".bigheadline", 0)->innertext);
//		$arr['subheadline'] = strip_tags(@$html->find(".subheadline", 0)->innertext);
//		$arr['author'] = strip_tags(@$html->find(".byline", 0)->innertext);
//		$arr['date'] = strip_tags(@$html->find(".dateline", 0)->innertext);
//		$arr['article'] = '';
//		
//		$text = @$html->find('.texte');
//		foreach($text as $key => $t) {
//			if(@$t->tag == 'div')
//				$arr['article'] .= strip_tags(@$t->innertext).' ';
//		}
//		
//		// Get images in the form of an array.
//		$tables = $html->find('table');
//		foreach($tables as $key => $t) {
//			if(@$t->width == '100%' && @$t->cellspacing == '0' && @$t->border == '0'){
//				$imgs = $t->find('img');
//				
//				foreach($imgs as $i)
//					if(substr(@$i->src, -3) == 'jpg')
//						$arr['images'][] = strip_tags(@$i->src).' ';
//			}
//		}
//		
//		if(count((array) $arr['images']) > 0){
//			// Get domain from url. 
//			$url_arr = parse_url($url);
//			$prefix = $url_arr['scheme'].'://'.$url_arr['host'];
//			
//			echo '<img src="'.$prefix.$arr['images'][0].'" alt="image">';
//		}
//		
//		echo '<pre>';
//		print_r($arr);
//		echo '</pre>';
		
		
		
		
		
		
		
			
		//$url = 'http://www.newsday.co.tt/'; // Fail
		//$url = 'http://www.newsday.co.tt/crime_and_court/0,116593.html'; // Pass
		//$url = 'http://www.newsday.co.tt/news/'; // Fail
		//$url = 'http://www.newsday.co.tt/day/1,39032.html#foto'; // Pass
		//$url = 'http://guardian.co.tt/files/imagecache/article_main_image_stretched/articles/images/lbw_0.png'; // Pass
		
		
		
		
		
		//$url = 'http://www.trinidadexpress.com/index.pl/article_news?id=161602801';
/*		$url = 'http://www.trinidadexpress.com/index.pl/article_opinion?id=161602683';
			
		$html = file_get_contents($url);
		
		
		$search = array("'<script[^>]*?>.*?</script>'si",	// strip out javascript
						"'<[\/\!]*?[^<>]*?>'si",			// strip out html tags
						"'([\r\n])[\s]+'",					// strip out white space
						"'&(quot|#34|#034|#x22);'i",		// replace html entities
						"'&(amp|#38|#038|#x26);'i",			// added hexadecimal values
						"'&(lt|#60|#060|#x3c);'i",
						"'&(gt|#62|#062|#x3e);'i",
						"'&(nbsp|#160|#xa0);'i",
						"'&(iexcl|#161);'i",
						"'&(cent|#162);'i",
						"'&(pound|#163);'i",
						"'&(copy|#169);'i",
						"'&(reg|#174);'i",
						"'&(deg|#176);'i",
						"'&(#39|#039|#x27);'",
						"'&(euro|#8364);'i",				// europe
						"'&a(uml|UML);'",					// german
						"'&o(uml|UML);'",
						"'&u(uml|UML);'",
						"'&A(uml|UML);'",
						"'&O(uml|UML);'",
						"'&U(uml|UML);'",
						"'&szlig;'i",
						);
		$replace = array(	"",
							"|",
							"\\1",
							"\"",
							"&",
							"<",
							">",
							" ",
							chr(161),
							chr(162),
							chr(163),
							chr(169),
							chr(174),
							chr(176),
							chr(39),
							chr(128),
							"ä",
							"ö",
							"ü",
							"Ä",
							"Ö",
							"Ü",
							"ß",
						);
					
		$text = explode('|', preg_replace($search,$replace,$html));
		
		$start = 'View printable version';
		$end = '<!--';
		
		$start_found = false;
		$end_found = false;
		foreach($text as $key => $t){
			if(trim($t) == '')
				unset($text[$key]);
			
			if($start_found == false) {
				if(trim($t) != $start){
					unset($text[$key]);
				} else {
					unset($text[$key]);
					$start_found = true;
				}
			} else {
				if($end_found) {
					unset($text[$key]);
				} else {
					if(trim($t) == $end){
						$end_found = true;
						unset($text[$key]);
					} else {
						//$text[$key] = str_replace("\n", "", $t);
					}
				}
			}	
		}
		
		$text = array_values($text);
		
		echo '<h1>'.$text[0].'</h1>';
		
		//echo '<h1>'.$text[0].'</h1>';
		
		array_shift($text);
		
		echo '<p><strong>'.$text[0].'</strong></p>';
		
		array_shift($text);
		
		echo '<p><em>'.$text[0].'</em></p>';
		
		array_shift($text);
		
		echo '<p>'.$text[0].'</p>';
		
		array_shift($text);
		
		echo implode('', $text);*/
		
		
//		$doc = new DomDocument();
//		$doc->loadHTML($html);
//		
//		$divs = $doc->getElementsByTagName('div');
//		
//		foreach($divs as $div) {
//			if(@$div->attributes->getNamedItem('class')->nodeValue == 'bigheadline'){
//				echo 'true';
//			}
//		}
		
		
//		// Further narrow down to article cell.
//		preg_match('/<td valign="top">(.*?)<\/td>/is', $matches[1], $matches1);
//		
//		$doc = new DomDocument();
//		$doc->loadHTML('<table><tr><td>'.$matches1[1].'</td></tr></table>');
//		
//		echo $matches1[1];
		
		
		
		
		
		
		
//		//$this->load->view('test/facebook_update_user_box');
//		
//		$this->load->library('facebook_connect');
//		
//		$data = array(
//					'user'		=> $this->facebook_connect->user,
//					'user_id'	=> $this->facebook_connect->user_id
//				);
//
//		// This is how to call a client API methods
//		//
//		// $this->facebook_connect->client->feed_registerTemplateBundle($one_line_story_templates, $short_story_templates, $full_story_template);
//		// $this->facebook_connect->client->events_get($data['user_id']);
//		
//		$this->load->view('test/facebook_with_codeigniter', $data);

//		$this->load->model('story');
//
//		$query = $this->db->get('stories');
//		
//		$result = $query->result_array();
//		
//		foreach($result as $key => $story){
//			$this->story->assess($story['id']);
//			echo $story['headline_txt'].' has been assessed.<br/>';
//		}
//		
//		echo 'Done.';

	}

}



















































//		$this->load->library('youtube');
//		
//		$videos = $this->youtube->get_user_videos('cnc3television');
//		
//		echo '<pre>';
//		print_r($videos);
//		echo '</pre>';
		
	
//		require_once('Zend/Loader.php');
//		Zend_Loader::loadClass('Zend_Gdata_YouTube');
//		
//		$yt = new Zend_Gdata_YouTube();
//		$yt->setMajorProtocolVersion(2);
//		$videos = $yt->getuserUploads('cnc3television');
//		
//		echo '<pre>';
//		foreach($videos as $videoEntry){
//			echo "---------VIDEO----------\n";
//			echo "ID: ". $videoEntry->getVideoId()."\n";
//			echo "Title: " . $videoEntry->getVideoTitle() . "\n";
//			echo "Description: ";
//			echo $videoEntry->getVideoDescription()."\n";
//			echo "Duration: ".$videoEntry->getVideoDuration()."\n";
//			echo "Recorded: ".$videoEntry->getVideoRecorded()."\n";
//			echo "View Count: ".$videoEntry->getVideoViewCount()."\n";
//			echo "Category: ".$videoEntry->getVideoCategory()."\n";
//			echo "Published: ".$videoEntry->getPublished()."\n";
//			echo "Updated: ".$videoEntry->getUpdated()."\n";
//			echo "Rating: ";
//			print_r($videoEntry->getVideoRatingInfo());
//			echo "\r";
//			echo "Location: ";
//			print_r($videoEntry->getVideoGeoLocation());
//			echo "\r";
//			echo "Watch Page URL: ".$videoEntry->getVideoWatchPageUrl()."\n";
//			echo "Thumbnails: ";
//			print_r($videoEntry->getVideoThumbnails());
//			echo "\r";
//			echo "\n\n\n";
//		}
//		echo '</pre>';

	
		//echo '<pre>';
		//print_r($_SERVER);
		//echo '</pre>';
		
//		$resource_path = '/category/year/mm/dd/story_headline_url';
//		
//		$input = "Hi there <image a.jpg>\n\n\n\n\n\n<caption>Your mother.</caption>. There is another set of text of the same thing <Caption>Some Other caption.</Caption>.";
//		
//		$input = preg_replace('/(?<=>)\s*?(?=<caption>)/is', '', $input);
//		//$input = preg_replace('/(?<=<caption>)\s*?([\r|\n])\s*?(?=<\/caption>)/is', '', $input);
//			 
//		echo $input;

		//$this->load->library('util');
		//echo $this->util->get_random_filename($_SERVER['DOCUMENT_ROOT'].'/tmp/', '.jpg');
//		$to = 'wavesmachine@hotmail.com';
//		
//		mail($to, 'test', 'this is a test message.');
//		
//		echo $to;
?>