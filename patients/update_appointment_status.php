<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$appointment_id = $_POST['appointment_id'] ?? null;
$new_status = $_POST['status'] ?? null;

if (!$appointment_id || !$new_status) {
    echo json_encode(['success' => false, 'message' => 'Missing appointment ID or new status.']);
    exit();
}

// Ensure the new status is a valid value to prevent SQL injection
$valid_statuses = ['cancelled', 'rescheduled'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status provided.']);
    exit();
}

try {
    // We need to verify that the patient owns this appointment for security
    $patient_email = $_SESSION['username'];
    $sql_check_owner = "SELECT a.id FROM appointments a 
                        JOIN users p ON a.patient_id = p.id
                        WHERE a.id = ? AND p.email = ?";
    $stmt_check_owner = $conn->prepare($sql_check_owner);
    $stmt_check_owner->bind_param("is", $appointment_id, $patient_email);
    $stmt_check_owner->execute();
    $result_check_owner = $stmt_check_owner->get_result();

    if ($result_check_owner->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or you do not have permission to modify it.']);
        $stmt_check_owner->close();
        $conn->close();
        exit();
    }
    $stmt_check_owner->close();

    // Update the status of the appointment
    $sql_update = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $new_status, $appointment_id);

    if ($stmt_update->execute()) {
        echo json_encode(['success' => true, 'message' => 'Appointment status updated.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment: ' . $stmt_update->error]);
    }

    $stmt_update->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>