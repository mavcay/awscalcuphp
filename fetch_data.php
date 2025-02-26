<?php
header("Content-Type: application/json");

$api_url = "http://localhost:5000/api"; 

$action = $_POST['action'];

if ($action == "get_countries") {
    $region_id = $_POST['region_id'];
    echo file_get_contents("$api_url/countries?region_id=$region_id");
}
?>
