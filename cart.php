<?php
session_start();

// Check if user session expired (no cookie but had session)
if (!isset($_COOKIE['user_session']) && isset($_SESSION['current_user'])) {
    session_destroy();
    $_SESSION = [];
    header("Location: index.php?expired=true");
    exit();
}

// Clear cart if session expired
if (!isset($_COOKIE['user_session']) && isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Booking Cart | Tagpo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css"> <!-- Your existing CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Specific Cart UI Tweaks to match Tagpo Style */
        .cart-item-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: #fff;
            transition: var(--transition);
            overflow: hidden;
        }
        .cart-item-card:hover {
            box-shadow: var(--shadow);
        }
        .item-image-sm {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--deep-mid), var(--deep));
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-light);
        }
        .addon-badge {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--muted);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .summary-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            position: sticky;
            top: 100px;
        }
    </style>
</head>

<body>

<?php include 'includes/header.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="container">
        <a href="index.php">Home</a> / <span class="text-muted">Booking Cart</span>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Title Section -->
        <div class="col-12 mb-4">
            <span class="section-eyebrow">Reservations</span>
            <h2 class="section-heading">🛒 Your Booking Cart</h2>
            <p class="section-sub">Review your selected venues and event details before confirming.</p>
        </div>

        <?php if (empty($cart)): ?>
            <div class="col-12 text-center py-5">
                <div class="mb-4" style="font-size: 4rem; opacity: 0.3;">📂</div>
                <h3 class="h4">Your cart is currently empty.</h3>
                <p class="text-muted mb-4">It looks like you haven't picked a venue yet.</p>
                <a href="search.php" class="btn-book">Browse Venues</a>
            </div>
        <?php else: ?>
            
            <!-- Items List -->
            <div class="col-lg-8">
                <?php foreach ($cart as $index => $item): ?>
                    <div class="cart-item-card p-3 mb-3 fade-up">
                        <div class="d-md-flex gap-4">
                            <!-- Mini Image Placeholder -->
                            <div class="item-image-sm flex-shrink-0 mb-3 mb-md-0">
                                <i class="fa-solid fa-hotel fa-2x"></i>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($item['venue_name']); ?></h5>
                                    <a href="remove_from_cart.php?id=<?php echo $index; ?>" class="text-danger text-decoration-none small">
                                        <i class="fa-solid fa-trash-can me-1"></i> Remove
                                    </a>
                                </div>
                                
                                <p class="mb-2 text-muted small">
                                    <i class="fa-solid fa-calendar-day me-1"></i> <?php echo htmlspecialchars($item['event_date']); ?> 
                                    <span class="mx-2">|</span>
                                    <i class="fa-solid fa-star me-1"></i> <?php echo htmlspecialchars($item['event_type']); ?>
                                </p>

                                <div class="mb-3">
                                    <span class="d-block small fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.65rem; color: var(--gold);">Add-ons Included:</span>
                                    <?php
                                    $addonsTotal = 0;
                                    if (!empty($item['addons'])):
                                        foreach ($item['addons'] as $addon):
                                            echo '<span class="addon-badge">' . htmlspecialchars($addon) . '</span>';
                                            if ($addon == "Catering") $addonsTotal += 5000;
                                            if ($addon == "Photo Booth") $addonsTotal += 3000;
                                            if ($addon == "DJ Booth") $addonsTotal += 4000;
                                        endforeach;
                                    else:
                                        echo '<span class="text-muted small">Standard Package (No Add-ons)</span>';
                                    endif;
                                    ?>
                                </div>

                                <?php
                                    $venuePrice = $item['venue_price'];
                                    $itemTotal = $venuePrice + $addonsTotal;
                                    $total += $itemTotal;
                                ?>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <span class="text-muted small">Venue + Extras</span>
                                    <span class="fw-bold text-deep">₱<?php echo number_format($itemTotal); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="summary-card shadow-sm">
                    <h5 class="mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>₱<?php echo number_format($total); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Service Fee</span>
                        <span class="text-success">FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h6 mb-0">Total Amount</span>
                        <span class="h4 mb-0 text-deep">₱<?php echo number_format($total); ?></span>
                    </div>

                    <a href="checkout.php" class="btn-book w-100 text-center py-3">
                        Proceed to Checkout <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                    
                    <div class="mt-4 p-3 bg-surface rounded" style="border: 1px dashed var(--border);">
                        <p class="small text-muted mb-0">
                            <i class="fa-solid fa-shield-halved text-gold me-2"></i> 
                            Secure booking guaranteed by <strong>Tagpo</strong>.
                        </p>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>