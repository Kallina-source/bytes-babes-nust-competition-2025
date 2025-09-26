<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.html");
    exit();
}

$email = $_SESSION['username'];
$user_id = null;
$patient_data = null;

// Get user_id from the users table
$sql_user = "SELECT id FROM users WHERE email = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($user = $result_user->fetch_assoc()) {
    $user_id = $user['id'];
}
$stmt_user->close();

if ($user_id) {
    // Check if a patient record already exists for this user
    $sql_patient = "SELECT id, full_name, date_of_birth, gender, contact_number, address, medical_history FROM patients WHERE user_id = ?";
    $stmt_patient = $conn->prepare($sql_patient);
    $stmt_patient->bind_param("i", $user_id);
    $stmt_patient->execute();
    $result_patient = $stmt_patient->get_result();
    $patient_data = $result_patient->fetch_assoc();
    $stmt_patient->close();
}

$conn->close();

$is_readonly = ($patient_data !== null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - MESMTF</title>
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
                <a href="medical_records.php" class="nav-link active">Medical Records</a>
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
                <h1>Medical Records</h1>
                <p>Manage your personal health information.</p>
            </header>
            
            <div class="content-area">
                <div class="content-section">
                    <h2>Personal Information</h2>
                    <form id="medical_record_form" class="record-form">
                        <input type="hidden" id="patient_id" name="patient_id" value="<?php echo htmlspecialchars($patient_data['id'] ?? ''); ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($patient_data['full_name'] ?? ''); ?>" <?php echo $is_readonly ? 'readonly' : ''; ?>>
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($patient_data['date_of_birth'] ?? ''); ?>" <?php echo $is_readonly ? 'readonly' : ''; ?>>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" <?php echo $is_readonly ? 'disabled' : ''; ?>>
                                    <option value="male" <?php echo ($patient_data['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($patient_data['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo ($patient_data['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contact_number">Contact Info</label>
                                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($patient_data['contact_number'] ?? ''); ?>" <?php echo $is_readonly ? 'readonly' : ''; ?>>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($patient_data['address'] ?? ''); ?>" <?php echo $is_readonly ? 'readonly' : ''; ?>>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="medical_history">Medical History</label>
                            <textarea id="medical_history" name="medical_history" rows="4" <?php echo $is_readonly ? 'readonly' : ''; ?>><?php echo htmlspecialchars($patient_data['medical_history'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary" id="save-btn" style="display: none;">Save Changes</button>
                            <button type="button" class="btn-primary" id="edit-btn">Edit Information</button>
                            <button type="button" class="btn-secondary" id="cancel-btn" style="display: none;">Cancel</button>
                        </div>
                    </form>
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
        const form = document.getElementById('medical_record_form');
        const editBtn = document.getElementById('edit-btn');
        const saveBtn = document.getElementById('save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const formFields = form.querySelectorAll('input, select, textarea');

        // Initial state of buttons based on if a record exists
        const hasRecord = !!document.getElementById('patient_id').value;
        if (hasRecord) {
            if (editBtn) editBtn.style.display = 'inline-block';
            if (saveBtn) saveBtn.style.display = 'none';
            if (cancelBtn) cancelBtn.style.display = 'none';
        } else {
            if (editBtn) editBtn.style.display = 'none';
            if (saveBtn) saveBtn.style.display = 'inline-block';
            if (cancelBtn) cancelBtn.style.display = 'none';
            formFields.forEach(field => {
                field.removeAttribute('readonly');
                field.removeAttribute('disabled');
            });
        }

        function toggleEditMode(enable) {
            formFields.forEach(field => {
                if (enable) {
                    field.removeAttribute('readonly');
                    field.removeAttribute('disabled');
                } else {
                    field.setAttribute('readonly', 'readonly');
                    if (field.tagName.toLowerCase() === 'select') {
                        field.setAttribute('disabled', 'disabled');
                    }
                }
            });
            if (editBtn) editBtn.style.display = enable ? 'none' : 'inline-block';
            if (saveBtn) saveBtn.style.display = enable ? 'inline-block' : 'none';
            if (cancelBtn) cancelBtn.style.display = enable ? 'inline-block' : 'none';
        }

        // Event listeners for the buttons
        if (editBtn) {
            editBtn.addEventListener('click', function() {
                toggleEditMode(true);
            });
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                window.location.reload(); 
            });
        }

        // Form submission logic
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('update_patient_info.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
    </script>
</body>
</html>