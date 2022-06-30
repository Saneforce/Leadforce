<?php

defined('BASEPATH') or exit('No direct script access allowed');

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
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|   http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|   $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|       my-controller/my-method -> my_controller/my_method
*/

$route['default_controller']   = 'clients';
$route['404_override']         = '';
$route['translate_uri_dashes'] = false;

$route['superadmin/login'] = "superadmin/auth/adminLogin";
$route['superadmin/logout'] = "superadmin/auth/adminLogout";

$route['superadmin/company'] = "superadmin/settings/company";
$route['superadmin/companyView'] = "superadmin/settings/companyView";
$route['superadmin/addcompany'] = "superadmin/settings/addcompany";
$route['superadmin/editcompany/(:any)'] = "superadmin/settings/editcompany";
$route['superadmin/downloadcompany/(:any)'] = "superadmin/settings/downloadcompany";

/**
 * API route
 */
$route['api/authentication'] = "api/Authentication/loginapi";
$route['api/forgotpassword'] = "api/Authentication/forgot_password";
//$route['api/getlead'] = "api/ApiController/getlead";
/*Activity*/
$route['api/getactivity'] = "api/ApiController/getactivity";
$route['api/createactivity'] = "api/ApiController/createactivity";
$route['api/viewactivity'] = "api/ApiController/viewactivity";
$route['api/getcontacts'] = "api/ApiController/getcontacts";
$route['api/getactivitytypes'] = "api/ApiController/getactivitytypes";
$route['api/getsearchdeal'] = "api/ApiController/getsearchdeal";
$route['api/get_org_person_bydeal'] = "api/ApiController/get_org_person_bydeal";
/*Deal*/
$route['api/getdeals'] = "api/ApiController/getdeals";
$route['api/getdealbyid'] = "api/ApiController/getdealbyid";
$route['api/list_activities_by_dealid'] = "api/ApiController/list_activities_by_dealid";
$route['api/createdeal'] = "api/ApiController/createdeal";
$route['api/get_search_organisation'] = "api/ApiController/get_search_organisation";
$route['api/changepipeline'] = "api/ApiController/changepipeline";
$route['api/changepipeline_teammembers'] = "api/ApiController/changepipelineteammembers";
$route['api/check_deal_exist'] = "api/ApiController/check_deal_exist";
/*Contacts*/
$route['api/all_contacts'] = "api/ApiController/all_contacts";
$route['api/add_contact'] = "api/ApiController/add_contact";
$route['api/get_contact_details'] = "api/ApiController/getcontactDetails";
$route['api/getstaff_details'] = "api/ApiController/getstaff_details";

$route['api/getorgcontacts'] = "api/ApiController/getorgcontacts";
$route['api/getcontactsorg'] = "api/ApiController/getcontactsorg";
$route['api/getcontactsdeals'] = "api/ApiController/getcontactsdeals";
$route['api/getcontactsactivity'] = "api/ApiController/getcontactsactivity";

$route['api/getorgbyid'] = "api/ApiController/getorgbyid";
$route['api/getorgdeals'] = "api/ApiController/getorgdeals";
$route['api/getorgactivity'] = "api/ApiController/getorgactivity";


/*Organisation*/
$route['api/organisationlist'] = "api/ApiController/organisationlist";
$route['api/addclient_byapi'] = "api/ApiController/addclient_byapi";


$route['api/getcustomfields'] = "api/ApiController/getcustomfields";
$route['api/getcountries'] = "api/ApiController/getcountries";
$route['api/getstages'] = "api/ApiController/getpriorities";

$route['api/getroles'] = "api/ApiController/getroles";
$route['api/getdesignations'] = "api/ApiController/getdesignations";
$route['api/getpipeline'] = "api/ApiController/getpipeline";


/**
 * Dashboard clean route
 */
$route['admin'] = 'admin/dashboard';

/**
 * Misc controller routes
 */
$route['admin/access_denied'] = 'admin/misc/access_denied';
$route['admin/not_found']     = 'admin/misc/not_found';

/**
 * Staff Routes
 */
$route['admin/profile']           = 'admin/staff/profile';
$route['admin/profile/(:num)']    = 'admin/staff/profile/$1';
$route['admin/tasks/view/(:any)'] = 'admin/tasks/index/$1';

/**
 * Items search rewrite
 */
$route['admin/items/search'] = 'admin/invoice_items/search';

/**
 * In case if client access directly to url without the arguments redirect to clients url
 */
$route['/'] = 'clients';

/**
 * @deprecated
 */
$route['viewinvoice/(:num)/(:any)'] = 'invoice/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['invoice/(:num)/(:any)'] = 'invoice/index/$1/$2';

/**
 * @deprecated
 */
$route['viewestimate/(:num)/(:any)'] = 'estimate/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['estimate/(:num)/(:any)'] = 'estimate/index/$1/$2';
$route['subscription/(:any)']    = 'subscription/index/$1';

/**
 * @deprecated
 */
$route['viewproposal/(:num)/(:any)'] = 'proposal/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['proposal/(:num)/(:any)'] = 'proposal/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['contract/(:num)/(:any)'] = 'contract/index/$1/$2';

// For backward compatilibilty
$route['survey/(:num)/(:any)'] = 'surveys/participate/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['knowledge-base']                 = 'knowledge_base/index';
$route['knowledge-base/search']          = 'knowledge_base/search';
$route['knowledge-base/article']         = 'knowledge_base/index';
$route['knowledge-base/article/(:any)']  = 'knowledge_base/article/$1';
$route['knowledge-base/category']        = 'knowledge_base/index';
$route['knowledge-base/category/(:any)'] = 'knowledge_base/category/$1';

/**
 * @deprecated 2.2.0
 */
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'add_kb_answer') === false) {
    $route['knowledge-base/(:any)']         = 'knowledge_base/article/$1';
    $route['knowledge_base/(:any)']         = 'knowledge_base/article/$1';
    $route['clients/knowledge_base/(:any)'] = 'knowledge_base/article/$1';
    $route['clients/knowledge-base/(:any)'] = 'knowledge_base/article/$1';
}

/**
 * @deprecated 2.2.0
 * Fallback for auth clients area, changed in version 2.2.0
 */
$route['clients/reset_password']  = 'authentication/reset_password';
$route['clients/forgot_password'] = 'authentication/forgot_password';
$route['clients/logout']          = 'authentication/logout';
$route['clients/register']        = 'authentication/register';
$route['clients/login']           = 'authentication/login';

// Aliases for short routes
$route['reset_password']  = 'authentication/reset_password';
$route['forgot_password'] = 'authentication/forgot_password';
$route['login']           = 'authentication/login';
$route['logout']          = 'authentication/logout';
$route['register']        = 'authentication/register';

/**
 * Terms and conditions and Privacy Policy routes
 */
$route['terms-and-conditions'] = 'terms_and_conditions';
$route['privacy-policy']       = 'privacy_policy';

/**
 * @since 2.3.0
 * Routes for admin/modules URL because Modules.php class is used in application/third_party/MX
 */
$route['admin/modules']               = 'admin/mods';
$route['admin/modules/(:any)']        = 'admin/mods/$1';
$route['admin/modules/(:any)/(:any)'] = 'admin/mods/$1/$2';

/**
 * @since  2.3.0
 * Route for clients set password URL, because it's using the same controller for staff to
 * If user addded block /admin by .htaccess this won't work, so we need to rewrite the URL
 * In future if there is implementation for clients set password, this route should be removed
 */
$route['authentication/set_password/(:num)/(:num)/(:any)'] = 'admin/authentication/set_password/$1/$2/$3';

if (file_exists(APPPATH . 'config/my_routes.php')) {
    include_once(APPPATH . 'config/my_routes.php');
}
