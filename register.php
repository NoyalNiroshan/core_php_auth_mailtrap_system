<?php
require 'includes/config.php';

$regMessage = '';
$regStatus = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $secret = "6LcJzBMrAAAAAIZz4PZBQKA_Uz8hqezwa7mBvRsx";
    $response = $_POST['g-recaptcha-response'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
    $captchaSuccess = json_decode($verify);

    if (!$captchaSuccess->success) {
        $regMessage = "Please complete the CAPTCHA.";
        $regStatus = "error";
    } else {
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"]);
        $address = htmlspecialchars($_POST["address"]);
        $phone = htmlspecialchars($_POST["phone"]);
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];

        if ($password !== $confirmPassword) {
            $regMessage = "Passwords do not match!";
            $regStatus = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $regMessage = "Email already registered!";
                $regStatus = "error";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email, address, phone, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $address, $phone, $hashedPassword);
                if ($stmt->execute()) {
                    $regMessage = "Registration successful! Redirecting to login...";
                    $regStatus = "success";
                    echo "<script>setTimeout(() => location.href='login.php', 1500);</script>";
                } else {
                    $regMessage = "Error occurred during registration.";
                    $regStatus = "error";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Ubuntu', sans-serif;
            background: linear-gradient(-45deg, #0f0f0f, #1a1a1a, #2c2c2c, #000000);
            background-size: 600% 600%;
            animation: gradientBG 10s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #00ff99;
            padding: 2rem;
        }

        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .register-box {
            display: flex;
            flex-direction: row;
            background-color: rgba(0, 0, 0, 0.4);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px #00ff99;
            max-width: 1000px;
            width: 100%;
            gap: 2rem;
        }

        .register-box img {
            width: 280px;
            height: auto;
            border-radius: 10px;
            object-fit: contain;
        }

        form {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem 2rem;
        }

        h2 {
            grid-column: span 2;
            text-align: center;
            color: #00ff99;
            margin-bottom: 0.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        input {
            padding: 0.8rem;
            background-color: #111;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
        }

        #strengthMessage {
            grid-column: span 2;
            font-size: 0.9rem;
            text-align: center;
            margin-top: -0.5rem;
        }

        .weak { color: red; }
        .medium { color: orange; }
        .strong { color: #00ff99; }

        .g-recaptcha {
            grid-column: span 2;
            margin-top: 0.5rem;
        }

        button {
            grid-column: span 2;
            padding: 0.9rem;
            background: #00ff99;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            color: #000;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #00cc77;
        }

        .links {
            grid-column: span 2;
            text-align: center;
        }

        .links a {
            color: #00ff99;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .register-box {
                flex-direction: column;
                align-items: center;
            }

            form {
                grid-template-columns: 1fr;
            }

            h2, #strengthMessage, .g-recaptcha, button, .links {
                grid-column: span 1 !important;
            }
        }
    </style>
</head>
<body>

<div class="register-box">
    <img src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExY3h0NHllM3k2cjZhenp2ZGpmM2t6NzAzNWYyOTVsOHRxZ2JnaXZ1YyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/4Ugztq4GmrBRna3zaB/giphy.gif" alt="Register GIF">
    <form method="POST" onsubmit="return validateForm()">
        <h2>Register</h2>

        <div class="form-group">
            <input type="text" name="name" placeholder="Name" required>
        </div>

        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <input type="text" name="address" placeholder="Address" required>
        </div>

        <div class="form-group">
            <input type="text" name="phone" placeholder="Phone" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" id="password-field" placeholder="Password" onkeyup="checkPasswordStrength()" required>
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <div class="form-group">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <span class="toggle-password" onclick="toggleConfirmPassword()">👁️</span>
        </div>

        <div id="strengthMessage"></div>

        <div class="form-group g-recaptcha" data-sitekey="6LcJzBMrAAAAAGoa64VJbqpqry1XWIYtOs6VDvnG"></div>

        <button type="submit">Register</button>

        <p class="links"><a href="login.php">Already have an account?</a></p>
    </form>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    function togglePassword() {
        const field = document.getElementById("password-field");
        field.type = field.type === "password" ? "text" : "password";
    }

    function toggleConfirmPassword() {
        const field = document.getElementById("confirm_password");
        field.type = field.type === "password" ? "text" : "password";
    }

    function checkPasswordStrength() {
        const pwd = document.getElementById("password-field").value;
        const msg = document.getElementById("strengthMessage");
        let strength = "Weak password ❌", colorClass = "weak";

        if (pwd.length >= 8 && /[A-Z]/.test(pwd) && /\d/.test(pwd) && /[!@#$%^&*]/.test(pwd)) {
            strength = "Strong password ✅";
            colorClass = "strong";
        } else if (pwd.length >= 6) {
            strength = "Medium password ⚠️";
            colorClass = "medium";
        }

        msg.className = colorClass;
        msg.textContent = strength;
    }

    function validateForm() {
        const pass = document.getElementById("password-field").value;
        const confirm = document.getElementById("confirm_password").value;
        if (pass !== confirm) {
            toastr.error("Passwords do not match!");
            return false;
        }
        return true;
    }

    <?php if (!empty($regMessage)): ?>
    toastr.<?= $regStatus ?>("<?= $regMessage ?>");
    <?php endif; ?>
</script>

</body>
</html>
