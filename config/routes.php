<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
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
| 	www.your-site.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://www.codeigniter.com/user_guide/general/routing.html
*/

$route['storylines/custom/articles/(:any)/(:any)/(:any)/(:any)']		= "articles/$1/$2/$3/$4";
$route['storylines/custom/articles/(:any)/(:any)/(:any)']		= "articles/$1/$2/$3";
$route['storylines/custom/articles/(:any)/(:any)']		= "articles/$1/$2";
$route['storylines/custom/articles/(:any)'] 		= "articles/$1";
$route['storylines/custom/articles']				= "articles";

$route['storylines/custom/conditions/(:any)/(:any)/(:any)/(:any)']		= "conditions/$1/$2/$3/$4";
$route['storylines/custom/conditions/(:any)/(:any)/(:any)']		= "conditions/$1/$2/$3";
$route['storylines/custom/conditions/(:any)/(:any)']		= "conditions/$1/$2";
$route['storylines/custom/conditions/(:any)'] 		= "conditions/$1";
$route['storylines/custom/conditions']				= "conditions";

$route['storylines/custom/triggers/(:any)/(:any)/(:any)/(:any)']		= "triggers/$1/$2/$3/$4";
$route['storylines/custom/triggers/(:any)/(:any)/(:any)']		= "triggers/$1/$2/$3";
$route['storylines/custom/triggers/(:any)/(:any)']		= "triggers/$1/$2";
$route['storylines/custom/triggers/(:any)'] 		= "triggers/$1";
$route['storylines/custom/triggers']				= "triggers";

$route['storylines/custom/results/(:any)/(:any)/(:any)/(:any)']		= "results/$1/$2/$3/$4";
$route['storylines/custom/results/(:any)/(:any)/(:any)']		= "results/$1/$2/$3";
$route['storylines/custom/results/(:any)/(:any)']		= "results/$1/$2";
$route['storylines/custom/results/(:any)'] 		= "results/$1";
$route['storylines/custom/results']				= "results";		

$route['storylines/custom/data_objects/(:any)/(:any)/(:any)/(:any)']		= "data_objects/$1/$2/$3/$4";
$route['storylines/custom/data_objects/(:any)/(:any)/(:any)']		= "data_objects/$1/$2/$3";
$route['storylines/custom/data_objects/(:any)/(:any)']		= "data_objects/$1/$2";
$route['storylines/custom/data_objects/(:any)'] 		= "data_objects/$1";
$route['storylines/custom/data_objects']				= "data_objects";