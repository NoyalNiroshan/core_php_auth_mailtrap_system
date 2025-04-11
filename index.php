<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jaffna Weather Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Ubuntu', sans-serif;
            min-height: 100vh;
            background: linear-gradient(to right, #1f4037, #99f2c8);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            color: #fff;
        }

        .dashboard {
            background: rgba(0, 0, 0, 0.5);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
            text-align: center;
        }

        .dashboard h1 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #00ff99;
        }

        .weather-icon img {
            width: 100px;
            height: 100px;
        }

        .temp {
            font-size: 3rem;
            font-weight: bold;
            margin: 1rem 0 0.5rem;
        }

        .desc {
            font-size: 1.1rem;
            text-transform: capitalize;
        }

        .details {
            margin-top: 1.5rem;
            text-align: left;
        }

        .details p {
            margin: 0.4rem 0;
            font-size: 0.95rem;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 2rem;
            background: #00ff99;
            color: #000;
            font-weight: bold;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #00cc77;
        }
    </style>
</head>
<body>

<div class="dashboard" id="weather-dashboard">
    <h1>Weather in Jaffna</h1>
    <div class="weather-icon"><img src="" id="icon" alt="Weather Icon"></div>
    <div class="temp" id="temperature">--°C</div>
    <div class="desc" id="description">Loading...</div>

    <div class="details" id="details">
        <p>Humidity: --%</p>
        <p>Wind: -- km/h</p>
    </div>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<script>
    const apiKey = '28e6a0c0bc9e8679453f9c194207fb9d';
    const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=Jaffna,LK&units=metric&appid=${apiKey}`;

    fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
            document.getElementById("temperature").textContent = `${Math.round(data.main.temp)}°C`;
            document.getElementById("description").textContent = data.weather[0].description;
            document.getElementById("icon").src = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;
            document.getElementById("details").innerHTML = `
                <p>Humidity: ${data.main.humidity}%</p>
                <p>Wind: ${(data.wind.speed * 3.6).toFixed(1)} km/h</p>
                <p>Sunrise: ${new Date(data.sys.sunrise * 1000).toLocaleTimeString()}</p>
                <p>Sunset: ${new Date(data.sys.sunset * 1000).toLocaleTimeString()}</p>
            `;
        })
        .catch(err => {
            document.getElementById("description").textContent = "Failed to load weather.";
            console.error(err);
        });
</script>

</body>
</html>
