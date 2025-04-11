<?php
session_start();
require 'includes/config.php';

$loginMessage = '';
$loginStatus = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $secret = "6LcJzBMrAAAAAIZz4PZBQKA_Uz8hqezwa7mBvRsx";
    $response = $_POST['g-recaptcha-response'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
    $captchaSuccess = json_decode($verify);

    if ($captchaSuccess->success) {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($name, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION["user"] = $name;
                $loginMessage = "Login successful!";
                $loginStatus = "success";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 1500);
                </script>";
            } else {
                $loginMessage = "Invalid password!";
                $loginStatus = "error";
            }
        } else {
            $loginMessage = "No account found!";
            $loginStatus = "error";
        }
    } else {
        $loginMessage = "Please complete the CAPTCHA!";
        $loginStatus = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #00ff99;
        }

        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .login-box {
            display: flex;
            flex-direction: row;
            background-color: rgba(0, 0, 0, 0.4);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px #00ff99;
            gap: 2rem;
            max-width: 850px;
            width: 95%;
        }

        .login-box img {
            width: 300px;
            height: auto;
            border-radius: 10px;
        }

        form {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        h2 {
            text-align: center;
            color: #00ff99;
            margin-bottom: 1rem;
        }

        input {
            padding: 0.9rem;
            background-color: #111;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
            width: 100%;
        }

        .input-group {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
        }

        .g-recaptcha {
            width: 100% !important;
        }

        button {
            padding: 0.9rem;
            background: #00ff99;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            color: #000;
            cursor: pointer;
        }

        button:hover {
            background: #00cc77;
        }

        .links {
            text-align: center;
        }

        .links a {
            color: #00ff99;
            text-decoration: none;
            margin: 0 8px;
        }

        @media (max-width: 768px) {
            .login-box {
                flex-direction: column;
                align-items: center;
            }

            .login-box img {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcHJobmpnMHR0bnM1MGQxMzBpOGN1NzkzazBuMmhnYmV6YnNqZWdteSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/C98Iiwh4k3oBGLqD5A/giphy.gif" alt="Login GIF">
    <form method="POST">
        <h2>Login</h2>

        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group input-group">
            <input type="password" name="password" id="password-field" placeholder="Password" required>
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6LcJzBMrAAAAAGoa64VJbqpqry1XWIYtOs6VDvnG"></div>
        </div>

        <button type="submit">Login</button>

        <p class="links">
            <a href="register.php">Create account</a> | <a href="forgot_password.php">Forgot password?</a>
        </p>
    </form>
</div>

<!-- Toastr scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    function togglePassword() {
        const field = document.getElementById("password-field");
        field.type = field.type === "password" ? "text" : "password";
    }

    <?php if (!empty($loginMessage)): ?>
    toastr.<?= $loginStatus ?>("<?= $loginMessage ?>");
    <?php endif; ?>
</script>

</body>
</html>
