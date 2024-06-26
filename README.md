﻿# Weather_Reporter
## Overview

Weather Reporter is a web application designed to provide users with current weather information for a specified location. The application fetches weather data from the Weather.com API and generates a summary which users can use to send via email. The email content is generated using the OpenAI API.

## Features

- **Weather Data Fetching:** Retrieve current weather data for a specified location.
- **Weather Summary Generation:** Generate a weather summary based on the fetched data.
- **Email Generation:** Automatically generate an email containing the weather summary.
- **Email Sending:** Send the generated email to a specified recipient.

## Technologies Used

- **PHP:** Backend scripting language used to handle server-side logic.
- **Bootstrap:** Frontend framework for responsive design.
- **Weather.com API:** API used to fetch weather data.
- **OpenAI API:** API used to generate email content.

## Setup Instructions

1. **Clone the repository:**
    ```bash
    git clone https://github.com/sharmilidas33/Weather_Emailer.git
    ```

2. **Navigate to the project directory:**
    ```bash
    cd weather-reporter
    ```

3. **Install Composer dependencies:**
    ```bash
    composer install
    ```

4. **Create a `.env` file in the project root and add the following environment variables:**
    ```
    MY_WEATHER_API_KEY=your_weather_api_key
    MY_OPEN_AI_API_KEY=your_openai_api_key
    ```

5. **Start a local server:**
    - If you have PHP installed:
        ```bash
        php -S localhost:8000
        ```
    - If you have Node.js installed:
        ```bash
        npx http-server -p 8000
        ```

6. **Open your web browser and navigate to `http://localhost:8000` to access the application.**

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request with your changes.

