<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$full_name = $_POST['full_name'] ?? '';
$date_of_birth = $_POST['date_of_birth'] ?? '';
$gender = $_POST['gender'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$address = $_POST['address'] ?? '';
$medical_history = $_POST['medical_history'] ?? '';
$user_id = null; // We will get this from the session below
$patient_id = $_POST['patient_id'] ?? null; // We now get the 'id' from the hidden field

// Get user_id from the session
$email = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$user_id = $user_data['id'];
$stmt->close();

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID not found.']);
    exit();
}

if ($patient_id) {
    // If patient_id exists, update the existing record
    // We now use the 'id' column in the WHERE clause
    $sql = "UPDATE patients SET full_name = ?, date_of_birth = ?, gender = ?, contact_number = ?, address = ?, medical_history = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $full_name, $date_of_birth, $gender, $contact_number, $address, $medical_history, $patient_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Medical record updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update record: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    // If no patient_id exists, insert a new record
    $sql = "INSERT INTO patients (user_id, full_name, date_of_birth, gender, contact_number, address, medical_history) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $full_name, $date_of_birth, $gender, $contact_number, $address, $medical_history);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Medical record created successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create record: ' . $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>