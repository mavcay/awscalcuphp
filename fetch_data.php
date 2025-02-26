<?php
header("Content-Type: application/json");

// Base API URL
$api_url = "http://localhost:5000/api"; 

$action = $_POST['action'];

if ($action == "get_countries") {
    $region_id = $_POST['region_id'];
    $response = file_get_contents("$api_url/countries?region_id=$region_id");
    echo $response;
}

if ($action == "get_services") {
    $response = file_get_contents("$api_url/services");
    echo $response;
}

if ($action == "get_call_types") {
    $service_id = $_POST['service_id'];
    $response = file_get_contents("$api_url/call-types?service_id=$service_id");
    echo $response;
}
?>
