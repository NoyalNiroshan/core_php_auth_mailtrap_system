<?php
require 'includes/config.php';
require 'includes/email_config.php';

$fpMessage = '';
$fpStatus = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $secret = "6LcJzBMrAAAAAIZz4PZBQKA_Uz8hqezwa7mBvRsx";
    $response = $_POST['g-recaptcha-response'];
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
    $captchaSuccess = json_decode($verify);

    if (!$captchaSuccess->success) {
        $fpMessage = "Please complete the CAPTCHA!";
        $fpStatus = "error";
    } else {
        $email = $_POST["email"];
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE users SET token = ?, token_expiry = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expiry, $email);

            if ($stmt->execute()) {
                if (sendVerificationEmail($email, $token)) {
                    $fpMessage = "Password reset link sent to your email.";
                    $fpStatus = "success";
                } else {
                    $fpMessage = "Failed to send email.";
                    $fpStatus = "error";
                }
            } else {
                $fpMessage = "Failed to update token.";
                $fpStatus = "error";
            }
        } else {
            $fpMessage = "Email not found!";
            $fpStatus = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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

        .forgot-box {
            display: flex;
            flex-direction: row;
            background-color: rgba(0, 0, 0, 0.4);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 20px #00ff99;
            max-width: 900px;
            width: 100%;
            gap: 2rem;
        }

        .forgot-box img {
            width: 280px;
            height: auto;
            border-radius: 10px;
            object-fit: contain;
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

        input {
            padding: 0.9rem;
            background-color: #111;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
            width: 100%;
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
            .forgot-box {
                flex-direction: column;
                align-items: center;
            }

            .forgot-box img {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>

<div class="forgot-box">
    <img src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExeTZ2ZWFpMGFnOHF4Z2tlMjgycHdtbHZsZzRldXh2ZDJ5MzM1eHZvZyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/qxXWdM1lssRMqIa4Bm/giphy.gif" alt="Forgot Password GIF">
    <form method="POST">
        <h2>Forgot Password</h2>
        <input type="email" name="email" placeholder="Enter your email" required>
        <div class="g-recaptcha" data-sitekey="6LcJzBMrAAAAAGoa64VJbqpqry1XWIYtOs6VDvnG"></div>
        <button type="submit">Send Reset Link</button>
        <p class="links"><a href="login.php">Back to login</a></p>
    </form>
</div>

<!-- Toastr & jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    <?php if (!empty($fpMessage)): ?>
    toastr.<?= $fpStatus ?>("<?= $fpMessage ?>");
    <?php endif; ?>
</script>

</body>
</html>
