/**
 * Online Academic System - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Print functionality
    const printButtons = document.querySelectorAll('.btn-print');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            window.print();
        });
    });

    // Dynamic search/filter for tables
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableId = this.dataset.table;
            const table = document.getElementById(tableId);
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });
    });

    // Marks calculation
    const marksInputs = document.querySelectorAll('.marks-input');
    marksInputs.forEach(input => {
        input.addEventListener('input', function() {
            const maxMarks = parseInt(this.dataset.max) || 100;
            const obtained = parseInt(this.value) || 0;
            
            if (obtained > maxMarks) {
                this.value = maxMarks;
            }
            
            if (obtained < 0) {
                this.value = 0;
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

/**
 * Toggle password visibility
 * @param {string} inputId - The ID of the password input
 * @param {HTMLElement} icon - The icon element to toggle
 */
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

/**
 * Confirm action with custom message
 * @param {string} message - The confirmation message
 * @returns {boolean}
 */
function confirmAction(message) {
    return confirm(message);
}

/**
 * Format number with leading zeros
 * @param {number} num - The number to format
 * @param {number} size - The desired length
 * @returns {string}
 */
function padNumber(num, size) {
    let s = num + '';
    while (s.length < size) s = '0' + s;
    return s;
}

/**
 * Calculate percentage
 * @param {number} obtained - Marks obtained
 * @param {number} total - Total marks
 * @returns {number}
 */
function calculatePercentage(obtained, total) {
    if (total === 0) return 0;
    return Math.round((obtained / total) * 100);
}

/**
 * Export table to CSV
 * @param {string} tableId - The ID of the table to export
 * @param {string} filename - The filename for the CSV
 */
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        let rowData = [];
        const cells = row.querySelectorAll('td, th');
        cells.forEach(cell => {
            // Remove any HTML tags and get text content
            let text = cell.textContent.replace(/(\r\n|\n|\r)/gm, '').trim();
            // Escape quotes and wrap in quotes if contains comma
            if (text.includes(',')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            rowData.push(text);
        });
        csv.push(rowData.join(','));
    });

    downloadCSV(csv.join('\n'), filename);
}

/**
 * Download CSV content
 * @param {string} csv - The CSV content
 * @param {string} filename - The filename
 */
function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
