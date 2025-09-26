document.addEventListener('DOMContentLoaded', function() {
    
    // --- Sidebar Active Link ---
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // --- Modal Logic for Add User pages ---
    const modal = document.getElementById('user-modal');
    const addUserBtn = document.getElementById('add-user-btn');
    const closeBtn = document.querySelector('.modal .close-btn');

    if (addUserBtn) addUserBtn.onclick = () => { modal.style.display = 'block'; }
    if (closeBtn) closeBtn.onclick = () => { modal.style.display = 'none'; }
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // --- Chatbot Logic ---
    const chatbotToggleBtn = document.getElementById('chatbot-toggle-btn');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotCloseBtn = document.getElementById('chatbot-close-btn');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSendBtn = document.getElementById('chatbot-send-btn');
    const chatbotMessages = document.getElementById('chatbot-messages');
    
    if (!chatbotToggleBtn) return; // Stop if no chatbot on page

    // I had to move the empty chatbot container to the pages that didn't have it defined
    // to prevent errors. A better approach would be a single template.
    const chatbotContainer = document.getElementById('chatbot-container');
    if (chatbotContainer && !chatbotContainer.innerHTML.trim()) {
        chatbotContainer.innerHTML = `
            <button id="chatbot-toggle-btn">Chat</button>
            <div id="chatbot-window">
                <div id="chatbot-header"><span>AI Admin Assistant</span><button id="chatbot-close-btn">&times;</button></div>
                <div id="chatbot-messages"><div class="bot-message">How can I assist you, Admin?</div></div>
                <div id="chatbot-input-container">
                    <div class="typing-indicator" id="typing-indicator" style="display: none;"><span></span><span></span><span></span></div>
                    <input type="text" id="chatbot-input" placeholder="Ask a question..."><button id="chatbot-send-btn">Send</button>
                </div>
            </div>
        `;
        // Re-assign elements after creation
        // This is a workaround for the static file setup.
        document.getElementById('chatbot-toggle-btn').addEventListener('click', toggleChatbot);
        document.getElementById('chatbot-close-btn').addEventListener('click', toggleChatbot);
        document.getElementById('chatbot-send-btn').addEventListener('click', handleSendMessage);
        document.getElementById('chatbot-input').addEventListener('keypress', (e) => e.key === 'Enter' && handleSendMessage());
    }
    
    chatbotWindow.style.display = 'none'; // Initial state

    function toggleChatbot() {
        const win = document.getElementById('chatbot-window'); // re-get
        if (win.style.display === 'none') {
            win.style.display = 'flex';
            setTimeout(() => win.classList.add('open'), 10);
        } else {
            win.classList.remove('open');
            setTimeout(() => win.style.display = 'none', 300);
        }
    };
    
    chatbotToggleBtn.addEventListener('click', toggleChatbot);
    chatbotCloseBtn.addEventListener('click', toggleChatbot);

    const adminResponses = {
        "add a doctor": "Go to 'Manage Doctors' and click the 'Add New Doctor' button. Fill in the form in the pop-up window and click 'Save'.",
        "delete a patient": "On the 'Manage Patients' page, find the patient in the table and click the red 'Delete' button. You will be asked for confirmation.",
        "view reports": "Navigate to the 'Reports & Analytics' page. From there, you can see available report types and click to download them as PDF or Excel files.",
        "change settings": "Go to the 'System Settings' page to update hospital information or perform data backups.",
        "hello": "Hello Admin! I'm here to help you manage the MESMTF system.",
        "default": "I can help with questions like 'How to add a doctor?' or 'How to view reports?'. Please try asking one of those."
    };

    function getAdminBotResponse(userInput) {
        const lowerInput = userInput.toLowerCase();
        for (const key in adminResponses) {
            if (lowerInput.includes(key)) return adminResponses[key];
        }
        return adminResponses['default'];
    };

    function handleSendMessage() {
        const input = document.getElementById('chatbot-input'); // re-get
        const messages = document.getElementById('chatbot-messages'); // re-get
        const indicator = document.getElementById('typing-indicator'); // re-get
        const messageText = input.value.trim();
        if (messageText === '') return;

        messages.innerHTML += `<div class="user-message">${messageText}</div>`;
        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        indicator.style.display = 'block';
        setTimeout(() => {
            const botResponse = getAdminBotResponse(messageText);
            messages.innerHTML += `<div class="bot-message">${botResponse}</div>`;
            indicator.style.display = 'none';
            messages.scrollTop = messages.scrollHeight;
        }, 1200);
    };

    chatbotSendBtn.addEventListener('click', handleSendMessage);
    chatbotInput.addEventListener('keypress', (e) => e.key === 'Enter' && handleSendMessage());
});