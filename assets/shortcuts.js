/**
 * GLOBAL KEYBOARD SHORTCUTS
 * Works on all pages: index.php, venue.php, booking.php, etc.
 */

const shortcuts = [
    { key: 'Ctrl + K', action: 'Search Venues', description: 'Open quick search' },
    { key: 'Ctrl + B', action: 'New Booking', description: 'Start a new booking' },
    { key: 'Ctrl + L', action: 'Logout', description: 'Quick logout' },
    { key: 'Ctrl + Home', action: 'Go Home', description: 'Return to homepage' },
    { key: '?', action: 'Help', description: 'Show keyboard shortcuts' },
    { key: 'Esc', action: 'Close Modal', description: 'Close any open dialog' },
];

document.addEventListener('keydown', function(event) {
    // Ctrl + K: Search Venues
    if (event.ctrlKey && event.key === 'k') {
        event.preventDefault();
        openSearchModal();
    }
    
    // Ctrl + B: New Booking
    if (event.ctrlKey && event.key === 'b') {
        event.preventDefault();
        window.location.href = 'booking.php';
    }
    
    // Ctrl + L: Logout
    if (event.ctrlKey && event.key === 'l') {
        event.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }
    
    // Ctrl + Home: Go Home
    if (event.ctrlKey && event.key === 'Home') {
        event.preventDefault();
        window.location.href = 'index.php';
    }
    
    // ?: Show Help
    if (event.key === '?') {
        event.preventDefault();
        showShortcutsHelp();
    }
    
    // Esc: Close Modal
    if (event.key === 'Escape') {
        closeAllModals();
    }
});

function openSearchModal() {
    // Check if search modal exists, otherwise scroll to search bar
    const searchCard = document.querySelector('.search-card');
    if (searchCard) {
        searchCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const searchInput = searchCard.querySelector('.form-control');
        if (searchInput) {
            searchInput.focus();
        }
    } else {
        // If no search card on this page, redirect to index (has search)
        window.location.href = 'index.php#search';
    }
}

function showShortcutsHelp() {
    let shortcutText = '⌨️ KEYBOARD SHORTCUTS\n\n';
    shortcuts.forEach(s => {
        shortcutText += `${s.key.padEnd(15)} → ${s.action}\n`;
    });
    shortcutText += '\n💡 Tip: Visit /shortcuts.php for full details';
    
    // Create a modal instead of alert
    const modal = document.createElement('div');
    modal.className = 'shortcuts-modal';
    modal.innerHTML = `
        <div class="shortcuts-modal-content">
            <button class="shortcuts-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            <h2>⌨️ Keyboard Shortcuts</h2>
            <ul>
                ${shortcuts.map(s => `<li><strong>${s.key}</strong> — ${s.action}<br><small>${s.description}</small></li>`).join('')}
            </ul>
            <p><small>📖 <a href="shortcuts.php">View full shortcuts & tips →</a></small></p>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeAllModals() {
    // Close lightbox if open
    const lightbox = document.getElementById('lightbox');
    if (lightbox && lightbox.style.display === 'flex') {
        lightbox.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close shortcuts modal if open
    const shortcutsModal = document.querySelector('.shortcuts-modal');
    if (shortcutsModal) {
        shortcutsModal.remove();
    }
    
    // Close Bootstrap modals
    const bsModals = document.querySelectorAll('.modal.show');
    bsModals.forEach(m => {
        const modal = bootstrap.Modal.getInstance(m);
        if (modal) modal.hide();
    });
}

// Add CSS for shortcuts modal
const style = document.createElement('style');
style.textContent = `
    .shortcuts-modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.2s ease;
    }
    
    .shortcuts-modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        max-width: 500px;
        position: relative;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: slideUp 0.3s ease;
    }
    
    .shortcuts-modal-content h2 {
        font-family: 'Playfair Display', serif;
        color: #0f1520;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }
    
    .shortcuts-modal-content ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .shortcuts-modal-content li {
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.9rem;
    }
    
    .shortcuts-modal-content li:last-child {
        border-bottom: none;
    }
    
    .shortcuts-modal-content strong {
        color: #1a56db;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
    }
    
    .shortcuts-modal-content small {
        color: #6b7280;
        display: block;
        margin-top: 4px;
    }
    
    .shortcuts-modal-content p {
        margin-top: 16px;
        text-align: center;
    }
    
    .shortcuts-modal-content a {
        color: #1a56db;
        text-decoration: none;
    }
    
    .shortcuts-modal-content a:hover {
        text-decoration: underline;
    }
    
    .shortcuts-close {
        position: absolute;
        top: 12px;
        right: 12px;
        background: none;
        border: none;
        font-size: 28px;
        color: #6b7280;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .shortcuts-close:hover {
        color: #1f2937;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
