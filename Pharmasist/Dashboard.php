<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pharmacist Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="page-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>MESMTF Pharmacy</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="Dashboard.html" class="nav-link active">Dashboard</a>
                <a href="Prescriptions.html" class="nav-link">Prescriptions</a>
                <a href="Inventory.html" class="nav-link">Inventory</a>
                <a href="Patients.html" class="nav-link">Patients</a>
                <a href="Reports.html" class="nav-link">Reports</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="navbar">
                <div class="nav-left">
                    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
                    <h2>Dashboard</h2>
                </div>
                <div class="nav-right">
                    <div class="profile">Thomas Amunyela</div>
                    <a href="index.html" class="logout-btn">Logout</a>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="stats-container">
                    <div class="stat-card card">
                        <h4>Pending Prescriptions</h4>
                        <p>12</p>
                    </div>
                    <div class="stat-card card low-stock">
                        <h4>Medicines Low in Stock</h4>
                        <p>3</p>
                    </div>
                    <div class="stat-card card">
                        <h4>Completed Orders Today</h4>
                        <p>8</p>
                    </div>
                </div>

                <div class="activity-container card">
                    <h3>Priority Tasks</h3>
                    <ul class="activity-list">
                        <li><span>Prescription for Tuupoo Ndishishi is waiting for pickup.</span><span class="activity-time">Status: Pending</span></li>
                        <li><span>Stock for Paracetamol is low (15 units remaining).</span><span class="activity-time">Action: Re-order</span></li>
                        <li><span>Prescription for John Petrus dispensed.</span><span class="activity-time">Status: Completed</span></li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <div class="chatbot">
        <button class="chatbot-toggle">Chat</button>
        <div class="chatbot-window">
            <div class="chatbot-header">AI Assistant</div>
            <div class="chatbot-messages"><div class="message bot">Welcome, Thomas. How can I help you today?</div></div>
            <div class="chatbot-input-area">
                <div class="typing-indicator"><span></span><span></span><span></span></div>
                <textarea class="chatbot-input" placeholder="Ask a question..." rows="1"></textarea>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>