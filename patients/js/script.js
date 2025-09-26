document.addEventListener('DOMContentLoaded', function() {
    
    // --- Sidebar Active Link ---
    // This part ensures the correct sidebar link is highlighted on each page.
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // --- Drug Administration Page Logic ---
    // This code only runs if the elements for the drug administration page exist.
    const todayScheduleContainer = document.getElementById('today-schedule-container');
    const adherenceHistoryBody = document.getElementById('adherence-history-body');

    if (todayScheduleContainer && adherenceHistoryBody) {
        
        // Function to render today's medication schedule
        const renderTodaySchedule = (schedule) => {
            todayScheduleContainer.innerHTML = ''; // Clear existing content
            schedule.forEach(medication => {
                const card = document.createElement('div');
                card.classList.add('card');
    
                const buttonText = medication.status === 'taken' ? 'Marked' : 'Mark as Taken';
                const buttonClass = medication.status === 'taken' ? 'btn-secondary' : 'btn-primary';
                const buttonDisabled = medication.status === 'taken' ? 'disabled' : '';
    
                const formattedTime = new Date(medication.scheduled_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                
                card.innerHTML = `
                    <h4>${medication.name} (${medication.dosage})</h4>
                    <p class="card-main-text">Due: ${formattedTime}</p>
                    <p>Status: <strong class="highlight">${medication.status}</strong></p>
                    <button class="${buttonClass}" ${buttonDisabled}>${buttonText}</button>
                `;
                todayScheduleContainer.appendChild(card);
            });
        };
    
        // Function to render adherence history
        const renderAdherenceHistory = (history) => {
            adherenceHistoryBody.innerHTML = ''; // Clear existing content
            history.forEach(log => {
                const row = document.createElement('tr');
                const statusClass = `status-${log.status.toLowerCase()}`;
                row.innerHTML = `
                    <td>${log.date_administered}</td>
                    <td>${log.medication}</td>
                    <td>${log.scheduled_time}</td>
                    <td><span class="${statusClass}">${log.status}</span></td>
                `;
                adherenceHistoryBody.appendChild(row);
            });
        };
    
        // Fetch data from the PHP endpoint
        fetch('get_data.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                renderTodaySchedule(data.today_schedule);
                renderAdherenceHistory(data.adherence_history);
            })
            .catch(error => {
                console.error('There was a problem fetching the data:', error);
                todayScheduleContainer.innerHTML = '<p>Failed to load medication data. Please try again later.</p>';
                adherenceHistoryBody.innerHTML = '<tr><td colspan="4">Failed to load history data.</td></tr>';
            });
    }

    // --- Reports Page Logic ---
    const adherenceRateValue = document.getElementById('adherence-rate-value');
    const dosesTakenValue = document.getElementById('doses-taken-value');
    const dosesMissedValue = document.getElementById('doses-missed-value');
    const dosesPendingValue = document.getElementById('doses-pending-value');

    if (adherenceRateValue) {
        fetch('get_reports.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

                adherenceRateValue.textContent = `${data.adherence_rate}%`;
                dosesTakenValue.textContent = data.taken;
                dosesMissedValue.textContent = data.missed;
                dosesPendingValue.textContent = data.pending;
            })
            .catch(error => {
                console.error('There was a problem fetching the reports:', error);
                adherenceRateValue.textContent = 'N/A';
                dosesTakenValue.textContent = 'N/A';
                dosesMissedValue.textContent = 'N/A';
                dosesPendingValue.textContent = 'N/A';
            });
    }

    // --- Chatbot Logic ---
    const chatbotToggleBtn = document.getElementById('chatbot-toggle-btn');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotCloseBtn = document.getElementById('chatbot-close-btn');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSendBtn = document.getElementById('chatbot-send-btn');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const typingIndicator = document.getElementById('typing-indicator');

    // Hide the window initially, then enable transitions for open/close
    if (chatbotWindow) {
        chatbotWindow.style.display = 'none';
    }

    const toggleChatbot = () => {
        if (chatbotWindow) {
            if (chatbotWindow.style.display === 'none') {
                chatbotWindow.style.display = 'flex';
                setTimeout(() => chatbotWindow.classList.add('open'), 10);
            } else {
                chatbotWindow.classList.remove('open');
                setTimeout(() => chatbotWindow.style.display = 'none', 300);
            }
        }
    };

    if (chatbotToggleBtn) chatbotToggleBtn.addEventListener('click', toggleChatbot);
    if (chatbotCloseBtn) chatbotCloseBtn.addEventListener('click', toggleChatbot);

    // Dictionary of simple responses
    const responses = {
        "register": "To register, go to the main page, click the 'Register' button, fill in your details, and click 'Register'. Your Patient ID will be generated automatically.",
        "reschedule": "To reschedule an appointment, go to the 'Appointments' page, find the upcoming appointment you wish to change, and click the 'Reschedule' button.",
        "diagnosis mean": "On the Medical Records page, you can see past diagnoses. A diagnosis is a conclusion reached by a doctor about the illness you have. For details on a specific term, feel free to ask me!",
        "p. falciparum": "Plasmodium falciparum is a parasite that causes the most dangerous form of malaria in humans. It's important to treat it quickly.",
        "symptoms of typhoid": "Common symptoms of Typhoid include a sustained high fever, weakness, stomach pain, headache, and loss of appetite. Sometimes a rash of flat, rose-colored spots may appear.",
        "take coartem": "Coartem is usually taken twice a day for three days. It's very important to take it with food (like milk, or a meal) to help your body absorb the medicine. Always complete the full course even if you feel better.",
        "pharmacy has my drug": "The system shows availability at the main hospital pharmacy. For other locations, I recommend calling your local pharmacies like 'Central Pharmacy' or 'Rhino Park Pharmacy' to confirm stock.",
        "dose today": "Please check the 'Drug Administration' page for your adherence log. It shows which doses have been marked as 'Taken' for today.",
        "start a thread": "To start a new thread in the forum, go to the 'Forum' page and click the 'Create New Thread' button at the top right.",
        "video for fever": "I recommend watching the 'What is Malaria?' video, as fever is a primary symptom of malaria. You can find it on the 'Educational Resources' page.",
        "hello": "Hello! I'm the MESMTF AI assistant. How can I help you navigate the patient portal?",
        "default": "I'm sorry, I don't have information on that. I can help with questions about navigating this portal, understanding medical terms related to Malaria/Typhoid, and using the system features."
    };

    const getBotResponse = (userInput) => {
        const lowerInput = userInput.toLowerCase();
        for (const key in responses) {
            if (lowerInput.includes(key)) {
                return responses[key];
            }
        }
        return responses['default'];
    };

    const handleSendMessage = () => {
        const message = chatbotInput.value.trim();
        if (message === '') return;

        const userMessageElem = document.createElement('div');
        userMessageElem.className = 'user-message';
        userMessageElem.textContent = message;
        chatbotMessages.appendChild(userMessageElem);

        chatbotInput.value = '';
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

        typingIndicator.style.display = 'block';
        setTimeout(() => {
            const botResponse = getBotResponse(message);
            const botMessageElem = document.createElement('div');
            botMessageElem.className = 'bot-message';
            botMessageElem.textContent = botResponse;
            chatbotMessages.appendChild(botMessageElem);
            
            typingIndicator.style.display = 'none';
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }, 1500);
    };
    
    if (chatbotSendBtn) chatbotSendBtn.addEventListener('click', handleSendMessage);
    if (chatbotInput) chatbotInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleSendMessage();
        }
    });

  // --- Logout Logic ---
const logoutBtn = document.getElementById('logout-btn');

if (logoutBtn) {
    logoutBtn.addEventListener('click', function(event) {
        // Prevent the default link behavior
        event.preventDefault(); 
        
        // TEMPORARY TEST: Add this line
        alert("Logout button clicked!");
        
        // Redirect to the home page (which is your login page)
        window.location.href = 'index.html';
    });
}

});