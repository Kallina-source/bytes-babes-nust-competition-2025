<?php
session_start();
require_once '../db_connect.php';

// Check if a POST request was made and the user is logged in as a patient
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username']) && $_SESSION['role'] === 'patient') {
    // Get the user's ID from the form
    $user_id = $_POST['user_id'];
    
    // Sanitize and validate form data
    $full_name = filter_var($_POST['full_name'], FILTER_SANITIZE_STRING);
    $date_of_birth = filter_var($_POST['date_of_birth'], FILTER_SANITIZE_STRING);
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
    $contact_number = filter_var($_POST['contact_number'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $medical_history = filter_var($_POST['medical_history'], FILTER_SANITIZE_STRING);

    // Check if a patient record already exists for this user_id
    $check_sql = "SELECT patient_id FROM patients WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if ($check_stmt === false) {
        die("Prepare check failed: " . $conn->error);
    }
    
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Patient record exists, so UPDATE it
        $update_sql = "UPDATE patients SET full_name = ?, date_of_birth = ?, gender = ?, contact_number = ?, address = ?, medical_history = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        
        if ($update_stmt === false) {
            die("Prepare update failed: " . $conn->error);
        }
        
        $update_stmt->bind_param("ssssssi", $full_name, $date_of_birth, $gender, $contact_number, $address, $medical_history, $user_id);
        
        if ($update_stmt->execute()) {
            // Success: redirect back to the records page
            header("Location: medical_records.php?status=success");
            exit();
        } else {
            // Error
            echo "Error updating record: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        // Patient record does NOT exist, so INSERT a new one
        // A simple patient ID generator
        $patient_id = "MES-" . str_pad($user_id, 4, '0', STR_PAD_LEFT);
        $insert_sql = "INSERT INTO patients (user_id, patient_id, full_name, date_of_birth, gender, contact_number, address, medical_history) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        
        if ($insert_stmt === false) {
            die("Prepare insert failed: " . $conn->error);
        }
        
        $insert_stmt->bind_param("isssssss", $user_id, $patient_id, $full_name, $date_of_birth, $gender, $contact_number, $address, $medical_history);
        
        if ($insert_stmt->execute()) {
            // Success: redirect back to the records page
            header("Location: medical_records.php?status=success");
            exit();
        } else {
            // Error
            echo "Error inserting record: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    // If not a POST request, redirect back to the records page
    header("Location: medical_records.php");
    exit();
}
?>