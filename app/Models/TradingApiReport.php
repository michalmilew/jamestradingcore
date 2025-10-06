<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TradingApiReport extends TradingApi
{
    use HasFactory;

    public $username;
    public $auth_token;
    public $url;

    public function __construct()
    {
        $this->username = env('API_AUTH_USERNAME', 'jamespereiraofficial');
        $this->auth_token = env('API_AUTH_TOKEN', 'jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR');
        $this->url = 'https://www.trade-copier.com/webservice/v4/reporting/getReporting.php';
    }

    public function getReports($year)
    {
        $reports = [];
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: ' . $this->username,
            'Auth-Token: ' . $this->auth_token,
        ];

        // Iterate through each month to get reports
        for ($month = 1; $month <= 12; $month++) {  // Months are 1-12
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = http_build_query(['month' => $month, 'year' => $year]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($ch);
            if ($response === false) {
                Log::error('cURL Error: ' . curl_error($ch));
                curl_close($ch);
                continue; // Skip this iteration if there's an error
            }

            $json = json_decode($response, true); // Decode as associative array
            curl_close($ch);

            if (is_array($json) && array_key_exists('reporting', $json)) {
                foreach ($json['reporting'] as $report) {
                    $reports[] = $report;
                }
            } else {
                Log::error('Invalid JSON or missing reporting key: ' . json_last_error_msg());
            }
        }

        return $reports;
    }

    public function getPnls($year)
    {
        $reportsSummedByUser = [];
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Auth-Username' => env('API_AUTH_USERNAME', 'jamespereiraofficial'),
            'Auth-Token' => env('API_AUTH_TOKEN', 'jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR'),
        ];
        $url = 'https://www.trade-copier.com/webservice/v4/reporting/getReporting.php';

        for ($month = 1; $month <= 12; $month++) {
            $response = Http::withHeaders($headers)->post($url, [
                'month' => $month,
                'year' => $year
            ]);

            $data = $response->json();
            Log::info('Data: ' . json_encode($data));

            if (isset($data['reporting'])) {
                foreach ($data['reporting'] as $report) {
                    $login = $report['login'];
                    if (!isset($reportsSummedByUser[$login])) {
                        $reportsSummedByUser[$login] = 0;
                    }
                    $reportsSummedByUser[$login] += $report['pnl'];
                }
            }
        }

        return $reportsSummedByUser;
    }

    public function getAccountReports($year, $account_id)
    {
        $reports = [];
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Auth-Username' => $this->username,
            'Auth-Token' => $this->auth_token,
        ];
        $url = 'https://www.trade-copier.com/webservice/v4/reporting/getReporting.php';

        $ch = [];
        for ($month = 0; $month < date('m'); $month++) {
            $ch[$month] = curl_init();
            curl_setopt($ch[$month], CURLOPT_URL, $url);
            curl_setopt($ch[$month], CURLOPT_POST, false);
            curl_setopt($ch[$month], CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch[$month], CURLOPT_RETURNTRANSFER, true);

            $filter = ['month' => $month + 1, 'year' => $year, 'account_id' => $account_id];
            curl_setopt($ch[$month], CURLOPT_POST, count($filter));
            curl_setopt($ch[$month], CURLOPT_POSTFIELDS, http_build_query($filter));

            $response = curl_exec($ch[$month]);
            if ($response === false) {
                Log::error('cURL Error: ' . curl_error($ch[$month]));
                curl_close($ch[$month]);
                continue;
            }

            $json = json_decode($response, true);
            curl_close($ch[$month]);

            if (is_array($json) && array_key_exists('reporting', $json)) {
                foreach ($json['reporting'] as $report) {
                    $tradingReport = new TradingReport();
                    $tradingReport->fill((array)$report);
                    $reports[] = $tradingReport;
                }
            } else {
                Log::error('Invalid JSON or missing reporting key: ' . json_last_error_msg());
            }
        }
        return $reports;
    }
}
