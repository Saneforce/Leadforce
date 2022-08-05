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