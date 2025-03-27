<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php'; // ✅ Load Composer

class Google extends CI_Controller
{
    public function index()
    {
        // ✅ Initialize Google Client
        $client = new Google\Client();
        $client->setAuthConfig(APPPATH . 'config/google_credentials.json'); // ✅ Correct Path
        $client->setScopes(Google\Service\Calendar::CALENDAR);
        $client->setAccessType('offline'); // Required for refresh tokens
        $client->setPrompt('select_account consent');

        // ✅ If user has no access token, generate authentication URL
        if (!isset($_SESSION['access_token'])) {
            $authUrl = $client->createAuthUrl();
            echo "Click <a href='$authUrl'>here</a> to authenticate.";
            exit;
        }

        // ✅ Set Access Token
        $client->setAccessToken($_SESSION['access_token']);

        // ✅ Create Google Calendar Service
        $service = new Google\Service\Calendar($client);

        // ✅ Define event details
        $event = new Google\Service\Calendar\Event([
            'summary'     => 'Camping',
            'location'    => 'Online via Zoom',
            'description' => 'Project discussion',
            'start'       => ['dateTime' => '2025-03-28T14:00:00', 'timeZone' => 'Asia/Kolkata'],
            'end'         => ['dateTime' => '2025-03-28T15:00:00', 'timeZone' => 'Asia/Kolkata'],
        ]);

        // ✅ Insert event into the primary calendar
        $calendarId = 'primary';
        $createdEvent = $service->events->insert($calendarId, $event);

        echo "Event Created: <a href='{$createdEvent->htmlLink}' target='_blank'>View Event</a>";
    }

    // ✅ OAuth 2.0 Callback Function
    public function call()
    {
        $client = new Google\Client();
        $client->setAuthConfig(APPPATH . 'config/google_credentials.json');
        $client->setRedirectUri('http://localhost/hands_on_ci/index.php/google/call');
        $client->setScopes(Google\Service\Calendar::CALENDAR);

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION['access_token'] = $token;
            print_r($token);
            // redirect('calender');
        } else {
            echo "Authorization failed.";
        }
    }
}
