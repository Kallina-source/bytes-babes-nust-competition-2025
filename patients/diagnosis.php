<?php
session_start();
require_once '../db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../login.html");
    exit();
}

$email = $_SESSION['username'];
$sql = "SELECT full_name FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Diagnosis - MESMTF</title>
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
                <a href="diagnosis.php" class="nav-link active">AI Diagnosis</a>
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
                <h1>AI Diagnosis Module</h1>
                <p>Enter your symptoms to get a preliminary analysis.</p>
            </header>
            
            <div class="content-area">
                <div class="card">
                    <form id="diagnosis-form">
                        <div class="content-section">
                            <h2>Step 1: Select Your Symptoms</h2>
                            <div class="symptom-list">
                                <label><input type="checkbox" name="symptoms[]" value="Fever"> Fever</label>
                                <label><input type="checkbox" name="symptoms[]" value="Headache"> Headache</label>
                                <label><input type="checkbox" name="symptoms[]" value="Chills"> Chills</label>
                                <label><input type="checkbox" name="symptoms[]" value="Nausea"> Nausea</label>
                                <label><input type="checkbox" name="symptoms[]" value="Vomiting"> Vomiting</label>
                                <label><input type="checkbox" name="symptoms[]" value="Diarrhea"> Diarrhea</label>
                                <label><input type="checkbox" name="symptoms[]" value="Fatigue"> Fatigue</label>
                                <label><input type="checkbox" name="symptoms[]" value="Muscle Aches"> Muscle Aches</label>
                                <label><input type="checkbox" name="symptoms[]" value="Sweating"> Sweating</label>
                                <label><input type="checkbox" name="symptoms[]" value="Dry Cough"> Dry Cough</label>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Get AI Diagnosis</button>
                        </div>
                    </form>
                </div>

                <div class="card diagnosis-result" id="diagnosis-result" style="display: none;">
                    <div class="content-section">
                        <h2>Step 2: Preliminary Result</h2>
                        <div id="diagnosis-content">
                            </div>
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
    <script>
    document.getElementById('diagnosis-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const symptoms = formData.getAll('symptoms[]');

        if (symptoms.length === 0) {
            alert('Please select at least one symptom.');
            return;
        }

        const diagnosisResultDiv = document.getElementById('diagnosis-result');
        const diagnosisContentDiv = document.getElementById('diagnosis-content');

        // Show a loading message
        diagnosisContentDiv.innerHTML = '<h4>Generating Diagnosis...</h4><p>Please wait a moment.</p>';
        diagnosisResultDiv.style.display = 'block';

        // Send the selected symptoms to the backend
        fetch('diagnose.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let resultHtml = `<h4>Potential Diagnosis: <span class="highlight">${data.diagnosis}</span></h4>`;
                resultHtml += `<p>${data.message}</p>`;

                if (data.recommendations) {
                    resultHtml += '<h4>Recommended Next Steps:</h4><ul>';
                    data.recommendations.forEach(rec => {
                        resultHtml += `<li>${rec}</li>`;
                    });
                    resultHtml += '</ul>';
                }
                
                diagnosisContentDiv.innerHTML = resultHtml;
            } else {
                diagnosisContentDiv.innerHTML = `<h4 class="error-message">Error: ${data.message}</h4>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            diagnosisContentDiv.innerHTML = `<h4 class="error-message">An unexpected error occurred.</h4>`;
        });
    });
    </script>
</body>
</html>