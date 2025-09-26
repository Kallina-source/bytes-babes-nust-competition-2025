<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - MESMTF</title>
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
                <a href="pharmacy.php" class="nav-link">Pharmacy</a>
                <a href="drug_administration.php" class="nav-link">Drug Administration</a>
                <a href="reports.php" class="nav-link active">Reports</a>
                <a href="educational_resources.php" class="nav-link">Resources</a>
                <a href="forum.php" class="nav-link">Forum</a>
                <a href="#" id="logout-btn" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <h1>Generate Reports</h1>
                <p>Download or share your medical history.</p>
            </header>
            
            <div class="content-area">
                <div class="content-section">
                    <h2>Available Reports</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Report Type</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Diagnosis History</td>
                                    <td>A complete list of all past diagnoses.</td>
                                    <td>
                                        <button class="btn-primary">Download (PDF)</button>
                                        <button class="btn-secondary">Share</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Appointment History</td>
                                    <td>A log of all past and upcoming appointments.</td>
                                    <td>
                                        <button class="btn-primary">Download (PDF)</button>
                                        <button class="btn-secondary">Share</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Medication History</td>
                                    <td>A list of all prescribed medications, both active and completed.</td>
                                    <td>
                                        <button class="btn-primary">Download (PDF)</button>
                                        <button class="btn-secondary">Share</button>
                                    </td>
                                </tr>
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
                 <div class="bot-message">I can help explain what each report contains. Just ask!</div>
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