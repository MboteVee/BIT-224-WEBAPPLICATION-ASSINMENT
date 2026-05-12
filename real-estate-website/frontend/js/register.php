<?php
// frontend/js/register.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Real Estate</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Create an Account</h2>
            <form id="registerForm">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-submit">Register</button>
                <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
            </form>
            <div id="message" class="message"></div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const full_name = document.getElementById('full_name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            
            if (password !== confirm_password) {
                document.getElementById('message').textContent = 'Passwords do not match';
                document.getElementById('message').className = 'message error';
                return;
            }
            
            if (password.length < 6) {
                document.getElementById('message').textContent = 'Password must be at least 6 characters';
                document.getElementById('message').className = 'message error';
                return;
            }
            
            try {
                const response = await fetch('http://localhost/BIT-224-WEBAPPLICATION-ASSINMENT/real-estate-website/backend/auth/reg.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ full_name, email, phone, password })
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
                document.getElementById('message').textContent = 'Registration failed. Please try again.';
                document.getElementById('message').className = 'message error';
            }
        });
    </script>
</body>
</html>