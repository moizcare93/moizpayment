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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';

$route['dashboard'] = 'dashboard/index';

$route['clients'] = 'clients/index';
$route['clients/create'] = 'clients/create';
$route['clients/edit/(:num)'] = 'clients/edit/$1';
$route['clients/delete/(:num)'] = 'clients/delete/$1';

$route['quotations'] = 'quotations/index';
$route['quotations/create'] = 'quotations/create';
$route['quotations/edit/(:num)'] = 'quotations/edit/$1';
$route['quotations/view/(:num)'] = 'quotations/view/$1';
$route['quotations/delete/(:num)'] = 'quotations/delete/$1';
$route['quotations/convert/(:num)'] = 'quotations/convert_to_invoice/$1';

$route['invoices'] = 'invoices/index';
$route['invoices/create'] = 'invoices/create';
$route['invoices/edit/(:num)'] = 'invoices/edit/$1';
$route['invoices/view/(:num)'] = 'invoices/view/$1';
$route['invoices/print/(:num)'] = 'invoices/printable/$1';
$route['invoices/delete/(:num)'] = 'invoices/delete/$1';
$route['invoices/payment/(:num)'] = 'invoices/add_payment/$1';

$route['finance/income'] = 'finance/income';
$route['finance/income/create'] = 'finance/create_income';
$route['finance/income/delete/(:num)'] = 'finance/delete_income/$1';
$route['finance/expenses'] = 'finance/expenses';
$route['finance/expenses/create'] = 'finance/create_expense';
$route['finance/expenses/delete/(:num)'] = 'finance/delete_expense/$1';

$route['reports'] = 'reports/index';
$route['settings'] = 'settings/index';
