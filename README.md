# Gmail API Demo in Laravel 10

- This project demonstrates how to integrate the Gmail API into a Laravel 10 application to fetch inbox data and send emails.

## Technology and Versions

- Laravel Version: 10

##Prerequisites

Before running this project, make sure you have the following:

- PHP >= 8.1 installed on your system
- Composer installed
- A Google Cloud Platform project with the Gmail API enabled
- OAuth 2.0 credentials (Client ID and Client Secret) for your project

## Installation

1. Clone this repository to your local machine:
    - git clone <repository-url>
2. Navigate into the project directory:
    - cd gmail-api-demo-laravel-10
3.Install PHP dependencies using Composer:
    - composer install
4. Copy the .env.example file to .env
    - cp .env.example .env
5. Update the .env file with your Google OAuth 2.0 credentials and other necessary configurations:
    - GOOGLE_CLIENT_ID=your-client-id
    - GOOGLE_CLIENT_SECRET=your-client-secret
    - GOOGLE_REDIRECT_URI=your-redirect-uri

##Usage
1. Run the development server:
    - php artisan serve
2. Navigate to the provided URL in your web browser.
3. Follow the on-screen instructions to authenticate with your Google account and grant access to the application.
4. Once authenticated, you can fetch inbox data and send emails using the provided interfaces.




