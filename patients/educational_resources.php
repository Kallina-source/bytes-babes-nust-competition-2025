<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educational Resources - MESMTF</title>
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
                <a href="reports.php" class="nav-link">Reports</a>
                <a href="educational_resources.php" class="nav-link active">Resources</a>
                <a href="forum.php" class="nav-link">Forum</a>
<a href="#" id="logout-btn" class="nav-link logout">Logout</a>            
</nav>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <h1>Educational Resources</h1>
                <p>Learn more about Malaria, Typhoid, and general health.</p>
            </header>
            
            <div class="content-area">
                <div class="content-section">
                    <h2>Informational Videos</h2>
                    <div class="card-container">
                        <div class="card resource-card">
                            <h4>What is Malaria?</h4>
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/gse5h5wL9yQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                        <div class="card resource-card">
                            <h4>Understanding Typhoid Fever</h4>
                            <div class="video-container">
                                <iframe src="https://www.youtube.com/embed/JZ1Oo2zI__Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-section">
                    <h2>Helpful Articles & Guides</h2>
                    <ul class="resource-list">
                        <li><a href="#">How to Prevent Malaria and Typhoid</a></li>
                        <li><a href="#">Managing Medication Side Effects</a></li>
                        <li><a href="#">Guide to a Healthy Diet During Recovery</a></li>
                        <li><a href="#">Understanding Your Blood Test Results (PDF)</a></li>
                    </ul>
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
                 <div class="bot-message">Ask "Which video should I watch for fever?" to get a recommendation.</div>
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