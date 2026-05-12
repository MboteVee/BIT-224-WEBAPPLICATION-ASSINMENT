<?php
// frontend/js/login.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Real Estate</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Login to Your Account</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-submit">Login</button>
                <p class="auth-link">Don't have an account? <a href="register.php">Register here</a></p>
            </form>
            <div id="message" class="message"></div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = 'landing.php';
                } else {
                    document.getElementById('message').textContent = data.message;
                    document.getElementById('message').className = 'message error';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('message').textContent = 'Login failed. Please try again.';
                document.getElementById('message').className = 'message error';
            }
        });
    </script>
</body>
</html>