<?php
require 'includes/config.php';

$resetMessage = '';
$resetStatus = '';

if (isset($_GET["token"])) {
    $token = $_GET["token"];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newpass = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=?, token=NULL, token_expiry=NULL WHERE token=?");
        $stmt->bind_param("ss", $newpass, $token);
        if ($stmt->execute()) {
            $resetMessage = "Password reset successful!";
            $resetStatus = "success";
            echo "<script>setTimeout(() => location.href='login.php', 1500);</script>";
        } else {
            $resetMessage = "Failed to reset password!";
            $resetStatus = "error";
        }
    }
} else {
    echo "Invalid token";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
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

        .reset-box {
            display: flex;
            flex-direction: row;
            background-color: rgba(0, 0, 0, 0.4);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px #00ff99;
            max-width: 800px;
            width: 100%;
            gap: 2rem;
        }

        .reset-box img {
            width: 280px;
            height: auto;
            border-radius: 10px;
        }

        form {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.2rem;
        }

        h2 {
            text-align: center;
            color: #00ff99;
            margin-bottom: 0.5rem;
        }

        .form-group {
            position: relative;
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

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
        }

        button {
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
            text-align: center;
            font-size: 0.9rem;
        }

        .links a {
            color: #00ff99;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .reset-box {
                flex-direction: column;
                align-items: center;
            }

            .reset-box img {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>

<div class="reset-box">
    <img src="https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExMXp2NjVyb2plMW14d2JiZGR5cWEwa2pwcGthYmpibjYwbGswMWpscSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/xFoMueLgISxcuviukA/giphy.gif" alt="Reset Password GIF">
    <form method="POST">
        <h2>Reset Password</h2>
        <div class="form-group">
            <input type="password" name="password" id="password-field" placeholder="New Password" required>
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>
        <button type="submit">Reset Password</button>
        <p class="links"><a href="login.php">Back to login</a></p>
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

    <?php if (!empty($resetMessage)): ?>
    toastr.<?= $resetStatus ?>("<?= $resetMessage ?>");
    <?php endif; ?>
</script>

</body>
</html>
