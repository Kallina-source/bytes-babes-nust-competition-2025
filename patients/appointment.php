<?php
session_start();
require_once '../db_connect.php';

// Check if the user is logged in as a patient
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.html");
    exit();
}

// Get the user's ID from the session
$email = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$user_id = $user_data['id'];
$stmt->close();

// Get the patient_id from the patients table
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

// --- 1. Fetch a list of doctors to populate the dropdown ---
$doctors_sql = "SELECT id, full_name FROM users WHERE role = 'doctor'";
$doctors_result = $conn->query($doctors_sql);

// --- 2. Fetch all upcoming appointments for this patient ---
$upcoming_appointments_result = null;
if ($patient_id) {
    $upcoming_sql = "SELECT a.appointment_date, a.appointment_time, a.status, u.full_name AS doctor_name
                     FROM appointments a
                     JOIN users u ON a.doctor_id = u.id
                     WHERE a.patient_id = ? AND a.appointment_date >= CURDATE()
                     ORDER BY a.appointment_date ASC";
    $upcoming_stmt = $conn->prepare($upcoming_sql);
    $upcoming_stmt->bind_param("i", $patient_id);
    $upcoming_stmt->execute();
    $upcoming_appointments_result = $upcoming_stmt->get_result();
    $upcoming_stmt->close();
}

// --- 3. Fetch all past appointments for this patient ---
$past_appointments_result = null;
if ($patient_id) {
    $past_sql = "SELECT a.appointment_date, a.appointment_time, a.status, u.full_name AS doctor_name
                 FROM appointments a
                 JOIN users u ON a.doctor_id = u.id
                 WHERE a.patient_id = ? AND a.appointment_date < CURDATE()
                 ORDER BY a.appointment_date DESC";
    $past_stmt = $conn->prepare($past_sql);
    $past_stmt->bind_param("i", $patient_id);
    $past_stmt->execute();
    $past_appointments_result = $past_stmt->get_result();
    $past_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - MESMTF</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>MESMTF</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="medical_records.php" class="nav-link">Medical Records</a>
                <a href="appointment.php" class="nav-link active">Appointments</a>
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
                <h1>Appointment Management</h1>
                <p>Book, view, or reschedule your appointments.</p>
            </header>
            
            <div class="content-area">
                <div class="content-section">
                    <h2>Book a New Appointment</h2>
                    <form id="appointment_form" class="record-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="doctor">Select Doctor</label>
                                <select id="doctor" name="doctor">
                                    <?php if ($doctors_result && $doctors_result->num_rows > 0): ?>
                                        <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($doctor['id']); ?>"><?php echo htmlspecialchars($doctor['full_name']); ?></option>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <option value="">No doctors available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="app-date">Select Date</label>
                                <input type="date" id="app-date" name="app_date">
                            </div>
                            <div class="form-group">
                                <label for="app-time">Select Time</label>
                                <input type="time" id="app-time" name="app_time">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-primary" id="book_button">Book Appointment</button>
                        </div>
                    </form>
                </div>

                <div class="content-section">
                    <h2>Upcoming Appointments</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($upcoming_appointments_result && $upcoming_appointments_result->num_rows > 0): ?>
                                    <?php while($row = $upcoming_appointments_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($row['appointment_date'])) . ' - ' . date('h:i A', strtotime($row['appointment_time'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                            <td><span class="status-active"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                            <td>
                                                <button class="btn-secondary">Reschedule</button>
                                                <button class="btn-danger">Cancel</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4">No upcoming appointments found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="content-section">
                    <h2>Past Appointments</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($past_appointments_result && $past_appointments_result->num_rows > 0): ?>
                                    <?php while($row = $past_appointments_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($row['appointment_date'])) . ' - ' . date('h:i A', strtotime($row['appointment_time'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                            <td><span class="status-completed"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3">No past appointments found.</td></tr>
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
                <div class="bot-message">Ask me "How do I reschedule?" for help.</div>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('appointment_form');
        const bookButton = document.getElementById('book_button');

        if (form && bookButton) {
            bookButton.addEventListener('click', function(event) {
                event.preventDefault(); // Stop the page from reloading

                // Get form data
                const formData = new FormData(form);

                fetch('book_appointment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Success: ' + data.message);
                        window.location.reload(); // Reloads the page to show the new appointment
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                    console.error('Fetch error:', error);
                });
            });
        }
    });
    </script>
</body>
</html>   