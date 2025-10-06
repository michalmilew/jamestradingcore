// Global variables
let currentUserId = null;

// Alert function
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');
    const alertDiv = document.createElement('div');
    
    alertDiv.className = `flex items-center p-4 mb-4 rounded-lg transition-all duration-500 transform translate-x-full ${
        type === 'success' 
            ? 'text-green-400 bg-dark-2 border border-green-500'
            : 'text-red-400 bg-dark-2 border border-red-500'
    }`;
    
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="${type === 'success' 
                    ? 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z'
                    : 'M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z'
                }"/>
            </svg>
            <span class="text-sm font-medium">${message}</span>
        </div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-600 p-1.5 inline-flex h-8 w-8 text-gray-400 hover:text-gray-300" onclick="this.parentElement.remove()">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
        </button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    setTimeout(() => alertDiv.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        alertDiv.classList.add('translate-x-full');
        setTimeout(() => alertDiv.remove(), 500);
    }, 5000);
}

// Modal functions
function openPaymentModal(userId) {
    currentUserId = userId;
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('paymentForm').reset();
}

function openReferralAmountModal(userId) {
    currentUserId = userId;
    document.getElementById('referralAmountModal').classList.remove('hidden');
}

function closeReferralAmountModal() {
    document.getElementById('referralAmountModal').classList.add('hidden');
    document.getElementById('referralAmountForm').reset();
}

// Default price update function
async function updateDefaultPrice() {
    try {
        const response = await fetch('/en/admin/affiliates/default-price', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                default_price: document.getElementById('defaultReferralPrice').value
            })
        });

        const data = await response.json();
        if (data.success) {
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('An error occurred while updating the default price', 'error');
    }
}

// Form submit handlers
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('paymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        try {
            const response = await fetch(`/en/admin/affiliates/${currentUserId}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    amount_paid: this.amount_paid.value
                })
            });

            const data = await response.json();
            if (data.success) {
                closePaymentModal();
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred', 'error');
        }
    });

    document.getElementById('referralAmountForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        try {
            const response = await fetch(`/en/admin/affiliates/${currentUserId}/referral-amount`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    amount: this.amount.value
                })
            });

            const data = await response.json();
            if (data.success) {
                closeReferralAmountModal();
                showAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('An error occurred', 'error');
        }
    });
});

// ... rest of your JavaScript functions ... 