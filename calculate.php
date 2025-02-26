<?php
header("Content-Type: application/json");

// API URL (Make sure it matches server.js route)
$api_url = "http://localhost:5000/api/calculations"; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method. Use POST."]);
    exit;
}

// Ensure required fields exist
if (!isset($_POST['service_id'], $_POST['country_id'], $_POST['call_type_id'], $_POST['daily_usage'], $_POST['business_days'], $_POST['aht'])) {
    echo json_encode(["error" => "Missing required parameters."]);
    exit;
}

// Prepare data for API request
$data = [
    "calculation_logic_id" => $_POST['service_id'],
    "country_id" => $_POST['country_id'],
    "call_type_id" => $_POST['call_type_id'],
    "daily_usage" => $_POST['daily_usage'],
    "business_days" => $_POST['business_days'],
    "aht" => $_POST['aht']
];

// Convert to JSON
$json_data = json_encode($data);

// cURL Request
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_status !== 200 && $http_status !== 201) {
    echo json_encode(["error" => "API request failed with status $http_status"]);
    exit;
}

// Return API response
echo $response;
?>
