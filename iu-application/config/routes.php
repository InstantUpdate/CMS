<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* DO NOT TOUCH THIS FILE UNLESS YOU REALLY KNOW WHAT YOU ARE DOING! */

$route['setup'] = 'setup/index';
$route['setup/(:any)'] = 'setup/$1';

$route['administration'] = 'administration/dashboard';
$route['administration/extend/(:any)'] = 'process/pluginPage/$1';
$route['administration/(:any)'] = 'administration/$1';

$route['iu-dynamic-js/(:any).js'] = 'js/$1';
$route['popup/(:any)'] = 'popup/$1';
$route['uploadimage'] = 'uploadimage';

$route['(:any)'] = 'process/uri/$1';

$route['default_controller'] = 'process/uri';
$route['404_override'] = 'process/error/404';


/* End of file routes.php */
/* Location: ./application/config/routes.php */