<?php
session_start();
require_once 'includes/db.php';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}

// Handle Signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);

        // Auto login after signup
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        $error = "Email already exists";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Study Assistant</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="center-screen">
        <div class="auth-card glass-panel">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p class="text-muted">Sign in to continue your learning journey</p>
            </div>

            <?php if (isset($error)): ?>
                <div style="color: var(--danger-color); margin-bottom: 15px;"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="glass-input" required placeholder="john@example.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="glass-input" required placeholder="••••••">
                </div>
                <button type="submit" class="glass-button primary" style="width: 100%">Log In</button>
            </form>

            <div style="margin-top: 20px;">
                <p class="text-muted">Don't have an account? <a href="#" onclick="toggleForms()"
                        style="color: var(--accent-color)">Sign Up</a></p>
            </div>

            <!-- Signup Form (Hidden by default) -->
            <form id="signupForm" method="POST" action="" style="display: none; margin-top: 20px;">
                <input type="hidden" name="action" value="signup">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="glass-input" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="glass-input" required placeholder="john@example.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="glass-input" required placeholder="••••••">
                </div>
                <button type="submit" class="glass-button primary" style="width: 100%">Create Account</button>
            </form>
        </div>
    </div>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('loginForm');
            const signupForm = document.getElementById('signupForm');
            const title = document.querySelector('.auth-header h1');

            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                signupForm.style.display = 'none';
                title.textContent = 'Welcome Back';
            } else {
                loginForm.style.display = 'none';
                signupForm.style.display = 'block';
                title.textContent = 'Create Account';
            }
        }
    </script>
</body>

</html>