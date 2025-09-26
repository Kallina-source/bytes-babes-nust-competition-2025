<?php
// Display detailed PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

// Database credentials
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "mesmtf_db"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$user_id = 1; // We'll continue to use a hardcoded user ID for now

// Query to get all medication adherence data for the user
$sql = "
    SELECT status
    FROM medication_adherence 
    WHERE user_id = {$user_id}
";

$result = $conn->query($sql);

$adherence_stats = [
    'taken' => 0,
    'pending' => 0,
    'missed' => 0,
    'total' => 0,
    'adherence_rate' => 0
];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $adherence_stats['total']++;
        if (isset($adherence_stats[$row['status']])) {
            $adherence_stats[$row['status']]++;
        }
    }
    
    // Calculate the adherence rate
    if ($adherence_stats['total'] > 0) {
        $adherence_stats['adherence_rate'] = round(($adherence_stats['taken'] / $adherence_stats['total']) * 100, 2);
    }
}

$conn->close();

echo json_encode($adherence_stats);
?>