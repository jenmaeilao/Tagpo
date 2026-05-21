<?php
session_start();

// ── Handle Signup POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $signup_error = '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $signup_error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $signup_error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $signup_error = 'Passwords do not match.';
    } else {
        // Check if email already taken (check existing users + admin)
        $adminEmail = 'admin@tagpo.com';
        $alreadyExists = ($email === $adminEmail);

        if (!$alreadyExists && isset($_SESSION['users'])) {
            foreach ($_SESSION['users'] as $u) {
                if ($u['email'] === $email) {
                    $alreadyExists = true;
                    break;
                }
            }
        }

        if ($alreadyExists) {
            $signup_error = 'An account with that email already exists.';
        } else {
            // Save to session users
            $_SESSION['users'][] = [
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                'role'     => 'user',
            ];

            // Auto-login the new user
            $_SESSION['current_user'] = [
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                'role'     => 'user',
            ];
            setcookie('user_session', $email, time() + 30, '/');
            header('Location: index.php');
            exit();
        }
    }
    $default_tab = 'signup'; // Stay on signup tab on error
}

// ── Handle Login POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $login_error = '';
    $found = false;

    // Admin check
    if ($email === 'admin@tagpo.com' && $password === 'admin123') {
        $_SESSION['current_user'] = [
            'name'     => 'Admin',
            'email'    => 'admin@tagpo.com',
            'password' => 'admin123',
            'role'     => 'admin',
        ];
        setcookie('user_session', $email, time() + 30, '/');
        header('Location: index.php');
        exit();
    }

    // Session users check
    if (isset($_SESSION['users'])) {
        foreach ($_SESSION['users'] as $user) {
            if ($user['email'] === $email && $user['password'] === $password) {
                $_SESSION['current_user'] = $user;
                setcookie('user_session', $email, time() + 30, '/');
                header('Location: index.php');
                exit();
            }
        }
    }

    $login_error = 'Invalid email or password.';
    $default_tab = 'login'; // Stay on login tab on error
}

// ── Default tab (from GET param or fallback) ────────────────────────
$default_tab = $default_tab ?? ($_GET['mode'] ?? 'login');

// ── Redirect if already logged in ──────────────────────────────────
if (isset($_SESSION['current_user']) && isset($_COOKIE['user_session'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to TAGPO</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="styles.css"/>
  <style>
    /* ── Page-level overrides (auth only) ─────────────────── */
    body {
      background: var(--deep);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
    }

    /* ── Auth wrapper ──────────────────────────────────────── */
    .auth-wrap {
      width: 100%;
      max-width: 980px;
      min-height: 620px;
      border-radius: 20px;
      overflow: hidden;
      display: flex;
      box-shadow: 0 32px 80px rgba(0,0,0,.55);
      animation: fadeUp 0.5s ease both;
    }

    /* LEFT: image panel */
    .auth-panel-left {
      flex: 1;
      background:
        linear-gradient(160deg, rgba(15,21,32,.35) 0%, rgba(15,21,32,.82) 100%),
        url('https://i.pinimg.com/webp70/1200x/07/19/b1/0719b1417022d9dc9aa680d4d623d00f.webp')
        center/cover no-repeat;
      padding: 52px 48px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      color: #fff;
    }

    .auth-panel-left .brand {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: -0.5px;
    }

    .auth-panel-left .brand span { color: var(--gold-light); }

    .auth-tagline h2 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.6rem, 3vw, 2.4rem);
      font-weight: 600;
      line-height: 1.25;
      margin-bottom: 14px;
      color: #fff;
    }

    .auth-tagline p {
      font-size: 0.88rem;
      color: rgba(255,255,255,.65);
      line-height: 1.7;
      max-width: 340px;
    }

    .auth-dots { display: flex; gap: 6px; margin-top: 12px; }
    .auth-dots span {
      height: 4px;
      border-radius: 2px;
      background: rgba(255,255,255,.3);
    }
    .auth-dots span:first-child {
      width: 28px;
      background: var(--gold-light);
    }
    .auth-dots span:not(:first-child) { width: 14px; }

    /* RIGHT: form panel */
    .auth-panel-right {
      width: 430px;
      min-width: 0;
      background: var(--deep);
      padding: 52px 48px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      overflow-y: auto;
    }

    .auth-panel-right .tab-switcher {
      display: flex;
      gap: 4px;
      background: rgba(255,255,255,.06);
      border-radius: 10px;
      padding: 4px;
      margin-bottom: 32px;
    }

    .auth-panel-right .tab-btn {
      flex: 1;
      padding: 9px 0;
      border: none;
      background: transparent;
      border-radius: 7px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.85rem;
      font-weight: 600;
      color: rgba(255,255,255,.4);
      cursor: pointer;
      transition: all 0.22s ease;
    }

    .auth-panel-right .tab-btn.active {
      background: var(--accent);
      color: #fff;
      box-shadow: 0 3px 10px rgba(26,86,219,.4);
    }

    /* Form labels */
    .auth-label {
      font-size: 0.72rem;
      font-weight: 600;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: rgba(255,255,255,.45);
      margin-bottom: 6px;
      display: block;
    }

    /* Inputs */
    .auth-panel-right .form-control {
      background: rgba(255,255,255,.06);
      border: 1.5px solid rgba(255,255,255,.1);
      color: #fff;
      border-radius: var(--radius);
      padding: 12px 16px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      transition: border-color var(--transition), background var(--transition);
    }

    .auth-panel-right .form-control::placeholder { color: rgba(255,255,255,.3); }

    .auth-panel-right .form-control:focus {
      background: rgba(255,255,255,.1);
      border-color: var(--accent);
      color: #fff;
      box-shadow: 0 0 0 3px rgba(26,86,219,.2);
    }

    .auth-panel-right .input-group-text {
      background: rgba(255,255,255,.06);
      border: 1.5px solid rgba(255,255,255,.1);
      border-right: none;
      color: rgba(255,255,255,.4);
      border-radius: var(--radius) 0 0 var(--radius);
    }

    .auth-panel-right .input-group .form-control {
      border-left: none;
      border-radius: 0 var(--radius) var(--radius) 0 !important;
    }

    .auth-panel-right .input-group:focus-within .input-group-text {
      border-color: var(--accent);
      color: rgba(255,255,255,.7);
    }

    /* Submit button */
    .btn-auth-submit {
      background: var(--accent);
      color: #fff;
      border: none;
      width: 100%;
      padding: 13px;
      border-radius: var(--radius);
      font-family: 'DM Sans', sans-serif;
      font-weight: 600;
      font-size: 0.92rem;
      transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
      margin-top: 6px;
    }

    .btn-auth-submit:hover {
      background: var(--accent-dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(26,86,219,.4);
      color: #fff;
    }

    /* Divider */
    .auth-divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 20px 0;
    }
    .auth-divider hr {
      flex: 1;
      border-color: rgba(255,255,255,.12);
      margin: 0;
    }
    .auth-divider span {
      font-size: 0.75rem;
      color: rgba(255,255,255,.35);
      white-space: nowrap;
    }

    /* Social buttons */
    .btn-social {
      background: rgba(255,255,255,.05);
      border: 1.5px solid rgba(255,255,255,.1);
      color: rgba(255,255,255,.7);
      border-radius: var(--radius);
      padding: 9px 14px;
      font-size: 0.85rem;
      font-family: 'DM Sans', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      flex: 1;
      transition: all var(--transition);
    }
    .btn-social:hover {
      background: rgba(255,255,255,.1);
      color: #fff;
      border-color: rgba(255,255,255,.25);
    }

    /* Switch link */
    .auth-switch {
      font-size: 0.82rem;
      color: rgba(255,255,255,.4);
      text-align: center;
      margin-top: 18px;
    }
    .auth-switch a {
      color: var(--gold-light);
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
    }
    .auth-switch a:hover { text-decoration: underline; }

    /* Error alerts */
    .auth-alert {
      background: rgba(239,68,68,.12);
      border: 1px solid rgba(239,68,68,.25);
      border-radius: var(--radius-sm);
      color: #fca5a5;
      font-size: 0.83rem;
      padding: 10px 14px;
      margin-bottom: 18px;
    }

    /* Form transitions */
    .auth-form { transition: opacity 0.25s ease; }
    .auth-form.d-none { display: none !important; }

    /* Heading */
    .auth-heading {
      font-family: 'Playfair Display', serif;
      font-size: 1.55rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 4px;
    }
    .auth-subheading {
      font-size: 0.83rem;
      color: rgba(255,255,255,.4);
      margin-bottom: 28px;
    }

    /* ── RESPONSIVE ─────────────────────────────────────────── */
    @media (max-width: 767px) {
      .auth-panel-left { display: none; }
      .auth-wrap { max-width: 460px; }
      .auth-panel-right { width: 100%; padding: 40px 28px; }
    }

    @media (max-width: 380px) {
      .auth-panel-right { padding: 32px 20px; }
    }
  </style>
</head>
<body>

<div class="auth-wrap">

  <!-- ── LEFT PANEL ─────────────────────────────────────────── -->
  <div class="auth-panel-left">
    <div class="brand">TAGPO<span>.</span></div>

    <div class="auth-tagline">
      <h2>Capturing Moments,<br>Creating Memories</h2>
      <p>
        From coast-side weddings to rooftop galas — find your perfect venue
        and bring your celebration to life.
      </p>
      <div class="auth-dots">
        <span></span><span></span><span></span>
      </div>
    </div>

    <div style="font-size: 0.78rem; color: rgba(255,255,255,.3);">
      © 2026 TAGPO Luxury Venues
    </div>
  </div>

  <!-- ── RIGHT PANEL ────────────────────────────────────────── -->
  <div class="auth-panel-right">

    <!-- Tab switcher -->
    <div class="tab-switcher">
      <button class="tab-btn <?php echo $default_tab === 'login'  ? 'active' : ''; ?>"
              id="tab-login"  onclick="switchTab('login')">Sign In</button>
      <button class="tab-btn <?php echo $default_tab === 'signup' ? 'active' : ''; ?>"
              id="tab-signup" onclick="switchTab('signup')">Sign Up</button>
    </div>

    <!-- ── LOGIN FORM ──────────────────────────────────── -->
    <div id="form-login" class="auth-form <?php echo $default_tab !== 'login' ? 'd-none' : ''; ?>">

      <p class="auth-heading">Welcome back</p>
      <p class="auth-subheading">Sign in to manage your bookings</p>

      <?php if (!empty($login_error)): ?>
        <div class="auth-alert">
          <i class="fa-solid fa-triangle-exclamation me-2"></i>
          <?php echo htmlspecialchars($login_error); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['status']) && $_GET['status'] === 'registered'): ?>
        <div class="auth-alert" style="background:rgba(16,185,129,.12); border-color:rgba(16,185,129,.25); color:#6ee7b7;">
          <i class="fa-solid fa-circle-check me-2"></i> Account created! Please sign in.
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="action" value="login"/>

        <div class="mb-3">
          <label class="auth-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
            <input type="email" name="email" class="form-control"
                   placeholder="name@example.com"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required/>
          </div>
        </div>

        <div class="mb-3">
          <label class="auth-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" class="form-control"
                   placeholder="••••••••" required/>
            <button type="button" class="input-group-text border-start-0"
                    style="background:rgba(255,255,255,.06); border:1.5px solid rgba(255,255,255,.1); border-left:none; cursor:pointer; color:rgba(255,255,255,.4);"
                    onclick="togglePwd('pw-login', this)">
              <i class="fa-regular fa-eye" id="pw-login-icon"></i>
            </button>
            <input type="password" name="password" id="pw-login" class="d-none"/>
          </div>
          <!-- actual password field (overrides the dummy above via name) -->
        </div>

        <button type="submit" class="btn-auth-submit">
          Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>
      </form>

      <div class="auth-divider">
        <hr/><span>Or continue with</span><hr/>
      </div>

      <div class="d-flex gap-2">
        <button class="btn-social"><i class="fab fa-google"></i> Google</button>
        <button class="btn-social"><i class="fab fa-apple"></i> Apple</button>
      </div>

      <p class="auth-switch">
        New to TAGPO? <a onclick="switchTab('signup')">Create an account</a>
      </p>
    </div>

    <!-- ── SIGNUP FORM ─────────────────────────────────── -->
    <div id="form-signup" class="auth-form <?php echo $default_tab !== 'signup' ? 'd-none' : ''; ?>">

      <p class="auth-heading">Create account</p>
      <p class="auth-subheading">Join TAGPO and start planning your event</p>

      <?php if (!empty($signup_error)): ?>
        <div class="auth-alert">
          <i class="fa-solid fa-triangle-exclamation me-2"></i>
          <?php echo htmlspecialchars($signup_error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <input type="hidden" name="action" value="signup"/>

        <div class="mb-3">
          <label class="auth-label">Full Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
            <input type="text" name="name" class="form-control"
                   placeholder="Your full name"
                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                   required/>
          </div>
        </div>

        <div class="mb-3">
          <label class="auth-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
            <input type="email" name="email" class="form-control"
                   placeholder="name@example.com"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required/>
          </div>
        </div>

        <div class="mb-3">
          <label class="auth-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" id="pw-signup" class="form-control"
                   placeholder="Min. 6 characters" required/>
            <button type="button" class="input-group-text border-start-0"
                    style="background:rgba(255,255,255,.06); border:1.5px solid rgba(255,255,255,.1); border-left:none; cursor:pointer; color:rgba(255,255,255,.4);"
                    onclick="togglePwd('pw-signup', this)">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="mb-3">
          <label class="auth-label">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="confirm_password" id="pw-confirm" class="form-control"
                   placeholder="Repeat password" required/>
            <button type="button" class="input-group-text border-start-0"
                    style="background:rgba(255,255,255,.06); border:1.5px solid rgba(255,255,255,.1); border-left:none; cursor:pointer; color:rgba(255,255,255,.4);"
                    onclick="togglePwd('pw-confirm', this)">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-auth-submit">
          Create Account <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>
      </form>

      <p class="auth-switch">
        Already have an account? <a onclick="switchTab('login')">Sign in</a>
      </p>
    </div>

  </div><!-- /auth-panel-right -->
</div><!-- /auth-wrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function switchTab(tab) {
    document.getElementById('form-login').classList.toggle('d-none', tab !== 'login');
    document.getElementById('form-signup').classList.toggle('d-none', tab !== 'signup');
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-signup').classList.toggle('active', tab === 'signup');
  }

  function togglePwd(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (field.type === 'password') {
      field.type = 'text';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
      field.type = 'password';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
  }

  // Client-side confirm-password match hint
  const pwSignup  = document.getElementById('pw-signup');
  const pwConfirm = document.getElementById('pw-confirm');
  if (pwConfirm) {
    pwConfirm.addEventListener('input', function () {
      if (pwSignup && this.value && this.value !== pwSignup.value) {
        this.style.borderColor = 'rgba(239,68,68,.5)';
      } else {
        this.style.borderColor = '';
      }
    });
  }
</script>
</body>
</html>