<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

if (!function_exists('getReadableName')) {
    /**
     * Get a readable name from an email address if needed.
     *
     * @param string $name
     * @return string
     */
    function getReadableName($name)
    {
        // Check if the name looks like an email (contains "@" and ".")
        if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
            // Extract the part before the "@" symbol
            $parts = explode('@', $name);
            $namePart = $parts[0];

            // Split the name part by dots, dashes, or underscores
            $nameParts = preg_split('/[._-]/', $namePart);

            // Capitalize the first letter of each part
            $readableName = ucfirst($nameParts[0] ?? 'Unknown');

            return $readableName . ' ****';
        }

        // If it's not an email, process normally
        $nameParts = explode(' ', trim($name));
        $firstName = ucfirst($nameParts[0] ?? 'Unknown');

        return $firstName . ' *****';
    }
}

if (!function_exists('sendResendEmail')) {
    /**
     * Send an email using the Resend API.
     *
     * @param string $to The recipient's email address.
     * @param string $from The sender's email address.
     * @param string $subject The subject of the email.
     * @param string $htmlContent The HTML content of the email.
     * @return array The response from the Resend API.
     * @throws \Exception If there is an error with the request.
     */
    function sendResendEmail($to, $from, $subject, $htmlContent)
    {
        // Fetch the API key from the configuration file
        $apiKey = env('RESEND_API_KEY');

        // Define the Resend API endpoint
        $endpoint = 'https://api.resend.com/emails';

        // Create a new Guzzle HTTP client
        $client = new Client();

        try {
            // Send the POST request to the Resend API
            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'from' => "James Pereira <{$from}>",
                    // 'from' => "{$from}",
                    'to' => [$to],
                    'subject' => $subject,
                    'html' => $htmlContent,
                    'text' => $subject
                ],
            ]);

            // Return the response body as an associative array
            return json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Get the error response body and status code
            $responseBody = $e->getResponse()->getBody()->getContents();
            $statusCode = $e->getResponse()->getStatusCode();

            // Log the error and throw an exception with the details
            Log::error('Failed to send email via Resend API: ' . $responseBody);
            throw new \Exception('Error: ' . $responseBody . ', HTTP Code: ' . $statusCode);
        }
    }
}


if (!function_exists('parseNameFromEmailUsingNameAPI')) {
    function parseNameFromEmailUsingNameAPI($email)
    {
        $apiKey = env("NAME_API"); // Replace with your API key
        $url = "https://rc50-api.nameapi.org/rest/v5.3/parser/personnameparser?apiKey={$apiKey}";

        // JSON payload for the API
        $data = [
            "inputPerson" => [
                "type" => "NaturalInputPerson",
                "personName" => [
                    "fullName" => $email
                ],
                "emailAddresses" => [
                    [
                        "emailAddress" => $email
                    ]
                ]
            ],
            "context" => [
                "priority" => "REALTIME"
            ]
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        // Decode the JSON response
        $result = json_decode($response, true);

        // Extract and format the name if available
        if (
            isset($result['matches'][0]['parsedPerson']['name']['given']) &&
            isset($result['matches'][0]['parsedPerson']['name']['surname'])
        ) {
            $firstName = $result['matches'][0]['parsedPerson']['name']['given'];
            $lastName = $result['matches'][0]['parsedPerson']['name']['surname'];
            return ucfirst($firstName) . ' ' . ucfirst($lastName);
        } elseif (isset($result['matches'][0]['parsedPerson']['name']['given'])) {
            return ucfirst($result['matches'][0]['parsedPerson']['name']['given']);
        }

        // Return the original email prefix if no name is found
        return ucfirst(explode('@', $email)[0]);
    }
}
