<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Home_Controller';
$route['home'] = "Home_Controller";
$route['handle_ajax_request'] = 'Ajax_Controller';

$route['tempos'] = "Tempo_Controller";
$route['tempos/(:any)'] = "Tempo_Controller/$1";


$route['users'] = "User_Controller";
$route['users/forgot-password'] = "User_Controller/forgot_password";
$route['users/forgot-password/(:any)'] = "User_Controller/forgot_password/$1";
$route['users/saving-account'] = "User_Controller/saving_account_details";
$route['users/saving-account/(:any)'] = "User_Controller/saving_account_details/$1";
$route['users/(:any)'] = "User_Controller/$1";
$route['users/(:any)/(:any)'] = "User_Controller/$1/$2";
$route['users/(:any)/(:any)/(:any)'] = "User_Controller/$1/$2/$3";

$route['articles'] = "Article_Controller";
$route['articles/(:any)'] = "Article_Controller/$1";
$route['articles/(:any)/(:any)'] = "Article_Controller/$1/$2";

$route['admin'] = "Admin_Controller";

$route['admin/forgot-password'] = "admin_Controller/forgot_password";
$route['admin/change-password'] = "admin_Controller/forgot_password/change-password";
$route['admin/change-password/(:any)'] = "admin_Controller/forgot_password/change-password/$1";
$route['admin/forgot-password/(:any)'] = "admin_Controller/forgot_password/$1";

$route['admin/users'] = "Admin_Controller";

$route['admin/users/list'] = "Admin_Controller/list_users";
$route['admin/users/list/(:any)'] = "Admin_Controller/list_users/$1";
$route['admin/users/list/(:any)/(:any)'] = "Admin_Controller/list_users/$1/$2";

$route['admin/users/(:any)'] = "Admin_Controller/$1";
$route['admin/users/(:any)/(:any)'] = "Admin_Controller/$1/$2";
$route['admin/users/(:any)/(:any)/(:any)'] = "Admin_Controller/$1/$2/$3";
$route['admin/users/(:any)/(:any)/(:any)/(:any)'] = "Admin_Controller/$1/$2/$3/$4";
$route['admin/users/(:any)/(:any)/(:any)/(:any)/(:any)'] = "Admin_Controller/$1/$2/$3/$4/$5";

$route['admin/cronjobs'] = "Cronjob_Controller";
$route['admin/cronjobs/(:any)'] = "Cronjob_Controller/$1";
$route['admin/cronjobs/(:any)/(:any)'] = "Cronjob_Controller/$1/$2";
$route['admin/cronjobs/(:any)/(:any)/(:any)'] = "Cronjob_Controller/$1/$2/$3";
$route['admin/cronjobs/(:any)/(:any)/(:any)/(:any)'] = "Cronjob_Controller/$1/$2/$3/$4";

$route['admin/configurations'] = "Configuration_Controller";
$route['admin/configurations/(:any)'] = "Configuration_Controller/$1";
$route['admin/configurations/(:any)/(:any)'] = "Configuration_Controller/$1/$2";
$route['admin/configurations/(:any)/(:any)/(:any)'] = "Configuration_Controller/$1/$2/$3";

$route['admin/reports'] = "Report_Controller";
$route['admin/reports/(:any)'] = "Report_Controller/$1";
$route['admin/reports/(:any)/(:any)'] = "Report_Controller/$1/$2";
$route['admin/reports/(:any)/(:any)/(:any)'] = "Report_Controller/$1/$2/$3";

$route['admin/(:any)'] = "Admin_Controller/$1";
$route['admin/(:any)/(:any)'] = "Admin_Controller/$1/$2";

$route['404_override'] = 'Error_Controller';
$route['error'] = "Error_Controller";
$route['translate_uri_dashes'] = FALSE;
