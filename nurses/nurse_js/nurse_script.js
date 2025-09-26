document.addEventListener('DOMContentLoaded', function() {
    
    // --- Sidebar Active Link ---
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });

    // --- Chatbot Logic ---
    const chatbotToggleBtn = document.getElementById('chatbot-toggle-btn');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotCloseBtn = document.getElementById('chatbot-close-btn');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSendBtn = document.getElementById('chatbot-send-btn');
    const chatbotMessages = document.getElementById('chatbot-messages');
    
    if (!chatbotToggleBtn) return; // Stop if no chatbot on page

    chatbotWindow.style.display = 'none'; // Initial state

    const toggleChatbot = () => {
        if (chatbotWindow.style.display === 'none') {
            chatbotWindow.style.display = 'flex';
            setTimeout(() => chatbotWindow.classList.add('open'), 10);
        } else {
            chatbotWindow.classList.remove('open');
            setTimeout(() => chatbotWindow.style.display = 'none', 300);
        }
    };
    
    chatbotToggleBtn.addEventListener('click', toggleChatbot);
    chatbotCloseBtn.addEventListener('click', toggleChatbot);

    const nurseResponses = {
        "assigned patients": "You can see your list of assigned patients on the 'Patient Monitoring' page.",
        "need meds": "Please check the 'Medication Administration' page for a full schedule of today's medications.",
        "appointments": "Today's appointments are listed on the 'Appointments Support' page. You can confirm patient arrivals there.",
        "hello": "Hello! I can help you find patient lists and medication schedules. What do you need?",
        "default": "I can answer questions like 'Which patients need meds?' or 'What are today's appointments?'. Please try one of those."
    };

    function getBotResponse(userInput) {
        const lowerInput = userInput.toLowerCase();
        for (const key in nurseResponses) {
            if (lowerInput.includes(key)) return nurseResponses[key];
        }
        return nurseResponses['default'];
    };

    function handleSendMessage() {
        const messageText = chatbotInput.value.trim();
        if (messageText === '') return;

        const userMessageElem = document.createElement('div');
        userMessageElem.className = 'user-message';
        userMessageElem.textContent = messageText;
        chatbotMessages.appendChild(userMessageElem);
        
        chatbotInput.value = '';
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        const typingIndicator = document.getElementById('typing-indicator');
        typingIndicator.style.display = 'block';

        setTimeout(() => {
            const botResponse = getBotResponse(messageText);
            const botMessageElem = document.createElement('div');
            botMessageElem.className = 'bot-message';
            botMessageElem.textContent = botResponse;
            chatbotMessages.appendChild(botMessageElem);
            
            typingIndicator.style.display = 'none';
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }, 1200);
    };

    chatbotSendBtn.addEventListener('click', handleSendMessage);
    chatbotInput.addEventListener('keypress', (e) => e.key === 'Enter' && handleSendMessage());
});