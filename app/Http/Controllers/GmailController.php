<?php

namespace App\Http\Controllers;

use Exception;
use Google_Client;
use Google_Service_Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;

class GmailController extends Controller
{
    private $client;
    private $oauth2Service;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope('https://www.googleapis.com/auth/gmail.send');
        $this->client->addScope('https://www.googleapis.com/auth/gmail.readonly');
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.email');
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.profile');

        $this->gmailService = new Google_Service_Gmail($this->client);
        $this->oauth2Service = new Google_Service_Oauth2($this->client);
    }

    public function redirectToGoogle()
    {
        return redirect($this->client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        $this->client->setAccessToken($token);

        $userInfo = $this->oauth2Service->userinfo->get();
        $email = $userInfo->getEmail();
        $userId = $userInfo->getId();

        // Store the token in the session for later use
        session([
            'gmail_email' => $email,
            'gmail_user_id' => $userId,
            'gmail_token' => $token,
        ]);

        return redirect()->route('gmail.dashboard');
    }

    public function dashboard($messageType = null, $message = null)
    {
        // Check if we have an access token in the session
        if ($token = session('gmail_token')) {

            $this->client->setAccessToken($token);

            $email = session('gmail_email');
            Log::info('Email from session: ' . $email);

            return View('index', ['email' => $email, $messageType => $message]);
        } else {
            return response('Not Found', 404);
        }
    }

    public function sendEmail()
    {
        // Check if we have an access token in the session
        if ($token = session('gmail_token')) {

            $this->client->setAccessToken($token);
            $email = session('gmail_email');
            // Sample email data
            $emailData = [
                'to' => $email,
                'subject' => 'Test Email',
                'body' => 'This is a test email sent from Laravel with Gmail API.'
            ];

            // Call the sendEmail function
            $result = $this->sendGmailMessage($emailData);
            if ($result) {
                // Email sent successfully, redirect with success message
                return redirect()->route('gmail.dashboard')->with(['success' => 'Email sent successfully!']);
            } else {
                // Email sending failed, redirect with error message
                return redirect()->route('gmail.dashboard')->with(['error' => 'Failed to send email.']);
            }
        } else {
            return response('Not Found', 404);
        }
    }
    private function sendGmailMessage($emailData)
    {
        try {
            // Ensure that the recipient address is provided
            if (!isset($emailData['to'])) {
                throw new Exception("Recipient address is missing.");
            }

            // Ensure that the 'to' value is a valid email address
            $to = filter_var($emailData['to'], FILTER_VALIDATE_EMAIL);
            if (!$to) {
                throw new Exception("Invalid recipient email address.");
            }
            // Create a new Gmail message
            $message = new Google_Service_Gmail_Message();

            // Set the raw message content
            $message->setRaw($this->createMessage($emailData));

            // Send the email
            $sentMessage = $this->gmailService->users_messages->send('me', $message);

            // Additional processing or logging can be added here

            return $sentMessage;
        } catch (Exception $e) {
            // Handle exceptions (e.g., log, return an error message)
            return "Error: " . $e->getMessage();
        }
    }
    private function createMessage($emailData)
    {
        $to = $emailData['to'];
        $subject = $emailData['subject'];
        $body = $emailData['body'];

        $message = "To: $to\r\n";
        $message .= "Subject: $subject\r\n";
        $message .= "Content-Type: text/plain; charset=utf-8\r\n";
        $message .= "\r\n$body";

        return base64_encode($message);
    }
    public function getInbox()
    {
        // Check if we have an access token in the session
        if ($token = session('gmail_token')) {
            $this->client->setAccessToken($token);
            $email = session('gmail_email');

            try {
                // Create a Gmail service instance
                $gmailService = new Google_Service_Gmail($this->client);

                // Fetch the user's inbox messages
                $messages = $gmailService->users_messages->listUsersMessages('me', ['labelIds' => 'INBOX', 'maxResults' => 10]);
                $messageContent = [];
                foreach ($messages->getMessages() as $message) {

                    // Fetch each message by its ID
                    $messageDetails = $gmailService->users_messages->get('me', $message->getId());

                    // Extract relevant information from $messageDetails as needed
                    // For example, you can access the subject and snippet:
                    $headers = $messageDetails->getPayload()->getHeaders();
                    $subjectHeader = collect($headers)->first(function ($header) {
                        return $header->getName() === 'Subject';
                    });
                    $subject = $subjectHeader ? $subjectHeader->getValue() : null;

                    // Extract the "Date" header
                    $dateHeader = collect($headers)->first(function ($header) {
                        return $header->getName() === 'Date';
                    });

                    // Check if the "Date" header is found and get its value
                    $date = $dateHeader ? $dateHeader->getValue() : null;

                    $snippet = $messageDetails->getSnippet();

                    // Store the message content or perform further processing
                    $messageContent[] = ['subject' => $subject, 'content' => $snippet, 'date' => $date];
                }

                // Process $messages as needed

                // Redirect with success message
                return view('index', ['email' => $email, 'success' => 'Inbox fetched successfully!', 'messages' => $messageContent]);
            } catch (\Exception $e) {
                // Handle exceptions, log errors, or redirect with an error message
                return view('index', ['email' => $email, 'error' => 'Failed to fetch inbox.']);
            }
        } else {
            return response('Not Found', 404);
        }
    }
}
