<?php

// for activities
$route['api/activities']['get'] = 'api/activities/getall';
$route['api/activities']['post'] = 'api/activities/post';
$route['api/activities/(:num)']['put'] = 'api/activities/put/$1';
$route['api/activities/(:num)']['delete'] = 'api/activities/delete/$1';
$route['api/activities/(:num)']['get'] = 'api/activities/get/$1';
$route['api/activities/customfields']['get'] = 'api/activities/getcustomfields';
$route['api/activities/done/(:num)']['put'] = 'api/activities/markasdone/$1';
$route['api/activities/undone/(:num)']['put'] = 'api/activities/unmarkasdone/$1';
$route['api/activities/types']['get'] = 'api/activities/gettypes';
$route['api/activities/priorities']['get'] = 'api/activities/getpriorities';
$route['api/activities/relatedto']['get'] = 'api/activities/getrelatedto';


// for staffs
$route['api/staffs']['get'] = 'api/staffs/getall';


//for relational data
$route['api/relationdata/(:any)']['get'] = 'api/relationdata/getall/$1';
$route['api/relationdata/(:any)/(:any)']['get'] = 'api/relationdata/getall/$1/$2';

//for organizations
$route['api/organizations/deal/(:num)']['get'] = 'api/organizations/getbydeal/$1';

//for persons
$route['api/persons/deal/(:num)']['get'] = 'api/persons/getbydeal/$1';

// for authentication
$route['api/authentication/login/']['post'] = 'api/authentication/login';
$route['api/authentication/forgotpassword/']['post'] = 'api/authentication/forgotpassword';