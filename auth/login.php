<?php
session_start();

$baseUrl = '../';

// default admin account
$admin = [
    'name' => 'Admin',
    'email' => 'admin@tagpo.com',
    'password' => 'admin123',
    'role' => 'admin'
];

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $found = false;

    // Check against Admin first
    if ($email == $admin['email'] && $password == $admin['password']) {
        $_SESSION['current_user'] = $admin;
        setcookie('user_session', $admin['email'], time() + 30, '/');
        header("Location: ../index.php");
        exit();
    }

    // Check against session users
    if (isset($_SESSION['users'])) {
        foreach ($_SESSION['users'] as $user) {
            if ($user['email'] == $email && $user['password'] == $password) {
                $found = true;
                $_SESSION['current_user'] = $user;
                break;
            }
        }
    }

    if ($found) {
        setcookie('user_session', $_SESSION['current_user']['email'], time() + 30, '/');
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TAGPO</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your Custom Style -->
    <link rel="stylesheet" href="../assets/styles.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: var(--radius-lg);
            background: #ffffff;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        .login-header {
            background: var(--deep);
            padding: 40px;
            text-align: center;
            color: #fff;
        }
        .login-body {
            padding: 40px;
        }
        .form-control {
            background: #f9fafb;
            border: 1px solid var(--border);
            padding: 12px 15px;
        }
        .form-control:focus {
            background: #fff;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.15);
        }
        .brand-font {
            font-family: 'Playfair Display', serif;
        }
        .alert-custom {
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            border: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            
            <!-- Logo / Brand Heading -->
            <div class="text-center mb-4 fade-up">
                <h1 class="brand-font fw-bold" style="font-size: 2.5rem;">TAGPO<span class="text-gold">.</span></h1>
            </div>

            <div class="login-card fade-up-1">
                <div class="login-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1">Welcome Back</h3>
                        <p class="text-muted small">Sign in to manage your bookings</p>
                    </div>

                    <!-- Status Messages -->
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'registered'): ?>
                        <div class="alert alert-success alert-custom mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i> Account created! Please login.
                        </div>
                    <?php endif; ?>

                    <?php if(isset($_GET['status']) && $_GET['status'] == 'logged_out'): ?>
                        <div class="alert alert-info alert-custom mb-4">
                            <i class="fa-solid fa-circle-info me-2"></i> Session expired. Please sign in again.
                        </div>
                    <?php endif; ?>

                    <?php if($error): ?>
                        <div class="alert alert-danger alert-custom mb-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" placeholder="name@example.com" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Password</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-book w-100 py-3 mb-3">
                            Sign In
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <p class="small text-muted">
                            New to TAGPO? 
                            <a href="signup.php" class="text-accent fw-bold text-decoration-none">Create an account</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <p class="text-center mt-4 small text-muted fade-up-2">
                &copy; 2026 TAGPO Luxury Venues. All rights reserved.
            </p>
        </div>
    </div>
</div>

</body>
</html>