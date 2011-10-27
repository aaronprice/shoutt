<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "stories";
$route['scaffolding_trigger'] = "";



// Trinidad Stories Routes.
$route['contact'] = "contact";
$route['terms'] = "terms";
$route['tos'] = "terms";

// Admin routes.
$route['admin/abuse/ignore'] = "admin/ignore_report";

// User Routes.
$route['login'] = "users/login";
$route['logout'] = "users/logout";
$route['signup'] = "users/signup";
$route['signup/(:any)'] = "users/signup";
$route['register'] = "users/signup";
$route['register/(:any)'] = "users/signup";
$route['join'] = "users/signup";
$route['join/(:any)'] = "users/signup";
$route['recover'] = "users/recover";
$route['users/(:any)/tech_details'] = "users/tech_details";
$route['users/(:any)/history/favorites/page(:num)'] = "users/favorites";
$route['users/(:any)/history/favorites'] = "users/favorites";
$route['users/(:any)/history/page(:num)'] = "users/history";
$route['users/(:any)/history'] = "users/history";
$route['users/(:any)/log/page(:num)'] = "users/log";
$route['users/(:any)/log'] = "users/log";
$route['users/(:any)/verify'] = "users/verify";
$route['users/(:any)/verify/(:any)'] = "users/verify";

// Story Routes.
$route['participants'] = 'stories/participants';
$route['delete'] = "stories/delete";
$route['story_deleted'] = "stories/story_deleted";
$route['report_abuse'] = "stories/report_abuse";
$route['favorite'] = "stories/favorite";
$route['vote'] = "stories/vote";
$route['edit_comment'] = "stories/edit_comment";
$route['edit/(:any)'] = "stories/edit";
$route['edit'] = "stories/edit";
$route['post/(:any)'] = "stories/compose";
$route['post'] = "stories/compose";
$route['new/(:any)'] = "stories/compose";
$route['new'] = "stories/compose";
$route['compose/(:any)'] = "stories/compose";
$route['compose'] = "stories/compose";
$route['write/(:any)'] = "stories/compose";
$route['write'] = "stories/compose";
$route['submit/processing'] = "stories/processing";
$route['submit/step2'] = "stories/step2";
$route['submit/(:any)'] = "stories/submit";
$route['submit'] = "stories/submit";
//$route['submit/(:any)'] = "stories/compose";
//$route['submit'] = "stories/compose";
$route['upload'] = "stories/upload";
$route['page(:num)'] = "stories";
$route['perspective'] = "stories/perspective";
$route['images'] = "stories/images";
$route['videos'] = "stories/videos";
$route['comment'] = "stories/comment";

// Get routing info from menu.
$menu = $this->config->item('menu');
$menu_str = '';
foreach($menu as $key => $value)
	$menu_str .= '|'.$key;
	
$sub_menus = $this->config->item('sub_menus');

foreach($sub_menus as $key => $value)
	foreach($sub_menus[$key] as $sub_key => $sub_value)
		if(strpos($menu_str, $sub_key) == false)
			$menu_str .= '|'.$sub_key;

$route['(all'.$menu_str.')/page(:num)'] = "stories/filter";
$route['(all'.$menu_str.')/perspective'] = "stories/perspective";
$route['(all'.$menu_str.')/images'] = "stories/images";
$route['(all'.$menu_str.')/videos'] = "stories/videos";
$route['(all'.$menu_str.')/upcoming/page(:num)'] = "stories/upcoming";
$route['(all'.$menu_str.')/popular/page(:num)'] = "stories/popular";
$route['(all'.$menu_str.')/popular/perspective'] = "stories/perspective";
$route['(all'.$menu_str.')/upcoming/perspective'] = "stories/perspective";
$route['(all'.$menu_str.')/popular/images'] = "stories/images";
$route['(all'.$menu_str.')/upcoming/images'] = "stories/images";
$route['(all'.$menu_str.')/popular/videos'] = "stories/videos";
$route['(all'.$menu_str.')/upcoming/videos'] = "stories/videos";
$route['(all'.$menu_str.')/popular/(:any)/perspective'] = "stories/perspective";
$route['(all'.$menu_str.')/popular/(:any)/images'] = "stories/images";
$route['(all'.$menu_str.')/popular/(:any)/videos'] = "stories/videos";
$route['(all'.$menu_str.')/popular/(:any)/page(:num)'] = "stories/top_in";
$route['(all'.$menu_str.')/popular/(:any)'] = "stories/top_in";
$route['(all'.$menu_str.')/upcoming/(:any)/perspective'] = "stories/perspective";
$route['(all'.$menu_str.')/upcoming/(:any)/images'] = "stories/images";
$route['(all'.$menu_str.')/upcoming/(:any)/videos'] = "stories/videos";
$route['(all'.$menu_str.')/upcoming/(:any)/page(:num)'] = "stories/top_in";
$route['(all'.$menu_str.')/upcoming/(:any)'] = "stories/top_in";
$route['(all'.$menu_str.')/upcoming'] = "stories/upcoming";
$route['(all'.$menu_str.')/popular'] = "stories/popular";
$route['(all'.$menu_str.')/:any'] = "stories/article";
$route['(all'.$menu_str.')'] = "stories/filter";



//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/page(:num)'] = "stories/filter";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/perspective'] = "stories/perspective";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/images'] = "stories/images";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming/page(:num)'] = "stories/upcoming";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular/page(:num)'] = "stories/popular";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular/perspective'] = "stories/perspective";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming/perspective'] = "stories/perspective";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular/images'] = "stories/images";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming/images'] = "stories/images";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular/(:any)/perspective'] = "stories/perspective";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular/(:any)/images'] = "stories/images";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular/(:any)/page(:num)'] = "stories/top_in";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular/(:any)'] = "stories/top_in";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming/(:any)/perspective'] = "stories/perspective";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming/(:any)/images'] = "stories/images";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming/(:any)/page(:num)'] = "stories/top_in";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming/(:any)'] = "stories/top_in";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/upcoming'] = "stories/upcoming";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/popular'] = "stories/popular";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)/:any'] = "stories/article";
//$route['(all|public|service|awareness|crime|murder|kidnapping|burglary|robbery|assault|other_crime|politics|elections|business|economy|finance|lifestyle|culture|health|safety|religion|environment|pollution|opinion|features|people|arts|entertainment|carnival_2010|sports|cricket|soccer|other_sports|misc|on_the_road|comedy)'] = "stories/filter";


// Feeds routes.
$route['rss'] = "feeds/rss";
$route['rss/(:any)'] = "feeds/rss";
$route['sitemap'] = "feeds/sitemap";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */