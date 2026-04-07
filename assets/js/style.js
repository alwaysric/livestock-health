// Auto dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Alerts auto-dismiss
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Tooltips initialization
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popovers initialization
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Confirm delete actions
function confirmDelete(event, message = 'Are you sure you want to delete this item? This action cannot be undone.') {
    if (!confirm(message)) {
        event.preventDefault();
        return false;
    }
    return true;
}

// Live search for tables
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});

// Show loading spinner
function showLoading() {
    const spinner = document.querySelector('.spinner-wrapper');
    if (spinner) {
        spinner.classList.add('show');
    }
}

// Hide loading spinner
function hideLoading() {
    const spinner = document.querySelector('.spinner-wrapper');
    if (spinner) {
        spinner.classList.remove('show');
    }
}

// Format date
function formatDate(date) {
    const d = new Date(date);
    return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard!');
    }, function() {
        alert('Failed to copy!');
    });
}

// Print page
function printPage() {
    window.print();
}

// Toggle password visibility
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelectorAll('.toggle-password');
    togglePassword.forEach(function(button) {
        button.addEventListener('click', function() {
            const passwordInput = document.querySelector(this.dataset.target);
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    });
});