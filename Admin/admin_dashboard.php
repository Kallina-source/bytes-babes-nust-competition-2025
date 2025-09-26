<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MESMTF</title>
    <link rel="stylesheet" href="admin_css/admin_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>MESMTF ADMIN</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="admin_dashboard.html" class="nav-link active">Dashboard</a>
                <a href="admin_manage_patients.html" class="nav-link">Manage Patients</a>
                <a href="admin_manage_doctors.html" class="nav-link">Manage Doctors</a>
                <a href="admin_manage_nurses.html" class="nav-link">Manage Nurses</a>
                <a href="admin_manage_pharmacists.html" class="nav-link">Manage Pharmacists</a>
                <a href="admin_appointments.html" class="nav-link">Appointments</a>
                <a href="admin_reports.html" class="nav-link">Reports & Analytics</a>
                <a href="admin_settings.html" class="nav-link">System Settings</a>
                <a href="index.html" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <h1>Admin Dashboard</h1>
                <p>Hello, <strong>Admin!</strong> Welcome to the management portal.</p>
            </header>
            
            <div class="content-area">
                <div class="card-container">
                    <div class="card">
                        <h4>Total Patients</h4>
                        <p class="card-main-text">342</p>
                    </div>
                    <div class="card">
                        <h4>Total Doctors</h4>
                        <p class="card-main-text">15</p>
                    </div>
                    <div class="card">
                        <h4>Total Nurses</h4>
                        <p class="card-main-text">48</p>
                    </div>
                    <div class="card">
                        <h4>Appointments Today</h4>
                        <p class="card-main-text">27</p>
                    </div>
                </div>

                <div class="content-section">
                    <h2>System Analytics</h2>
                    <div class="charts-container">
                        <div class="chart-card">
                            <h4>Patient Registrations (Monthly)</h4>
                            <div class="chart-placeholder">
                                
                                <p>(Chart showing patient growth)</p>
                            </div>
                        </div>
                        <div class="chart-card">
                            <h4>Appointments by Department</h4>
                            <div class="chart-placeholder">
                                
                                <p>(Chart showing department workload)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="chatbot-container">
        <button id="chatbot-toggle-btn">Chat</button>
        <div id="chatbot-window">
            <div id="chatbot-header"><span>AI Admin Assistant</span><button id="chatbot-close-btn">&times;</button></div>
            <div id="chatbot-messages"><div class="bot-message">Welcome, Admin! Ask me how to manage users or generate reports.</div></div>
            <div id="chatbot-input-container">
                <div class="typing-indicator" id="typing-indicator" style="display: none;"><span></span><span></span><span></span></div>
                <input type="text" id="chatbot-input" placeholder="Ask a question..."><button id="chatbot-send-btn">Send</button>
            </div>
        </div>
    </div>
    
    <script src="admin_js/admin_script.js"></script>
</body>
</html>