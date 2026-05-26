<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Check if user session expired (no cookie but had session)
if (!isset($_COOKIE['user_session']) && isset($_SESSION['current_user'])) {
  session_destroy();
  $_SESSION = [];
}

// Clear cart if session expired
if (!isset($_COOKIE['user_session']) && isset($_SESSION['cart'])) {
  unset($_SESSION['cart']);
}

// Determine base URL for links (set $baseUrl = '../' in subdirectory pages)
$baseUrl = isset($baseUrl) ? $baseUrl : '';

$current = basename($_SERVER['PHP_SELF']);

$isAdmin = isset($_SESSION['current_user']['role'])
  && $_SESSION['current_user']['role'] === 'admin';

// Get cart count
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">

    <!-- BRAND -->
    <a class="navbar-brand fw-bold" href="<?php echo $baseUrl; ?>index.php">
      Tagpo<span class="text-primary">.</span>
    </a>

    <!-- TOGGLER -->
    <button class="navbar-toggler border-0 shadow-none" type="button"
      data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- NAV LINKS -->
    <div class="collapse navbar-collapse" id="mainNav">

      <ul class="navbar-nav mx-auto">

        <li class="nav-item">
          <a class="nav-link <?php echo $current === 'index.php' ? 'fw-semibold text-dark' : ''; ?>"
            href="<?php echo $baseUrl; ?>index.php">Venues</a>
        </li>

        <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>index.php#venues">Explore Venues</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>index.php#features">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>index.php#about">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>wishlist.php"> Wishlist </a></li>
        <li class="nav-item">
          <a class="nav-link position-relative" href="<?php echo $baseUrl; ?>cart.php">
            Cart
            <?php if ($cartCount > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $cartCount; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <!-- ADMIN NAV (clean placement inside menu) -->
        <?php if ($isAdmin): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-danger fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
              Admin
            </a>

            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>admin/admin.php?view=dashboard">Dashboard</a></li>
              <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>admin/add_venue.php">Add Venue</a></li>
              <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>admin/admin.php?view=bookings">View Bookings</a></li>
            </ul>
          </li>
        <?php endif; ?>

      </ul>

      <!-- RIGHT SIDE -->
      <div class="d-flex align-items-center gap-2">

        <?php if (isset($_SESSION['current_user'])): ?>

          <span class="fw-semibold d-none d-md-inline">
            Hi, <?php echo $_SESSION['current_user']['name']; ?> 👋
          </span>


          <a href="<?php echo $baseUrl; ?>auth/logout.php" class="btn btn-danger btn-sm">
            Logout
          </a>

        <?php else: ?>

          <a href="<?php echo $baseUrl; ?>auth/login.php" class="text-decoration-none fw-semibold text-secondary">
            Log In
          </a>

          <a href="<?php echo $baseUrl; ?>auth/signup.php" class="btn btn-book shadow-sm">
            Sign Up
          </a>

        <?php endif; ?>

      </div>

    </div>
  </div>
</nav>

<script src="<?php echo $baseUrl; ?>assets/shortcuts.js"></script>