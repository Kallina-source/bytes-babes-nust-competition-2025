<?php
// the database connection file
require_once 'db_connect.php';

// Checking if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // confirm if passwords matches
    if ($_POST['password'] !== $_POST['confirmPassword']) {

        // redirect back to registration page if mismatch
        header("Location: register.html?error=password_mismatch");
        exit();
    }
    
    // Collecting and sanitizing form data
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $userType = trim($_POST['userType']); 
    $password = $_POST['password']; 
    
    $username = $email; 

    // current timestamp for the first login
    $currentTime = date('Y-m-d H:i:s');

    // hashing password for security purpose
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

     // Prepare the SQL statement to insert data with the new 'last_login_at' column
    $sql = "INSERT INTO users (full_name, email, phone, address, username, password, role, last_login_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        
        $stmt->bind_param("ssssssss", $fullName, $email, $phone, $address, $username, $hashed_password, $userType, $currentTime);
        
        // Execute the prepared statement
        if ($stmt->execute()) {
            // if registration is successful, redirect user to the right dashboard based on role selected
            // Check the user type and set the destination URL
            $destination = '';
            switch ($userType) {
                case 'patient':
                    $destination = 'patients/dashboard.php';
                    break;
                case 'doctor':
                    $destination = 'doctor/Dashboard.html';
                    break;
                case 'nurse':
                    $destination = 'nurses/nurse_dashboard.php';
                    break;
                case 'pharmacist':
                    $destination = 'Pharmasist/Dashboard.php';
                    break;
                case 'medical_receptionist':
                    $destination = 'medical_receptionist_dashboard.php';
                    break;
                case 'admin':
                    $destination = 'Admin/admin_dashboard.php';
                    break;
                default:
                    // Fallback in case of an invalid user type
                    $destination = 'login.html';
                    break;
            }
            
            // Redirecting the user to the right dashboard/interface
            header("Location: $destination");
            exit();

        } else {
            // if error occurs during execution
            echo "Error: " . $stmt->error;
        }

        // Closing statement
        $stmt->close();

    } else {
        // Error preparing the statement
        echo "Error preparing statement: " . $conn->error;
    }

    // Close database connection
    $conn->close();

} else {
    // If someone tries to access this page directly, they should be redirected 
    header("Location: register.html");
    exit();
}
?>