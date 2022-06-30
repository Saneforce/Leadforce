<?php
echo "<pre>";
print_r(apache_get_modules());
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, X-Requested-With, Content-Type, access_token, Access_token, AccessToken");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');

foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
print_r('$request');print_r($request);print_r($request);
exit();