document.addEventListener('DOMContentLoaded', () => {
    // --- Sidebar Toggle for Mobile ---
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // --- Active Sidebar Link ---
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });

    // --- Chatbot ---
    const initializeChatbot = (container) => {
        const chatbotToggle = container.querySelector('.chatbot-toggle');
        const chatbotWindow = container.querySelector('.chatbot-window');
        const chatbotInput = container.querySelector('.chatbot-input');
        const chatbotMessages = container.querySelector('.chatbot-messages');
        const typingIndicator = container.querySelector('.typing-indicator');

        if (!chatbotToggle) return;

        chatbotToggle.addEventListener('click', () => {
            chatbotWindow.style.display = chatbotWindow.style.display === 'flex' ? 'none' : 'flex';
        });

        chatbotInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const userMessage = chatbotInput.value.trim();
                if (userMessage) {
                    addMessage(userMessage, 'user', chatbotMessages);
                    chatbotInput.value = '';
                    simulateBotResponse(userMessage, chatbotMessages, typingIndicator);
                }
            }
        });
    };
    
    function addMessage(text, sender, messageContainer) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender);
        messageElement.textContent = text;
        messageContainer.appendChild(messageElement);
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    function simulateBotResponse(userMessage, messageContainer, typingIndicator) {
        if(typingIndicator) typingIndicator.style.display = 'block';
        setTimeout(() => {
            let botResponse = "Sorry, I can only answer questions about stock levels. Try asking 'stock for Coartem'.";
            if (userMessage.toLowerCase().includes('stock for coartem')) {
                botResponse = "There are 50 units of Coartem remaining in stock. The expiry date is 12/2026.";
            } else if (userMessage.toLowerCase().includes('stock for paracetamol')) {
                botResponse = "Paracetamol is low in stock with only 15 units left. A re-order is recommended.";
            }
            if(typingIndicator) typingIndicator.style.display = 'none';
            addMessage(botResponse, 'bot', messageContainer);
        }, 1500);
    }
    
    // --- Initialize or Replace Chatbot ---
    document.querySelectorAll('.chatbot').forEach(container => {
        if (!container.querySelector('.chatbot-toggle')) {
            container.innerHTML = `
                <button class="chatbot-toggle">Chat</button>
                <div class="chatbot-window" style="display: none;">
                    <div class="chatbot-header">AI Assistant</div>
                    <div class="chatbot-messages"><div class="message bot">How can I help you?</div></div>
                    <div class="chatbot-input-area">
                        <div class="typing-indicator" style="display: none;"><span></span><span></span><span></span></div>
                        <textarea class="chatbot-input" placeholder="Ask a question..." rows="1"></textarea>
                    </div>
                </div>
            `;
        }
        initializeChatbot(container);
    });
});