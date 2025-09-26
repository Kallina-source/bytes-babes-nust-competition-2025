<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Dashboard - MESMTF</title>
    <link rel="stylesheet" href="nurse_css/nurse_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>MESMTF NURSE</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="nurse_dashboard.html" class="nav-link active">Dashboard</a>
                <a href="nurse_patient_monitoring.html" class="nav-link">Patient Monitoring</a>
                <a href="nurse_medication_administration.html" class="nav-link">Medication Administration</a>
                <a href="nurse_appointments_support.html" class="nav-link">Appointments Support</a>
                <a href="index.html" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <h1>Dashboard</h1>
                <p>Hello, <strong>Nurse Nangolo!</strong> Here is your summary for today, September 22, 2025.</p>
            </header>
            
            <div class="content-area">
                <div class="card-container">
                    <div class="card">
                        <h4>Today's Appointments</h4>
                        <p class="card-main-text">12</p>
                    </div>
                    <div class="card">
                        <h4>Assigned Patients</h4>
                        <p class="card-main-text">8</p>
                    </div>
                    <div class="card">
                        <h4>Medications to Administer</h4>
                        <p class="card-main-text">5</p>
                    </div>
                </div>

                <div class="content-section">
                    <h2>Quick Actions</h2>
                    <div class="quick-actions">
                         <a href="nurse_patient_monitoring.html" class="btn-primary">View Assigned Patients</a>
                         <a href="nurse_medication_administration.html" class="btn-secondary">Check Medication Schedule</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="chatbot-container">
        <button id="chatbot-toggle-btn">Chat</button>
        <div id="chatbot-window">
            <div id="chatbot-header"><span>AI Assistant</span><button id="chatbot-close-btn">&times;</button></div>
            <div id="chatbot-messages"><div class="bot-message">Welcome! Ask "What are my assigned patients?" to get started.</div></div>
            <div id="chatbot-input-container">
                <div class="typing-indicator" id="typing-indicator" style="display: none;"><span></span><span></span><span></span></div>
                <input type="text" id="chatbot-input" placeholder="Ask a question..."><button id="chatbot-send-btn">Send</button>
            </div>
        </div>
    </div>
    
    <script src="nurse_js/nurse_script.js"></script>
</body>
</html>