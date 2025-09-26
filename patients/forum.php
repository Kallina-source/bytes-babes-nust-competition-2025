<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Forum - MESMTF</title>
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
                <a href="educational_resources.php" class="nav-link">Resources</a>
                <a href="forum.php" class="nav-link active">Forum</a>
                <a href="#" id="logout-btn" class="nav-link logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <h1>Patient Forum</h1>
                <p>Share experiences and ask questions in our community.</p>
            </header>
            
            <div class="forum-layout">
                <div class="thread-list-container">
                    <div class="forum-header">
                        <h2>Discussions</h2>
                        <button id="new-thread-btn" class="btn-primary">New Thread</button>
                    </div>
                    <ul id="thread-list">
                        <li class="thread-item active" data-thread-id="1">
                            <h5>Coping with Coartem side effects?</h5>
                            <p>Last post by Maria S. - Today at 11:45 AM</p>
                        </li>
                        <li class="thread-item" data-thread-id="2">
                            <h5>Best foods to eat when recovering from Typhoid?</h5>
                            <p>Last post by John P. - Yesterday at 4:30 PM</p>
                        </li>
                         <li class="thread-item" data-thread-id="3">
                            <h5>How long did your fatigue last after Malaria?</h5>
                            <p>Last post by Tuupoo N. - Yesterday at 9:00 AM</p>
                        </li>
                    </ul>
                </div>

                <div id="thread-content-container" class="thread-content-container">
                    <div class="thread-content active" id="thread-1">
                        <h3>Coping with Coartem side effects?</h3>
                        <div class="posts-container">
                            <div class="post">
                                <p class="post-meta"><strong>Maria S.</strong> on Sep 21, 2025</p>
                                <p>Has anyone else experienced dizziness while taking Coartem? It's really affecting my day. Any tips for managing it?</p>
                            </div>
                             <div class="post">
                                <p class="post-meta"><strong>Dr. Ashipala (Doctor)</strong> on Sep 21, 2025</p>
                                <p>Hi Maria, dizziness can be a side effect. Make sure you are taking the medication with a fatty meal or milk to improve absorption and reduce side effects. Also, stay well-hydrated and avoid sudden movements. If it persists, please book a follow-up.</p>
                            </div>
                        </div>
                        <form class="reply-form">
                            <textarea placeholder="Write your reply..."></textarea>
                            <button type="submit" class="btn-primary">Post Reply</button>
                        </form>
                    </div>
                    <div class="thread-content" id="thread-2">
                        <h3>Best foods to eat when recovering from Typhoid?</h3>
                        <div class="posts-container">
                            <div class="post">
                                <p class="post-meta"><strong>John P.</strong> on Sep 20, 2025</p>
                                <p>I'm finally starting to feel better after Typhoid but have no appetite. What did everyone else eat during recovery?</p>
                            </div>
                        </div>
                         <form class="reply-form">
                            <textarea placeholder="Write your reply..."></textarea>
                            <button type="submit" class="btn-primary">Post Reply</button>
                        </form>
                    </div>
                    <div class="thread-content" id="thread-3">
                        <h3>How long did your fatigue last after Malaria?</h3>
                        <div class="posts-container">
                             <div class="post">
                                <p class="post-meta"><strong>Tuupoo N.</strong> on Sep 19, 2025</p>
                                <p>I finished my medication a week ago but I still feel so tired all the time. Is this normal? How long did it take others to get their energy back?</p>
                            </div>
                        </div>
                         <form class="reply-form">
                            <textarea placeholder="Write your reply..."></textarea>
                            <button type="submit" class="btn-primary">Post Reply</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="new-thread-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Create a New Discussion Thread</h2>
            <form id="new-thread-form">
                <div class="form-group">
                    <label for="thread-title">Thread Title</label>
                    <input type="text" id="thread-title" placeholder="Enter a descriptive title" required>
                </div>
                <div class="form-group">
                    <label for="thread-post">Your Post</label>
                    <textarea id="thread-post" rows="5" placeholder="Start the discussion here..." required></textarea>
                </div>
                <button type="submit" class="btn-primary">Create Thread</button>
            </form>
        </div>
    </div>

    <div id="chatbot-container">
        <button id="chatbot-toggle-btn">Chat</button>
        <div id="chatbot-window">
             <div id="chatbot-header">
                <span>AI Assistant</span>
                <button id="chatbot-close-btn">&times;</button>
            </div>
            <div id="chatbot-messages">
                 <div class="bot-message">Ask "How do I start a thread?" for instructions.</div>
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