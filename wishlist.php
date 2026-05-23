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

// Hardcoded venues (Dapat match ito sa index/venue.php mo)
$hardcoded_venues = [
    ['id' => 1, 'name' => 'Paradiso Terrestre', 'price' => 35000, 'location' => 'Molino, Cavite City', 'tag' => 'Wedding · Debut', 'image' => 'paradiso1.jpg', 'rating' => 4.8, 'reviews' => 36, 'cap' => 500],
    ['id' => 2, 'name' => 'Blue Gardens', 'price' => 60000, 'location' => 'Makati City, Metro Manila', 'tag' => 'Prom · Gala', 'image' => 'gardens1.jpg', 'rating' => 4.9, 'reviews' => 52, 'cap' => 250],
    ['id' => 3, 'name' => 'The Green Lounge Events Place', 'price' => 45000, 'location' => 'Quezon City, Metro Manila', 'tag' => 'Birthday · Corporate', 'image' => 'lounge1.jpg', 'rating' => 4.7, 'reviews' => 28, 'cap' => 300],
];

// Isama rin natin yung session venues para kung ni-wishlist nila yung in-add ni Admin, lilitaw pa rin
$session_venues = $_SESSION['venues'] ?? [];
$all_venues = array_merge($hardcoded_venues, $session_venues);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist | Tagpo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
        
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="wishlist-hero">
    <div class="container">
        <div class="d-flex align-items-center mb-3">
            <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Home
            </a>
        </div>
        <h1 class="display-5 fw-bold">Saved for Later </h1>
        <p class="lead">Your curated list of dream event spaces.</p>
    </div>
</section>

<div class="container mb-5">
    <div id="wishlist-container" class="row g-4">
        </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const venues = <?php echo json_encode($all_venues); ?>;
const wishlist = JSON.parse(localStorage.getItem('tagpo_wishlist')) || [];
const container = document.getElementById('wishlist-container');

function renderWishlist() {
    if (wishlist.length === 0) {
        container.innerHTML = `
            <div class="col-12 empty-state">
                <i class="bi bi-heartbreak" style="font-size: 4rem; color: #dee2e6;"></i>
                <h3 class="mt-3">Your wishlist is empty</h3>
                <p class="text-muted">Start exploring and save your favorite venues!</p>
                <a href="search.php" class="btn btn-primary px-4 py-2 mt-2 fw-bold">Browse Venues</a>
            </div>
        `;
        return;
    }

    let html = '';
    wishlist.forEach(id => {
        const v = venues.find(v => v.id == id);
        if (v) {
            html += `
    <div class="col-md-4" id="card-${v.id}">
      <div class="card venue-card h-100">

        <div class="venue-img-wrapper position-relative overflow-hidden">
          <img src="assets/images/${v.image}" alt="${v.name}" class="img-fluid w-100" />
          <span class="venue-badge">${v.tag}</span>
          <button onclick="removeFromWishlist(${v.id})" class="btn btn-light btn-sm position-absolute" style="top:10px; right:10px; border-radius: 50%; z-index: 5;">
            <i class="bi bi-trash text-danger"></i>
          </button>
        </div>

        <div class="card-body d-flex flex-column">
          <h5 class="mb-1">${v.name}</h5>

          <div class="venue-meta mb-2">
            <span><i class="bi bi-geo-alt me-1"></i>${v.location}</span>
            <span>⭐ ${v.rating} (${v.reviews})</span>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-auto pt-3" style="border-top: 1px solid var(--border);">
            <div>
              <div class="price-tag">
                ₱${Number(v.price).toLocaleString()}
                <small>/ package</small>
              </div>
              <small class="text-muted" style="font-size:.75rem;">
                <i class="bi bi-people me-1"></i>Up to ${Number(v.cap).toLocaleString()} guests
              </small>
            </div>
            <a href="venue.php?id=${v.id}" class="btn-view btn">
              View <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
        </div>

      </div>
    </div>
            `;
        }
    });
    container.innerHTML = html;
}

function removeFromWishlist(id) {
    if(confirm('Remove this venue from your wishlist?')) {
        const index = wishlist.indexOf(id);
        if (index > -1) {
            wishlist.splice(index, 1);
            localStorage.setItem('tagpo_wishlist', JSON.stringify(wishlist));
            renderWishlist(); // Refresh the UI
        }
    }
}

// Initial Render
renderWishlist();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>