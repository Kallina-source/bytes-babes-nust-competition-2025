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
    const chatbotToggle = document.querySelector('.chatbot-toggle');
    const chatbotWindow = document.querySelector('.chatbot-window');
    const chatbotInput = document.querySelector('.chatbot-input');
    const chatbotMessages = document.querySelector('.chatbot-messages');
    const typingIndicator = document.querySelector('.typing-indicator');

    if (chatbotToggle) { // Check if chatbot elements exist on the page
        chatbotToggle.addEventListener('click', () => {
            chatbotWindow.style.display = chatbotWindow.style.display === 'flex' ? 'none' : 'flex';
        });

        chatbotInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const userMessage = chatbotInput.value.trim();
                if (userMessage) {
                    addMessage(userMessage, 'user');
                    chatbotInput.value = '';
                    simulateBotResponse(userMessage);
                }
            }
        });
    }
    
    function addMessage(text, sender) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender);
        messageElement.textContent = text;
        chatbotMessages.appendChild(messageElement);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    function simulateBotResponse(userMessage) {
        typingIndicator.style.display = 'block';
        setTimeout(() => {
            let botResponse = "I'm sorry, I can only provide information on medication dosages. Please ask about a specific medicine like Coartem or Paracetamol.";
            if (userMessage.toLowerCase().includes('coartem')) {
                botResponse = "The standard adult dosage for Coartem (20/120mg) is 4 tablets as an initial dose, 4 tablets again after 8 hours, and then 4 tablets twice daily for the following 2 days.";
            } else if (userMessage.toLowerCase().includes('paracetamol')) {
                botResponse = "For adults, the typical dosage of Paracetamol is 500mg to 1000mg every 4 to 6 hours, not exceeding 4000mg in 24 hours.";
            }
            typingIndicator.style.display = 'none';
            addMessage(botResponse, 'bot');
        }, 1500);
    }
    
    // --- Appointments Page: Filter Logic ---
    const filterButtons = document.querySelectorAll('.filter-buttons .btn');
    const appointmentRows = document.querySelectorAll('tbody tr[data-date]');
    if(filterButtons.length > 0) {
        const today = new Date('2025-09-23').toISOString().split('T')[0];
        
        const filterAppointments = (filter) => {
            appointmentRows.forEach(row => {
                const rowDate = row.dataset.date;
                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === 'today') {
                    row.style.display = rowDate === today ? '' : 'none';
                } else if (filter === 'week') {
                    // Simplified: Show today and tomorrow for demo
                    const isThisWeek = (new Date(rowDate) >= new Date(today) && new Date(rowDate) <= new Date('2025-09-28'));
                    row.style.display = isThisWeek ? '' : 'none';
                }
            });
        };

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                filterAppointments(button.dataset.filter);
            });
        });

        // Initial filter
        filterAppointments('today');
    }

    // --- Patients Page: Modal Logic ---
    const patientRows = document.querySelectorAll('.patient-row');
    const modal = document.getElementById('patient-modal');
    const closeBtn = document.querySelector('.modal .close-btn');
    const patientDetails = document.getElementById('patient-details');

    if (modal) {
        const fakePatientData = {
            'P001': { name: 'Tuupoo Ndishishi', history: 'Diagnosed with Malaria on 15 Sep 2025.', prescription: 'Coartem 20mg/120mg.' },
            'P002': { name: 'Tangi Ashipala', history: 'Routine check-up on 18 Sep 2025.', prescription: 'None.' },
            'P003': { name: 'Maria Shaanika', history: 'Diagnosed with Typhoid on 22 Sep 2025.', prescription: 'Ciprofloxacin 500mg.' }
        };

        patientRows.forEach(row => {
            row.addEventListener('click', () => {
                const patientId = row.cells[0].textContent;
                const data = fakePatientData[patientId];
                patientDetails.innerHTML = `
                    <p><strong>Name:</strong> ${data.name}</p>
                    <p><strong>Medical History:</strong> ${data.history}</p>
                    <p><strong>Prescriptions:</strong> ${data.prescription}</p>
                `;
                modal.style.display = 'block';
            });
        });

        closeBtn.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (e) => {
            if (e.target == modal) modal.style.display = 'none';
        });
    }

    // --- Replace dummy chatbot divs on other pages ---
    // This is a simple way to include the chatbot on all pages without repeating the full HTML
    document.querySelectorAll('.chatbot').forEach(el => {
        if (el.innerHTML.includes('...')) {
            el.innerHTML = `
                <button class="chatbot-toggle">Chat</button>
                <div class="chatbot-window" style="display: none;">
                    <div class="chatbot-header">AI Assistant</div>
                    <div class="chatbot-messages">
                        <div class="message bot">Hello Dr. Smith. How can I assist you?</div>
                    </div>
                    <div class="chatbot-input-area">
                        <div class="typing-indicator" style="display: none;"><span></span><span></span><span></span></div>
                        <textarea class="chatbot-input" placeholder="Ask a question..." rows="1"></textarea>
                    </div>
                </div>
            `;
        }
    });
});