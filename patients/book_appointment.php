<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in and is a patient
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Get the user's ID from the session to find the patient record
$email = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$user_id = $user_data['id'];
$stmt->close();

// Get the patient ID from the patients table
// CORRECTED: Select the 'id' column instead of 'patient_id'
$patient_sql = "SELECT id FROM patients WHERE user_id = ?";
$patient_stmt = $conn->prepare($patient_sql);
$patient_stmt->bind_param("i", $user_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient_data = $patient_result->fetch_assoc();
// CORRECTED: Use the 'id' from the result to get the patient_id
$patient_id = $patient_data['id'] ?? null;
$patient_stmt->close();

// Check if patient ID was found
if (!$patient_id) {
    echo json_encode(['success' => false, 'message' => 'Patient record not found. Please complete your medical records first.']);
    exit();
}

// Get data from the POST request
$doctor_id = $_POST['doctor'] ?? '';
$app_date = $_POST['app_date'] ?? '';
$app_time = $_POST['app_time'] ?? '';

// Basic validation
if (empty($doctor_id) || empty($app_date) || empty($app_time)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

// Insert new appointment record
$insert_sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')";
$insert_stmt = $conn->prepare($insert_sql);
if (!$insert_stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}
$insert_stmt->bind_param("iiss", $patient_id, $doctor_id, $app_date, $app_time);

if ($insert_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $insert_stmt->error]);
}

$insert_stmt->close();
$conn->close();
?>