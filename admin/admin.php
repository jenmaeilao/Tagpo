<?php
require_once dirname(__DIR__) . '/config/session_config.php';

$baseUrl = '../';

// Update activity
$_SESSION['last_activity'] = time();

// Refresh cookie if logged in
if (isLoggedIn()) {
    $currentUser = getCurrentUser();
    setcookie('user_session', $currentUser['email'], time() + (60 * 60 * 24 * 7), '/');
}

/* =========================
   SECURITY CHECK (ADMIN ONLY)
========================= */
if (!isAdmin()) {
    die("Access denied. Admin only.");
}

/* =========================
   SAMPLE DATA (BOOKINGS)
   (from session)
========================= */
$bookings = $_SESSION['bookings'] ?? [];

/* =========================
   OOP CLASS (BOOKING MODEL)
========================= */
class Booking {
    private $venue_id;
    private $event_type;
    private $date;
    private $guests;

    public function __construct($venue_id, $event_type, $date, $guests) {
        $this->venue_id = $venue_id;
        $this->event_type = $event_type;
        $this->date = $date;
        $this->guests = $guests;
    }

    public function getVenueId() { return $this->venue_id; }
    public function getEventType() { return $this->event_type; }
    public function getDate() { return $this->date; }
    public function getGuests() { return $this->guests; }
}

/* =========================
   DATA TYPE TEST (basic validation)
========================= */
function isValidBooking($b) {
    return is_array($b)
        && isset($b['venue_id'], $b['event_type'], $b['date']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/styles.css">
</head>

<body>

<!-- NAVBAR SIMPLE ADMIN VERSION -->
<nav class="navbar navbar-light bg-white border-bottom px-4">
    <a class="navbar-brand fw-bold" href="../index.php">Tagpo<span class="text-primary">.</span></a>

    <div class="d-flex gap-3">
        <a href="../index.php" class="btn btn-outline-secondary btn-sm">Home</a>
        <a href="../auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container py-4">

<!-- HEADER -->
<h2 class="mb-4">Admin Dashboard</h2>

<!-- =========================
     NUMERIC ARRAY (STATS)
========================= -->
<?php
$stats = [
    count($bookings),
    3, // venues (sample)
    12 // users (sample)
];
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h6>Total Bookings</h6>
            <h3><?= $stats[0] ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h6>Total Venues</h6>
            <h3><?= $stats[1] ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h6>Total Users</h6>
            <h3><?= $stats[2] ?></h3>
        </div>
    </div>
</div>

<!-- =========================
     SWITCH (ADMIN MENU)
========================= -->
<?php
$view = $_GET['view'] ?? 'bookings';

switch ($view):
case 'bookings':
?>

<h4>All Bookings</h4>

<div class="card p-3">

<?php if (empty($bookings)): ?>
    <p class="text-muted">No bookings yet.</p>
<?php else: ?>

<table class="table table-striped">
<thead>
<tr>
    <th>Venue ID</th>
    <th>Event</th>
    <th>Date</th>
    <th>Guests</th>
</tr>
</thead>
<tbody>

<!-- =========================
     WHILE LOOP (DISPLAY)
========================= -->
<?php
$i = 0;
while ($i < count($bookings)):
    $b = $bookings[$i];
    if (isValidBooking($b)):
?>
<tr>
    <td><?= $b['venue_id'] ?></td>
    <td><?= $b['event_type'] ?></td>
    <td><?= $b['date'] ?></td>
    <td><?= $b['guests'] ?></td>
</tr>
<?php
    endif;
    $i++;
endwhile;
?>

</tbody>
</table>

<?php endif; ?>

</div>

<?php
break;

case 'users':
?>

<h4>Users</h4>
<p class="text-muted">User management coming soon.</p>

<?php
break;

case 'venues':
?>

<h4>Venues</h4>
<p class="text-muted">Venue management coming soon.</p>

<?php
break;

endswitch;
?>

<!-- =========================
     DO-WHILE LOOP (DEMO STATS)
========================= -->
<?php
$count = 1;
echo "<hr><h6>Quick System Check</h6>";
do {
    echo "<small>System check #" . $count . " OK</small><br>";
    $count++;
} while ($count <= 3);
?>

<!-- =========================
     OOP EXTENSION EXAMPLE
========================= -->
<?php
class AdminUser extends Booking {
    public function role() {
        return "admin access granted";
    }
}
?>

</div>

</body>
</html>