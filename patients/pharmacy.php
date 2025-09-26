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

// Fetch prescribed medications by joining treatments and diagnoses tables
$sql_prescriptions = "SELECT t.medication, t.status, t.created_at
                      FROM treatments t
                      JOIN diagnoses d ON t.diagnosis_id = d.id
                      WHERE d.patient_id = ?
                      ORDER BY t.created_at DESC";
$stmt_prescriptions = $conn->prepare($sql_prescriptions);
if ($stmt_prescriptions === false) {
    die("Error preparing prescriptions statement: " . $conn->error);
}
$stmt_prescriptions->bind_param("i", $user_id);
$stmt_prescriptions->execute();
$result_prescriptions = $stmt_prescriptions->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy - MESMTF</title>
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
                <a href="treatment.php" class="nav-link">Treatment</a>
                <a href="pharmacy.php" class="nav-link active">Pharmacy</a>
                <a href="drug_administration.php" class="nav-link">Drug Administration</a>
                <a href="reports.php" class="nav-link">Reports</a>
                <a href="educational_resources.php" class="nav-link">Resources</a>
                <a href="forum.php" class="nav-link">Forum</a>
                <a href="#" id="logout-btn" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <h1>Pharmacy</h1>
                <p>Check medication availability and request refills.</p>
            </header>
            
            <div class="content-area">
                <div class="content-section">
                    <h2>Your Prescribed Medications</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Prescription Date</th>
                                    <th>Status</th>
                                    <th>Hospital Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_prescriptions->num_rows > 0): ?>
                                    <?php while($row = $result_prescriptions->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['medication']); ?></td>
                                            <td><?php echo htmlspecialchars(date('M d, Y', strtotime($row['created_at']))); ?></td>
                                            <td><span class="status-active"><?php echo htmlspecialchars(ucfirst($row['status'])); ?></span></td>
                                            <td><span class="status-available">Available</span></td>
                                            <td>
                                                <?php if ($row['status'] == 'completed'): ?>
                                                    <button class="btn-sm btn-secondary">Request Refill</button>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No prescribed medications found.</td>
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