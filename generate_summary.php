<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Reporter</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-image: url('https://png.pngtree.com/thumb_back/fw800/background/20220613/pngtree-blurred-screen-background-with-earth-map-and-global-markings-photo-image_31497054.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            margin-top: 100px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
        }
        h2 {
            color: #333;
            margin-bottom: 30px;
        }
        p {
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $location = $_POST["location"];
                
                // Weather.com API
                $weather_api_key = $_ENV['MY_WEATHER_API_KEY'];
                $weather_url = "https://api.weatherapi.com/v1/current.json?key={$weather_api_key}&q={$location}";
                
                $ch_weather = curl_init();
                curl_setopt($ch_weather, CURLOPT_URL, $weather_url);
                curl_setopt($ch_weather, CURLOPT_RETURNTRANSFER, 1);
                
                $weather_data_json = curl_exec($ch_weather);
                curl_close($ch_weather);
                
                $weather_data = json_decode($weather_data_json, true);
                
                if(isset($weather_data['error'])) {
                    echo "<div class='alert alert-danger'>Error fetching weather data: " . $weather_data['error']['message'] . "</div>";
                    exit;
                }
                
                $temp_c = isset($weather_data['current']['temp_c']) ? $weather_data['current']['temp_c'] . '°C' : 'N/A';
                $humidity = isset($weather_data['current']['humidity']) ? $weather_data['current']['humidity'] . '%' : 'N/A';
                $precipitation = isset($weather_data['current']['precip_mm']) ? $weather_data['current']['precip_mm'] . 'mm' : 'N/A';
                $condition = isset($weather_data['current']['condition']['text']) ? $weather_data['current']['condition']['text'] : 'N/A';

                // Generate Weather Summary
                $summary = "Current weather in {$location}: Temperature is {$temp_c}, humidity is {$humidity}, precipitation is {$precipitation}, and the condition is {$condition}.";

                echo "<h2 class='text-center'>Weather Report for {$location}</h2>";
                echo "<div class='row'>";
                echo "<div class='col-md-12'><p class='text-center'>{$summary}</p></div>";
                echo "</div>";

                // Generate Email Button
                echo "<div class='row mt-4'>";
                echo "<div class='col-md-12 text-center'>";
                echo "<form action='generate_email.php' method='post'>";
                echo "<input type='hidden' name='location' value='{$location}'>";
                echo "<input type='hidden' name='summary' value='{$summary}'>";
                echo "<button type='submit' class='btn btn-primary' name='generate_email'>Generate Email</button>";
                echo "</form>";
                echo "</div>";
                echo "</div>";

            }
            ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
