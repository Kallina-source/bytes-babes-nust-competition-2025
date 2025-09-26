<?php
// Start the PHP session
session_start();

// Include the database connection file
require_once '../db_connect.php';

// Check if the user is logged in AND if their role is 'patient'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    // If the session role is not set or is incorrect, redirect to the login page
    header("Location: ../login.html?access_denied=true");
    exit();
}

// Get the email from the session (stored as 'username')
$email = $_SESSION['username'];

// Prepare a SQL statement to get the user's information using email
$sql = "SELECT id, full_name, last_login_at FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Check if user data was found
if (!$user_data) {
    die("User not found with email: " . htmlspecialchars($email));
}

$user_id = $user_data['id'];

// Get the patient's information from the 'patients' table
// CORRECTED: We now select the 'id' column from the patients table
$patient_sql = "SELECT id, full_name, contact_number, address FROM patients WHERE user_id = ?";
$patient_stmt = $conn->prepare($patient_sql);
if (!$patient_stmt) {
    die("Error preparing patient statement: " . $conn->error);
}
$patient_stmt->bind_param("i", $user_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient_data = $patient_result->fetch_assoc();


// Use the patient's full name if found, otherwise use the user's full name
$display_name = $user_data['full_name'];

// Determine the greeting based on registration vs login
if (isset($_SESSION['first_login']) && $_SESSION['first_login']) {
    $greeting_text = "Welcome to MESMTF! Your health journey starts here.";
    unset($_SESSION['first_login']); // Clear the flag so it doesn't show again
} else {
    $greeting_text = "Welcome back.";
    // Check if it's a very recent login (within 2 minutes)
    if ($user_data['last_login_at']) {
        $timestamp_diff = time() - strtotime($user_data['last_login_at']);
        if ($timestamp_diff < 120) {
            $greeting_text = "Welcome back!";
        }
    }
}

// Check if patient data exists before running other queries
$patient_profile_complete = ($patient_data !== null);

// Initialize variables to null to prevent errors if no data is found
$next_appointment = null;
$last_diagnosis = null;
$active_medication = null;
$activities_result = null;

// If patient data is found, fetch the rest of the dynamic data
if ($patient_profile_complete) {
    // CORRECTED: Use the 'id' column as the patient identifier
    $patient_id = $patient_data['id'];

    // Fetch next appointment - Corrected SQL query to use JOIN
    $appointment_sql = "SELECT a.appointment_date, u.full_name AS doctor_name FROM appointments a JOIN users u ON a.doctor_id = u.id WHERE a.patient_id = ? AND a.appointment_date > NOW() ORDER BY a.appointment_date ASC LIMIT 1";
    $appointment_stmt = $conn->prepare($appointment_sql);
    if (!$appointment_stmt) {
        die("Error preparing appointment statement: " . $conn->error);
    }
    $appointment_stmt->bind_param("i", $patient_id);
    $appointment_stmt->execute();
    $appointment_result = $appointment_stmt->get_result();
    $next_appointment = $appointment_result->fetch_assoc();

    // Fetch last diagnosis - Corrected SQL query to use 'diagnosis' column
    $diagnosis_sql = "SELECT diagnosis, diagnosis_date FROM diagnoses WHERE patient_id = ? ORDER BY diagnosis_date DESC LIMIT 1";
    $diagnosis_stmt = $conn->prepare($diagnosis_sql);
    if (!$diagnosis_stmt) {
        die("Error preparing diagnosis statement: " . $conn->error);
    }
    $diagnosis_stmt->bind_param("i", $patient_id);
    $diagnosis_stmt->execute();
    $diagnosis_result = $diagnosis_stmt->get_result();
    $last_diagnosis = $diagnosis_result->fetch_assoc();

    // Fetch active medication - CORRECTED to match your actual table structure
    $medication_sql = "SELECT m.name, p.dosage FROM prescriptions p JOIN medications m ON p.medication_id = m.id WHERE p.patient_id = ? ORDER BY p.created_at DESC LIMIT 1";
    $medication_stmt = $conn->prepare($medication_sql);
    if (!$medication_stmt) {
        die("Error preparing medication statement: " . $conn->error);
    }
    $medication_stmt->bind_param("i", $patient_id);
    $medication_stmt->execute();
    $medication_result = $medication_stmt->get_result();
    $active_medication = $medication_result->fetch_assoc();

    // Fetch recent activities - FIXED: Specify table aliases for created_at
    $recent_activities_sql = "SELECT * FROM (
        SELECT 'Appointment' AS activity_type, a.appointment_date AS activity_date, CONCAT('with ', u.full_name) AS details, a.status FROM appointments a JOIN users u ON a.doctor_id = u.id WHERE a.patient_id = ?
        UNION ALL
        SELECT 'Diagnosis' AS activity_type, d.diagnosis_date AS activity_date, d.diagnosis AS details, 'Completed' AS status FROM diagnoses d WHERE d.patient_id = ?
        UNION ALL
        SELECT 'Prescription' AS activity_type, p.created_at AS activity_date, CONCAT(m.name, ' (', p.dosage, ')') AS details, p.status FROM prescriptions p JOIN medications m ON p.medication_id = m.id WHERE p.patient_id = ?
    ) AS recent_activities ORDER BY activity_date DESC LIMIT 10";

    $recent_activities_stmt = $conn->prepare($recent_activities_sql);
    if (!$recent_activities_stmt) {
        die("Error preparing recent activities statement: " . $conn->error);
    }
    $recent_activities_stmt->bind_param("iii", $patient_id, $patient_id, $patient_id);
    $recent_activities_stmt->execute();
    $activities_result = $recent_activities_stmt->get_result();
    // Close prepared statements
    $appointment_stmt->close();
    $diagnosis_stmt->close();
    $medication_stmt->close();
    $recent_activities_stmt->close();
}

// Close other statements and connection
$stmt->close();
$patient_stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MESMTF</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>MESMTF</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="medical_records.php" class="nav-link">Medical Records</a>
                <a href="appointment.php" class="nav-link">Appointments</a>
                <a href="diagnosis.php" class="nav-link">AI Diagnosis</a>
                <a href="treatment.php" class="nav-link">Treatment</a>
                <a href="pharmacy.php" class="nav-link">Pharmacy</a>
                <a href="drug_administration.php" class="nav-link">Drug Administration</a>
                <a href="reports.php" class="nav-link">Reports</a>
                <a href="educational_resources.php" class="nav-link">Resources</a>
                <a href="forum.php" class="nav-link">Forum</a>
                <a href="#" id="logout-btn" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <h1>Dashboard</h1>
                <p>Hello, <strong><?php echo htmlspecialchars($display_name); ?>!</strong> <?php echo $greeting_text; ?></p>
            </header>
            
            <div class="content-area">
                <div class="card-container">
                    <div class="card">
                        <h4>Next Appointment</h4>
                        <?php if ($next_appointment): ?>
                            <p class="card-main-text"><?php echo date('M d, Y - h:i A', strtotime($next_appointment['appointment_date'])); ?></p>
                            <p>with <?php echo htmlspecialchars($next_appointment['doctor_name']); ?></p>
                        <?php else: ?>
                            <p class="card-main-text">No Upcoming Appointments</p>
                        <?php endif; ?>
                    </div>
                    <div class="card">
                        <h4>Last Diagnosis</h4>
                        <?php if ($last_diagnosis): ?>
                            <p class="card-main-text"><?php echo htmlspecialchars($last_diagnosis['diagnosis']); ?></p>
                            <p>on <?php echo date('M d, Y', strtotime($last_diagnosis['diagnosis_date'])); ?></p>
                        <?php else: ?>
                            <p class="card-main-text">No Diagnosis Found</p>
                        <?php endif; ?>
                    </div>
                    <div class="card">
                        <h4>Active Medications</h4>
                        <?php if ($active_medication): ?>
                            <p class="card-main-text"><?php echo htmlspecialchars($active_medication['name']); ?></p>
                            <p><?php echo htmlspecialchars($active_medication['dosage']); ?></p>
                        <?php else: ?>
                            <p class="card-main-text">No Active Medications</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="content-section">
                    <h2>Recent Activity</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activity Type</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($activities_result && $activities_result->num_rows > 0): ?>
                                    <?php while($row = $activities_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['activity_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                                        <td><span class="status-completed"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4">No recent activity found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <div id="chatbot-container">
        <button id="chatbot-toggle-btn">Chat</button>
        <div id="chatbot-window">
            <div id="chatbot-header">
                <span>AI Assistant</span>
                <button id="chatbot-close-btn">&times;</button>
            </div>
            <div id="chatbot-messages">
                <div class="bot-message">Hello! How can I help you today?</div>
            </div>
            <div id="chatbot-input-container">
                <div class="typing-indicator" id="typing-indicator" style="display: none;">
                    <span></span><span></span><span></span>
                </div>
                <input type="text" id="chatbot-input" placeholder="Ask a question...">
                <button id="chatbot-send-btn">Send</button>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>