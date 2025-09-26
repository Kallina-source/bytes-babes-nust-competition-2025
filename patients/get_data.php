<?php
// Add these lines to display PHP errors for easier debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

// Database credentials
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = "";     // Your MySQL password
$dbname = "mesmtf_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get the current date for filtering
$today = date('Y-m-d');

// Fetch Today's Schedule
$today_schedule_sql = "
    SELECT 
        m.name, 
        ma.dosage, 
        ma.scheduled_time, 
        ma.status
    FROM medication_adherence AS ma
    JOIN medications AS m ON ma.medication_id = m.id
    WHERE DATE(ma.scheduled_time) = '{$today}' AND ma.user_id = 1
    ORDER BY ma.scheduled_time ASC
";
$today_schedule_result = $conn->query($today_schedule_sql);
$today_schedule = [];
// Check if the query was successful AND returned rows
if ($today_schedule_result && $today_schedule_result->num_rows > 0) {
    while($row = $today_schedule_result->fetch_assoc()) {
        $today_schedule[] = $row;
    }
}

// Fetch Adherence History (last 7 days, excluding today)
$adherence_history_sql = "
    SELECT 
        DATE(ma.scheduled_time) AS date_administered,
        m.name AS medication,
        ma.dosage,
        ma.scheduled_time,
        ma.status
    FROM medication_adherence AS ma
    JOIN medications AS m ON ma.medication_id = m.id
    WHERE DATE(ma.scheduled_time) < '{$today}' AND ma.user_id = 1
    ORDER BY ma.scheduled_time DESC
    LIMIT 7
";
$adherence_history_result = $conn->query($adherence_history_sql);
$adherence_history = [];
// Check if the query was successful AND returned rows
if ($adherence_history_result && $adherence_history_result->num_rows > 0) {
    while($row = $adherence_history_result->fetch_assoc()) {
        $row['date_administered'] = date("M j, Y", strtotime($row['date_administered']));
        $row['scheduled_time'] = date("g:i A", strtotime($row['scheduled_time']));
        $adherence_history[] = $row;
    }
}

$conn->close();

// Combine and encode data as JSON
$response = [
    "today_schedule" => $today_schedule,
    "adherence_history" => $adherence_history
];

echo json_encode($response);
?>