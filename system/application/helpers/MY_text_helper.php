<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

function display_headline($input, $format=true) {
	
	// Normalize exageration
	$input = normalize_text($input);
	
	if( $format ) {
		// Format title.
		$input = format_headline($input);
	}
	
	// Filter profanity unless user is okay with it.
	$CI =& get_instance();
	$CI->load->model('user');
	$target = '';
	
	if($CI->user->is_logged_in()){
	
		$view_settings = $CI->user->settings('view', $CI->session->userdata($CI->config->item('session_key').'_usr'));
	
		// Check for opening links in new windows.
		if($view_settings['openextlinks'] == '1') {
			$target = ' target="_blank"';
		}
				
		// Filter Profanity.
		if($view_settings['profanity'] == '0' && substr($input, 0, 4) != '<img'){
			$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
		}
	} else {
		if(substr($input, 0, 4) != '<img'){
			// Filter Profanity.
			$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
		}
	}
	
	return $input;
}



function display_story($input, $resource_path='') {

	// FILTER PROFANITY
	// Filter profanity unless user is okay with it.
	$CI =& get_instance();
	$CI->load->model('user');
	$target = '';
	
	if($CI->user->is_logged_in()){
	
		$view_settings = $CI->user->settings('view', $CI->session->userdata($CI->config->item('session_key').'_usr'));
	
		// Check for opening links in new windows.
		if($view_settings['openextlinks'] == '1') {
			$target = ' target="_blank"';
		}
				
		// Filter Profanity.
		if($view_settings['profanity'] == '0'){
			$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
		}
	} else {
		// Filter Profanity.
		$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
	}

	// Forget captions. Just display images at full size with the options of viewing as slide show.
	// So with of 500px.

	// DISPLAY IMAGES.
	$input = preg_replace(
				'#<image ([a-z0-9]*)\.([a-z]*)>#i', 
				'<div class="story-image"><a class="group" rel="group" href="/img'.$resource_path.'/\\1.\\2"><img src="/img'.$resource_path.'/thm/\\1_956_956.\\2" alt="\\3"/></a></div>', 
				$input
			 ); 
	
	// Group Images.
	$input = str_replace("\n<div", '<div', $input);
			 
	// DISPLAY VIDEO.
	$input = display_video( $input, '960', '576' );
			
	// Convert Google Map Links to Maps
	//http://maps.google.com/maps?q=Queen%27s+Park+Savannah,+Trinidad+%26+Tobago&hl=en&cd=1&ei=klfsS_7KDpHoygSm8ICDDg&sll=10.667322,-61.512144&sspn=0.026316,0.038418&ie=UTF8&view=map&cid=1787264321610890320&ved=0CBMQpQY&hq=Queen%27s+Park+Savannah,+Trinidad+%26+Tobago&hnear=&ll=10.669486,-61.514082&spn=0.006811,0.011845&z=17&iwloc=A
	//http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=Mayaro,+Trinidad+%26+Tobago&sll=10.667322,-61.512144&sspn=0.027244,0.047379&ie=UTF8&hq=&hnear=Mayaro,+Trinidad+%26+Tobago&z=12
	//http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=Mayaro,+Trinidad+%26+Tobago&sll=37.0625,-95.677068&sspn=46.495626,92.460937&ie=UTF8&hq=&hnear=Mayaro,+Trinidad+%26+Tobago&z=12
	//http://maps.google.com/maps/place?ftid=0x8c4a15a93d7d35db:0x2637ed3f45505300&q=Mayaro,+Trinidad+%26+Tobago&hl=en&ei=u6DsS6uRBpWgzATtz_WEDg&sll=10.183186,-61.117018&sspn=0.17574,0.256119&ie=UTF8&ll=10.292288,-61.306458&spn=0,0&z=12&iwloc=A
	$input = preg_replace(
				"#http://maps.google.com/maps(.*?)q=([a-zA-Z0-9\+\-\.']+)(.*?)sll=(-?\d+\.\d+),(-?\d+\.\d+)(.*?)z=([0-9]+)(.*)?#",
				'<div class="map" q="\\2" latitude="\\4" longitude="\\5" zoom="\\7"></div>',
				$input
			 );
	
	
	
	// Convert non video links to HTML.
	$input = preg_replace('#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm','\\1<a href="\\2\\3" rel="nofollow"'.$target.'>\\2\\3</a>\\4', $input);
	
	// Convert www links to HTML.
	$input = preg_replace('#(^|[^\"=|//]{1})(www)([^\s<>]+)([\s\n<>]|$)#sm','\\1<a href="http://\\2\\3" rel="nofollow"'.$target.'>\\2\\3</a>\\4', $input);

	// Convert new lines "\n" to "<br />"
	$input = nl2br($input);
	
	// Remove line breaks.
	$input = str_replace("\n", "", $input);
	
	// Normalize exageration
	$input = normalize_text($input);
	
	// SHOW STORY.
	return $input;
}





function display_video($input, $width='500', $height='300') {
	
	// Convert YouTube links to videos. width: 500, height: 300
	$input = preg_replace( // Search for
				  "#http://(www\.)?youtube.com/watch\?v=([A-Za-z0-9_-]*)(.*)?#",
				  // Replace with
				  '</p><div class="video">'.
				  '<object width="'.$width.'" height="'.$height.'">'.
				  '<param name="movie" value="http://www.youtube.com/v/\\2&fs=1"></param>'.
				  '<param name="wmode" value="transparent"></param>'.
				  '<param name="allowfullscreen" value="true"></param>'.
				  '<embed src="http://www.youtube.com/v/\\2&fs=1&hd=1" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'" allowfullscreen="true"></embed>'.
				  '</object>'.
				  '</div><p>',
				  // on
				  $input
			 );
	
	
	// Convert Google Video Links to Videos.
	$input = preg_replace(
				"#http://video.google.com/videoplay\?docid=([0-9\-]*)(.*)?#", 
				'</p><div class="video">'.
				'<embed style="width:'.$width.'px; height:'.$height.'px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=\\1&hl=en" flashvars=""></embed>'.
				'</div><p>', 
				$input
			 );
			 
	// Convert Vimeo Links to Videos.	 
	$input = preg_replace(
				"#http://(www\.)?vimeo.com/([0-9]*)(.*)?#",
				'</p><div class="video">'. 
				'<object width="'.$width.'" height="'.$height.'">'.
				'<param name="allowfullscreen" value="true" />'.
				'<param name="allowscriptaccess" value="always" />'.
				'<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=\\2&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />'.
				'<embed src="http://vimeo.com/moogaloop.swf?clip_id=\\2&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.$width.'" height="'.$height.'"></embed>'.
				'</object>'.
				'</div><p>', 
				$input
			 );
			 
	// Convert Facebook Video Links to Videos.	 
	$input = preg_replace(
				"#http://www.facebook.com/(.*?)video/video.php\?v=([0-9]*)(.*)?#",
				'</p><div class="video">'.
				'<object width="'.$width.'" height="'.$height.'" >'.
				'<param name="allowfullscreen" value="true" />'.
				'<param name="allowscriptaccess" value="always" />'.
				'<param name="movie" value="http://www.facebook.com/v/\\2" />'.
				'<embed src="http://www.facebook.com/v/\\2" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed>'.
				'</object>'.
				'</div><p>',
				$input
			 );
			
	return $input;
}



function display_comment($input) {
	
	// Filter profanity unless user is okay with it.
	$CI =& get_instance();
	$CI->load->model('user');
	$target = '';
	
	if($CI->user->is_logged_in()){
	
		$view_settings = $CI->user->settings('view', $CI->session->userdata($CI->config->item('session_key').'_usr'));
	
		// Check for opening links in new windows.
		if($view_settings['openextlinks'] == '1') {
			$target = ' target="_blank"';
		}
				
		// Filter Profanity.
		if($view_settings['profanity'] == '0'){
			$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
		}
	} else {
		// Filter Profanity.
		$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
	}
	
	// Convert non video links to HTML.
	$input = preg_replace('#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm','\\1<a href="\\2\\3" rel="nofollow"'.$target.'>\\2\\3</a>\\4', $input);
	
	// Convert www links to HTML.
	$input = preg_replace('#(^|[^\"=|//]{1})(www)([^\s<>]+)([\s\n<>]|$)#sm','\\1<a href="http://\\2\\3" rel="nofollow"'.$target.'>\\2\\3</a>\\4', $input);

	// Convert new lines "\n" to "<br />"
	$input = nl2br($input);
	
	// Remove line breaks.
	$input = str_replace("\n", "", $input);
	
	// Normalize exageration
	$input = normalize_text($input);
	
	return $input;
}



function display_story_link($address, $title, $format=true) {

	// Filter profanity unless user is okay with it.
	$CI =& get_instance();
	$CI->load->model('user');
	
	$link = '<a href="'.$address.'"';
	
	// Format title.
	$title = display_headline($title, $format);
	
	if($CI->user->is_logged_in()){
		$view_settings = $CI->user->settings('view', $CI->session->userdata($CI->config->item('session_key').'_usr'));
	
		// Check for opening links in new windows.
		if($view_settings['openstorylinks'] == '1') {
			$link .= ' target="_blank"';
		}
		
		// Filter Profanity.
		if($view_settings['profanity'] == '0' && substr($title, 0, 4) != '<img'){
			$title = word_censor($title, $CI->config->item('profanity_words'), '*****');
		}
	} else {
		if(substr($title, 0, 4) != '<img'){
			$title = word_censor($title, $CI->config->item('profanity_words'), '*****');
		}
	}
	
	$link .= '>'.(empty($title) ? $address : $title).'</a>';
	
	return $link;
}


function display_ext_link($address, $title) {
	// Filter profanity unless user is okay with it.
	$CI =& get_instance();
	$CI->load->model('user');
	
	$link = '<a rel="nofollow" href="'.$address.'"';
	
	if($CI->user->is_logged_in()){
		$view_settings = $CI->user->settings('view', $CI->session->userdata($CI->config->item('session_key').'_usr'));
	
		// Check for opening links in new windows.
		if($view_settings['openstorylinks'] == '1') {
			$link .= ' target="_blank"';
		}			
	}
	
	$link .= '>'.(empty($title) ? $address : $title).'</a>';
	
	return $link;
}



function display_preview($input, $limit=160) {
	
	// Strip HTML Tags.
	$input = strip_tags($input);
	
	// Normalize exageration
	$input = normalize_text($input);
	
	// Remove URLs
	$input = remove_urls( $input );
	
	// Limit Characters.
	$input = character_limiter($input, $limit);
	
	// Filter profanity unless user is okay with it.
	$CI =& get_instance();
	$CI->load->model('user');
	
	if($CI->user->is_logged_in()){
	
		$view_settings = $CI->user->settings('view', $CI->session->userdata($CI->config->item('session_key').'_usr'));
	
		// Filter Profanity.
		if($view_settings['profanity'] == '0'){
			$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
		}
	} else {
		// Filter Profanity.
		$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
	}
	
	return $input;
}


function remove_urls( $str ) {
	$U = explode(' ',$str);

	$W = array();
	foreach ($U as $k => $u) {
		if (stristr($u,'http') || (count(explode('.',$u)) > 1)) {
			unset($U[$k]);
			return remove_urls( implode(' ',$U));
		}
	}
	return implode(' ',$U);
}





function display_js_preview($input, $limit=160) {
	
	// Strip HTML Tags.
	$input = strip_tags($input);
	
	// Normalize exageration
	$input = normalize_text($input);
	
	// Remove URLs
	$input = remove_urls( $input );
	
	// Limit Characters.
	$input = character_limiter($input, $limit);
	
	// Filter profanity unless user is okay with it.
	$CI =& get_instance();
	$CI->load->model('user');
	
	if($CI->user->is_logged_in()){
	
		$view_settings = $CI->user->settings('view', $CI->session->userdata($CI->config->item('session_key').'_usr'));
	
		// Filter Profanity.
		if($view_settings['profanity'] == '0'){
			$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
		}
	} else {
		// Filter Profanity.
		$input = word_censor($input, $CI->config->item('profanity_words'), '*****');
	}
	
	// Change "\n" to " "
	$input = str_replace("\n", " ", $input);
	
	// Add slashes to quotes.
	$input = addslashes($input);
	
	return $input;
}










if ( ! function_exists('profanity_counter'))
{
	function profanity_counter($str, $censored)
	{
		if ( ! is_array($censored))
		{
			return 0;
		}
        
        $str = ' '.$str.' ';

		// \w, \b and a few others do not match on a unicode character
		// set for performance reasons. As a result words like über
		// will not match on a word boundary. Instead, we'll assume that
		// a bad word will be bookended by any of these characters.
		$delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

		$count = 0;
		foreach ($censored as $badword)
		{
			$count += preg_match_all("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/ie", $str, $matches);
		}

        return $count;
	}
}










function format_headline($title){

	$article = array("a","an","the");
	
	$preposition = array("about","above","across","after","against","along",
	"amid","among","around","at","before","behind","below", "beneath",
	"beside","besides","between","beyond","but","by","concerning","despite",
	"down","during","except","from","in","including", "inside","into","like",
	"minus","near","notwithstanding","of","off","on", "onto","opposite","out",
	"outside","over","past","per","plus","regarding","since","through",
	"throughout","till","to","toward","towards","under","underneath","unless",
	"unlike","until","up","upon","versus","via","with","within","without");
	
	$conjunction_coordinating = array("and","but","for","nor","or","so","yet");
	
	$conjunction_subordinating = array("after","although","as","because","if",
	"lest","than","that","though","when","whereas","while");
	
	$conjunction_correlative = array("also","both","each","either","neither","whether");
	
	$nocaps = array_merge(
						  $article, 
						  $preposition, 
						  $conjunction_coordinating,
						  $conjunction_subordinating, 
						  $conjunction_correlative
						  );
	
	$multi_word_preposition = array("according to","in addition to","in back of",
	"in front of", "in spite of","on top of","other than","together with");
	
	$words = explode(" ", $title);
	$num_words = count($words);
		
	//CAPITALIZE ONLY WORDS THAT SHOULD BE CAPITALIZED
	for($i = 0; $i < $num_words; $i++) {
		if(in_array(strtolower($words[$i]), $nocaps)) 
			$words[$i] = strtolower($words[$i]); 
		else $words[$i] = ucwords($words[$i]);
	}//END LOOP
	
	$title = implode(" ", $words);
	
	//REPLACING A STRING THAT MAY HAVE CAPITAL LETTERS
	//WITH ONE THAT IS ALL LOWER-CASE.
	//USE str_ireplace() IF YOU HAVE PHP 5; 
	//IT CAN USE ARRAYS FOR PATTERN AND REPLACEMENT
	foreach($multi_word_preposition as $value){ 
		$title = eregi_replace($value, $value, $title); 
	}
	
	$words = explode(" ", $title);
	
	//CAPITALIZE FIRST AND LAST WORDS
	$words[0] = ucwords($words[0]);
	$words[$num_words - 1] = ucwords($words[$num_words - 1]);
		
	$title = implode(" ", $words);
	
	$title = str_replace(' ?', '?', $title);
	
	return rtrim($title, ' !.,');
	
}//END englishTitle() 







function get_keywords( $story, $length = 15 ){
	
	// Clean up a little
	$story = strtolower( str_replace( "\n", " ", strip_tags( $story ) ) );
	
	// Replace non alpha,
	$story = preg_replace( "/[^a-z]+/", ' ', $story );
	
	$story = normalize_text( $story );
	
	// Remove common words.
	$commonWords = array('a','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as',
	'a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','bright','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','decided','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done',
	'don\'t','down','downwards','during','e','each','early','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','friday','friend','friends','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself',
	'he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let',
	'let\'s','like','liked','likely','likewise','little','look','looking','looks','love','loves','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','monday','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone',
	'no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saturday','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should',
	'shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','story','sub','such','sunday','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s',
	'that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thursday','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying',
	't\'s','tuesday','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever',
	'who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero',
	// Months of the year.
	'january','february','march','april','may','june','july','august','september','october','november','december',
	// the country
	'trinidad','tobago','trini',
	// Family relations
	'mother','father','sister','brother','aunt','aunty','uncle','grandmother','grandfather','niece','nephew','cousin');
	$story = preg_replace('/\b('.implode( '|', $commonWords ).')\b/',' ',$story);
	
	// Get words.
	$words = explode( ' ', $story );
	
	$new_words = array('shoutt','trinidad and tobago news','trinidad news');
	$count = 0;
	foreach ( $words as $index => $word ) {
		if ( strlen( trim( $word ) ) > 4 && strlen( trim( $word ) ) < 15 && !in_array( $word, $new_words ) && $count <= $length) {
			$new_words[] = $words[ $index ];
			++ $count;
		}
	}
	
	return implode( ',', $new_words );
	
}








function normalize_text($input){
	$search = array('....', '??', '!!', ' ,', ' .', ' ?', ' !', 'ooo');
	$replace = array('...', '?', '!', ',', '.', '?', '!', 'oo');
	$count = 1;
	while($count > 0){
		$input = str_replace($search, $replace, $input, $count);
	}
	return $input;
}





//if ( ! function_exists('character_limiter'))
//{
//	function character_limiter($str, $n = 500, $end_char = '&#8230;')
//	{
//		
//		$str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));
//		
//		if(strlen($str) > $n){
//			
//			return substr($str, 0, $n).$end_char;
//			
//		} else return $str;
//		
//	}
//}


/* End of file image_helper.php */
/* Location: ./application/helpers/image_helper.php */
?>