<?php
header('Content-Type: application/json');

// Get the search term from the query string, e.g., ?search=aspirin
$search = isset($_GET['search']) ? urlencode($_GET['search']) : 'aspirin';

// OpenFDA endpoint
$url = "https://api.fda.gov/drug/label.json?search=openfda.brand_name:$search&limit=1";

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute request
$response = curl_exec($ch);
curl_close($ch);

// Output the response directly
echo $response;
?>