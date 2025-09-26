<?php
// Start a PHP session
session_start();

// database connection file
require_once 'db_connect.php'; 

// confirm form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect the form data and sanitize
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare a SQL statement to retrieve the user by their email
    $sql = "SELECT full_name, username, password, role FROM users WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
        
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if a user with that email exists
        if ($result->num_rows == 1) {
            // Fetch user data
            $user = $result->fetch_assoc();

            // Verify submitted password against hashed password database
            if (password_verify($password, $user['password'])) {

                // successful login
                // Store user data in session
                $_SESSION['user_id'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Update the last_login_at timestamp in the database
                $updateSql = "UPDATE users SET last_login_at = NOW() WHERE username = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("s", $user['username']);
                $updateStmt->execute();
                $updateStmt->close();

                // Redirect to the right dashboard
                $destination = '';
                switch ($user['role']) {
                    case 'patient':
                        $destination = 'patients/dashboard.php';
                        break;
                    case 'doctor':
                        $destination = 'doctor/Dashboard.php';
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

                header("Location: $destination");
                exit();

            } else {
                // Password verification failed, redirect back to login page
                header("Location: login.html?login_failed=true");
                exit();
            }
        } else {
            // No user found with that email
            header("Location: login.html?login_failed=true");
            exit();
        }

        $stmt->close();
    } else {
        // Error preparing statement
        die("Error preparing statement: " . $conn->error);
    }

    // Close connection
    $conn->close();

} else {
    // Redirect if accessed directly
    header("Location: login.html");
    exit();
}
?>