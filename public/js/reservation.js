let reservationState = {
    currentStep: 1,
    selectedCourt: null,
    selectedDate: null,
    selectedTime: null,
    selectedDuration: 1.5,
    playerCount: 4,
    requirements: ''
};

var courtPrice = 25;
let bookedSlots = [];
let isSubmittingReservation = false;
let authenticatedUser = null;

const courtNames = {
    1: 'Court 1 - Cupra',
    2: 'Court 2 - Dechatlon',
    3: 'Court 3 - Codeforces'
};

document.addEventListener('DOMContentLoaded', async function() {
    console.log('DOM fully loaded');

    const isAuthenticated = await checkAuthentication();
    if (!isAuthenticated) {
        return;
    }

    initializeDatePicker();
    initializeTimeSlots();
    initializeMobileMenu();
    loadSavedState();
    updateStepDisplay();
    updateSummary();
    
    hideDurationSelector();
    setupButtonListeners();
    setupReservationDetails();
    setupReturnButton();
});

function checkAuthentication() {
    return fetch('/auth/status')
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                alert('Il faut s\'authentifier d\'abord pour réserver un terrain.');
                window.location.href = '/login';
                return false;
            }

            authenticatedUser = data.user;
            return true;
        })
        .catch(error => {
            console.error('Authentication check failed:', error);
            alert('Il faut s\'authentifier d\'abord pour réserver un terrain.');
            window.location.href = '/login';
            return false;
        });
}

function setupReturnButton() {
    const returnBtn = document.getElementById('returnToStep1');
    if (returnBtn) {
        returnBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Returning to step 1');
            resetReservation();
            goToStep(1);
        });
    }
}

function goToStep(step) {
    reservationState.currentStep = step;
    updateStepDisplay();
    
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        successMessage.classList.add('hidden');
    }
}

function resetReservation() {
    reservationState.selectedCourt = null;
    reservationState.selectedDate = null;
    reservationState.selectedTime = null;
    
    document.querySelectorAll('.court-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    const dateInput = document.getElementById('reservation-date');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = tomorrow.toISOString().split('T')[0];
        reservationState.selectedDate = dateInput.value;
    }
    
    reservationState.selectedTime = null;
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    updateSummary();
}

function setupButtonListeners() {
    console.log('Setting up button listeners');
    
    document.querySelectorAll('.btn-select-court').forEach((button, index) => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const courtId = index + 1;
            courtPrice = (courtId === 3) ? 15 : 25;
            console.log('Court selected:', courtId);
            selectCourt(courtId);
        });
    });
    
    const backToStep1 = document.querySelector('#step2 .btn-secondary');
    if (backToStep1) {
        backToStep1.addEventListener('click', function(e) {
            e.preventDefault();
            goToStep(1);
        });
    }
    
    const continueToStep3 = document.querySelector('#step2 .btn-primary');
    if (continueToStep3) {
        continueToStep3.addEventListener('click', function(e) {
            e.preventDefault();
            if (validateStep2()) {
                goToStep(3);
            }
        });
    }

    const backToStep2 = document.querySelector('#step3 .btn-secondary');
    if (backToStep2) {
        backToStep2.addEventListener('click', function(e) {
            e.preventDefault();
            goToStep(2);
        });
    }

    const continueToStep4 = document.querySelector('#step3 .btn-primary');
    if (continueToStep4) {
        continueToStep4.addEventListener('click', function(e) {
            e.preventDefault();
            syncReservationDetails();
            goToStep(4);
        });
    }
    
    const backToStep3 = document.querySelector('#step4 .btn-secondary');
    if (backToStep3) {
        backToStep3.addEventListener('click', function(e) {
            e.preventDefault();
            goToStep(3);
        });
    }
    
    const confirmBtn = document.querySelector('#step4 .btn-primary');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function(e) {
            e.preventDefault();
            confirmBooking();
        });
    }
}

function validateStep2() {
    syncDateFromInput();

    if (!reservationState.selectedDate) {
        alert('Veuillez choisir une date.');
        return false;
    }
    if (!reservationState.selectedTime) {
        alert('Veuillez choisir un créneau.');
        return false;
    }
    if (bookedSlots.includes(reservationState.selectedTime.substring(0, 5))) {
        alert('Ce créneau est déjà réservé. Veuillez choisir un autre créneau.');
        reservationState.selectedTime = null;
        updateTimeSlots();
        return false;
    }
    return true;
}

function syncDateFromInput() {
    const dateInput = document.getElementById('reservation-date');
    if (dateInput && dateInput.value) {
        reservationState.selectedDate = dateInput.value;
    }
}

function setupReservationDetails() {
    const playerCount = document.getElementById('player-count');
    const requirements = document.getElementById('reservation-requirements');

    if (playerCount) {
        playerCount.value = String(reservationState.playerCount || 4);
        playerCount.addEventListener('change', syncReservationDetails);
    }

    if (requirements) {
        requirements.value = reservationState.requirements || '';
        requirements.addEventListener('input', syncReservationDetails);
    }
}

function syncReservationDetails() {
    const playerCount = document.getElementById('player-count');
    const requirements = document.getElementById('reservation-requirements');

    reservationState.playerCount = playerCount ? parseInt(playerCount.value, 10) : 4;
    reservationState.requirements = requirements ? requirements.value.trim() : '';
    updateSummary();
}

function selectCourt(courtId) {
    document.querySelectorAll('.court-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    const selectedCard = document.querySelector(`[data-court="${courtId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    reservationState.selectedCourt = courtId;
    updateSummary();
    loadBookedSlots();
    goToStep(2);
}

function hideDurationSelector() {
    const durationSelector = document.querySelector('.duration-selector');
    if (durationSelector) {
        durationSelector.style.display = 'none';
    }
}

function initializeMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');
    if (menuBtn && navMenu) {
        menuBtn.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }
}

function initializeDatePicker() {
    const dateInput = document.getElementById('reservation-date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = tomorrow.toISOString().split('T')[0];
        reservationState.selectedDate = dateInput.value;
        
        dateInput.addEventListener('change', function(e) {
            reservationState.selectedDate = e.target.value;
            reservationState.selectedTime = null;
            loadBookedSlots();
        });
    }
}

function initializeTimeSlots() {
    updateTimeSlots();
}

function updateTimeSlots() {
    const slotGrid = document.getElementById('time-slots');
    if (!slotGrid) return;
    
    slotGrid.innerHTML = '';
    
    for (let hour = 8; hour <= 22; hour += 1.5) {
        const startHour = Math.floor(hour);
        const startMinute = (hour % 1) * 60;
        const timeString = `${startHour.toString().padStart(2, '0')}:${startMinute.toString().padStart(2, '0')}:00`;
        const displayTime = `${startHour.toString().padStart(2, '0')}:${startMinute.toString().padStart(2, '0')}`;
        
        const slot = document.createElement('div');
        slot.className = 'time-slot';
        slot.textContent = displayTime;

        if (bookedSlots.includes(displayTime)) {
            continue;
        }
        
        slot.addEventListener('click', function() {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            reservationState.selectedTime = timeString;
            updateSummary();
        });
        
        slotGrid.appendChild(slot);
    }
}

function loadBookedSlots() {
    if (!reservationState.selectedCourt || !reservationState.selectedDate) {
        bookedSlots = [];
        updateTimeSlots();
        return;
    }

    const params = new URLSearchParams({
        court_number: reservationState.selectedCourt,
        reservation_date: reservationState.selectedDate
    });

    fetch(`/reservation/slots?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            bookedSlots = data.success ? data.bookedSlots : [];
            if (reservationState.selectedTime && bookedSlots.includes(reservationState.selectedTime.substring(0, 5))) {
                reservationState.selectedTime = null;
            }
            updateTimeSlots();
            updateSummary();
        })
        .catch(error => {
            console.error('Failed to load booked slots:', error);
            bookedSlots = [];
            updateTimeSlots();
        });
}

function updateStepDisplay() {
    document.querySelectorAll('.reservation-step').forEach(step => {
        step.classList.add('hidden');
    });
    
    const currentStepElement = document.getElementById(`step${reservationState.currentStep}`);
    if (currentStepElement) {
        currentStepElement.classList.remove('hidden');
    }
    
    document.querySelectorAll('.progress-step').forEach((step, index) => {
        if (index + 1 <= reservationState.currentStep) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
    
    if (reservationState.currentStep === 4) {
        updateSummary();
    }
}

function updateSummary() {
    const courtSummary = document.getElementById('summary-court');
    if (courtSummary && reservationState.selectedCourt) {
        courtSummary.textContent = courtNames[reservationState.selectedCourt] || 'Court ' + reservationState.selectedCourt;
    }
    
    const dateSummary = document.getElementById('summary-date');
    if (dateSummary && reservationState.selectedDate) {
        const date = new Date(reservationState.selectedDate);
        dateSummary.textContent = date.toLocaleDateString('fr-FR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
    
    const timeSummary = document.getElementById('summary-time');
    if (timeSummary) {
        timeSummary.textContent = reservationState.selectedTime ? reservationState.selectedTime.substring(0, 5) : 'Not selected';
    }

    const playersSummary = document.getElementById('summary-players');
    if (playersSummary) {
        playersSummary.textContent = `${reservationState.playerCount || 4} joueurs`;
    }

    const requirementsSummary = document.getElementById('summary-requirements');
    if (requirementsSummary) {
        requirementsSummary.textContent = reservationState.requirements || 'Aucune';
    }
    
    const totalSummary = document.getElementById('summary-total');
    if (totalSummary) {
        totalSummary.textContent = `€${courtPrice}.00`;
    }
}

function confirmBooking() {
    if (isSubmittingReservation) {
        return;
    }

    if (!reservationState.selectedCourt || !validateStep2()) {
        return;
    }

    isSubmittingReservation = true;
    const confirmBtn = document.getElementById('btn-confirm-final');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Réservation...';
    }

    console.log('Envoi de la réservation au serveur...', reservationState);
    
    // Préparation des données pour le backend PHP
    const formData = new FormData();
    formData.append('court_number', reservationState.selectedCourt);
    formData.append('reservation_date', reservationState.selectedDate);
    formData.append('reservation_time', reservationState.selectedTime);
    formData.append('player_count', reservationState.playerCount || 4);
    formData.append('requirements', reservationState.requirements || '');

    // Envoi asynchrone vers PHP
    fetch('/reservation/save', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Réservation confirmée.');
            document.querySelectorAll('.reservation-step').forEach(step => {
                step.classList.add('hidden');
            });
            
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.classList.remove('hidden');
            }
            reservationState.selectedTime = null;
            loadBookedSlots();
        } else {
            if (data.loginRequired) {
                alert(data.message);
                window.location.href = '/login';
                return;
            }
            alert('Erreur : ' + data.message);
            loadBookedSlots();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur de connexion est survenue.');
    })
    .finally(() => {
        isSubmittingReservation = false;
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirmer et Réserver';
        }
    });
}

function loadSavedState() {
    const saved = localStorage.getItem('padelReservation');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            reservationState = {
                ...reservationState,
                playerCount: parsed.playerCount || reservationState.playerCount,
                requirements: parsed.requirements || reservationState.requirements,
            };
            reservationState.currentStep = 1; // On force le retour à 1 à l'initialisation
            setupReservationDetails();
        } catch (e) {
            console.error('Failed to load saved state', e);
        }
    }
}

function saveState() {
    localStorage.setItem('padelReservation', JSON.stringify(reservationState));
}

setInterval(saveState, 5000);
