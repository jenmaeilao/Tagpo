<?php

$shortcuts = [
    ['key' => 'Ctrl + K', 'action' => 'Search Venues', 'description' => 'Open quick search modal'],
    ['key' => 'Ctrl + B', 'action' => 'New Booking', 'description' => 'Start a new venue booking'],
    ['key' => 'Ctrl + L', 'action' => 'Logout', 'description' => 'Quick logout option'],
    ['key' => 'Ctrl + Home', 'action' => 'Go Home', 'description' => 'Return to homepage'],
    ['key' => '?', 'action' => 'Help', 'description' => 'Show help & keyboard shortcuts'],
    ['key' => 'Esc', 'action' => 'Close Modal', 'description' => 'Close any open dialog'],
];

$quick_tips = [
    'Always check venue capacity before booking your event',
    'Confirm your booking at least 7 days in advance',
    'Free cancellation available up to 14 days before event',
    'Compare prices using the advanced filter on search page',
    'Save your favorite venues to wishlist for quick access',
    'Check reviews from verified customers before deciding',
    'Payment can be done online or via bank transfer',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shortcuts & Quick Reference — TAGPO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .shortcuts-container { max-width: 900px; margin: 60px auto; padding: 40px 24px; }
        .shortcut-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 16px; margin-bottom: 12px; display: grid; grid-template-columns: 120px 1fr; gap: 20px; align-items: center; }
        .shortcut-key { background: #f3f4f6; border-radius: 8px; padding: 12px; font-family: 'Courier New', monospace; font-weight: 600; color: var(--deep); text-align: center; font-size: 0.85rem; border: 1px solid var(--border); }
        .shortcut-content h5 { font-size: 0.95rem; font-weight: 600; color: var(--deep); margin-bottom: 4px; }
        .shortcut-content p { font-size: 0.85rem; color: var(--muted); margin: 0; }
        .tips-section { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 24px; margin-top: 40px; }
        .tips-section h3 { font-family: 'Playfair Display', serif; color: var(--deep); margin-bottom: 16px; }
        .tip-item { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .tip-icon { font-size: 1.2rem; flex-shrink: 0; }
        .tip-text { font-size: 0.9rem; color: #374151; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="shortcuts-container">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 8px;">Keyboard Shortcuts</h1>
        <p style="color: var(--muted); margin-bottom: 32px;">Quick keyboard shortcuts to navigate and use TAGPO faster</p>
        
        <div class="shortcuts-list">
            <?php foreach ($shortcuts as $shortcut): ?>
                <div class="shortcut-card">
                    <div class="shortcut-key"><?php echo htmlspecialchars($shortcut['key']); ?></div>
                    <div class="shortcut-content">
                        <h5><?php echo htmlspecialchars($shortcut['action']); ?></h5>
                        <p><?php echo htmlspecialchars($shortcut['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="tips-section">
            <h3>Quick Tips for Better Bookings</h3>
            <?php foreach ($quick_tips as $tip): ?>
                <div class="tip-item">
                    <span class="tip-icon">💡</span>
                    <p class="tip-text"><?php echo htmlspecialchars($tip); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Keyboard shortcut handlers
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                // Open search
                document.location.href = '/php_works/OOP_Midterm/search.php';
            }
            if (e.ctrlKey && e.key === 'b') {
                e.preventDefault();
                document.location.href = '/php_works/OOP_Midterm/booking.php';
            }
            if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                document.location.href = '/php_works/OOP_Midterm/logout.php';
            }
            if (e.key === '?') {
                e.preventDefault();
                alert('Press Ctrl+K to search, Ctrl+B to book, Ctrl+L to logout');
            }
        });
    </script>
</body>
</html>
