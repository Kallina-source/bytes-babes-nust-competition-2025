<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.html");
    exit();
}

$email = $_SESSION['username'];
$user_id = null;

$sql_user = "SELECT id FROM users WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
if ($stmt_user === false) {
    die("Error preparing user statement: " . $conn->error);
}
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$user_id = $user_data['id'] ?? null;
$stmt_user->close();

if (!$user_id) {
    die("User not found.");
}

// Fetch active treatment plans by joining with the diagnoses table
$sql_active = "SELECT t.medication, t.dosage, t.duration, t.status, d.created_at AS completion_date
               FROM treatments t
               JOIN diagnoses d ON t.diagnosis_id = d.id
               WHERE d.patient_id = ? AND t.status = 'active'
               ORDER BY t.created_at DESC";
$stmt_active = $conn->prepare($sql_active);
if ($stmt_active === false) {
    die("Error preparing active treatments statement: " . $conn->error);
}
$stmt_active->bind_param("i", $user_id);
$stmt_active->execute();
$result_active = $stmt_active->get_result();

// Fetch completed treatment plans by joining with the diagnoses table
$sql_completed = "SELECT t.medication, t.dosage, t.duration, t.created_at AS completion_date
                  FROM treatments t
                  JOIN diagnoses d ON t.diagnosis_id = d.id
                  WHERE d.patient_id = ? AND t.status = 'completed'
                  ORDER BY t.created_at DESC";
$stmt_completed = $conn->prepare($sql_completed);
if ($stmt_completed === false) {
    die("Error preparing completed treatments statement: " . $conn->error);
}
$stmt_completed->bind_param("i", $user_id);
$stmt_completed->execute();
$result_completed = $stmt_completed->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatment & Prescriptions - MESMTF</title>
    <link rel="stylesheet" href="css/style.css">
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
                <a href="appointment.php" class="nav-link">Appointments</a>
                <a href="diagnosis.php" class="nav-link">AI Diagnosis</a>
                <a href="treatment.php" class="nav-link active">Treatment</a>
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
                <h1>Treatment & Prescriptions</h1>
                <p>View your current and past treatment plans.</p>
            </header>
            
            <div class="content-area">
                <div class="content-section">
                    <h2>Current Treatment Plan</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_active->num_rows > 0): ?>
                                    <?php while($row = $result_active->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['medication']); ?></td>
                                            <td><?php echo htmlspecialchars($row['dosage']); ?></td>
                                            <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                            <td><span class="status-active"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No current treatment plans found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="content-section">
                    <button class="btn-secondary" disabled>Download Prescription (PDF)</button>
                </div>

                <div class="content-section">
                    <h2>Completed Treatment Plans</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                    <th>Completion Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_completed->num_rows > 0): ?>
                                    <?php while($row = $result_completed->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['medication']); ?></td>
                                            <td><?php echo htmlspecialchars($row['dosage']); ?></td>
                                            <td><?php echo htmlspecialchars($row['duration']); ?></td>
                                            <td><?php echo htmlspecialchars($row['completion_date']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No completed treatment plans found.</td>
                                    </tr>
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
                <div class="bot-message">Ask me anything about your health.</div>
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