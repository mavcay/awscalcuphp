<?php
header("Content-Type: application/json");

// API URL
$api_url = "http://localhost:5000/api/calculations";

// Prepare data for API request
$data = array(
    "calculation_logic_id" => $_POST['service_id'], 
    "country_id" => $_POST['country_id'],
    "daily_usage" => $_POST['daily_usage'],
    "business_days" => $_POST['business_days'],
    "aht" => $_POST['aht']
);

$json_data = json_encode($data);

// Initialize cURL
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

// Execute request and get response
$response = curl_exec($ch);
curl_close($ch);

// Return full API response to frontend
echo $response;
?>