<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_email'])) {
    $location = $_POST['location'];
    $summary = $_POST['summary'];
    $email_content = $_POST['email_content'];

    $to = "sharmildas36@gmail.com"; 
    $from = "sharmilidas1@gmail.com"; 
    $subject = "Weather Report for {$location}";

    // Prepare the email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: {$from}" . "\r\n";

    // Send the email
    if (mail($to, $subject, $email_content, $headers)) {
        echo "<div class='alert alert-success'>Email sent successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error sending email. Please try again later.</div>";
    }
}

// If the request method is not POST or the 'send_email' button was not clicked, display the email content form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_email'])) {
    $location = $_POST['location'];
    $summary = $_POST['summary'];

    // Compose email content using OpenAI API
    $openai_api_key = $_ENV['MY_OPEN_AI_API_KEY'];
    $prompt = "Compose an email with the following weather summary:\n\nLocation: {$location}\nWeather Summary: {$summary}\n\nPlease include a greeting, the weather summary, and a closing statement.";
    $email_content = generateEmailContent($openai_api_key, $prompt);

    if ($email_content) {
        echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Email Content</title>
    <!-- Bootstrap CSS -->
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>

<div class='container mt-5'>
    <div class='row justify-content-center'>
        <div class='col-md-8'>
            <h2 class='text-center mb-4'>Generated Email Content</h2>
            <textarea class='form-control mb-4' rows='8' readonly>{$email_content}</textarea>
            <form action='' method='post'>
                <input type='hidden' name='location' value='{$location}'>
                <input type='hidden' name='summary' value='{$summary}'>
                <input type='hidden' name='email_content' value='{$email_content}'>
                <button type='submit' class='btn btn-primary btn-block' name='send_email'>Send Email</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
</body>
</html>
";
    } else {
        echo "<div class='alert alert-danger'>Error generating email content. Please try again later.</div>";
    }
} else {
    //Display the form for the user to enter the location and weather summary
    echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Weather Email</title>
    <!-- Bootstrap CSS -->
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>

<div class='container mt-5'>
    <div class='row justify-content-center'>
        <div class='col-md-8'>
            <h2 class='text-center mb-4'>Generate Weather Email</h2>
            <form action='' method='post'>
                <div class='form-group'>
                    <label for='location'>Location</label>
                    <input type='text' class='form-control' id='location' name='location' required>
                </div>
                <div class='form-group'>
                    <label for='summary'>Weather Summary</label>
                    <textarea class='form-control' id='summary' name='summary' rows='4' required></textarea>
                </div>
                <button type='submit' class='btn btn-primary btn-block' name='generate_email'>Generate Email</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
</body>
</html>
";
}

function generateEmailContent($api_key, $prompt) {
    $headers = [
        "Authorization: Bearer {$api_key}",
        "Content-Type: application/json",
    ];

    $data = [
        "model" => "davinci-codex",
        "prompt" => $prompt,
        "max_tokens" => 50,
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $err = curl_error($ch);

    curl_close($ch);

    if ($err) {
        echo "cURL Error #:" . $err;
        return false;
    } else {
        $response = json_decode($response, true);

        if (isset($response['choices'][0]['text'])) {
            return $response['choices'][0]['text'];
        } else {
            error_log("OpenAI API response does not contain email content.");
            return false;
        }
    }
}
?>
