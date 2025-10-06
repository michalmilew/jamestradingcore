<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewTradingApiClient
{
    protected $base_url;
    protected $username;
    protected $auth_token;
    protected $api_key;

    public function __construct()
    {
        // Get configuration from environment variables
        $this->base_url = env('METATRADER_API_BASE_URL', 'https://api.metatrader.com/v1');
        $this->username = env('METATRADER_API_USERNAME', '');
        $this->auth_token = env('METATRADER_API_AUTH_TOKEN', '');
        $this->api_key = env('METATRADER_API_KEY', '');
    }

    /**
     * Get all accounts or specific accounts by ID
     *
     * @param array|null $account_ids
     * @return array
     * @throws \Exception
     */
    public function getAccounts($account_ids = null)
    {
        try {
            $endpoint = '/accounts';
            $params = [];
            
            if ($account_ids) {
                $params['account_ids'] = implode(',', $account_ids);
            }

            $response = $this->sendRequest('GET', $endpoint, $params);
            
            // Transform response to match expected format
            $accounts = [];
            foreach ($response['accounts'] ?? [] as $accountData) {
                $account = new TradingAccount();
                $account->fill($accountData);
                $accounts[] = $account;
            }

            return $accounts;
        } catch (\Exception $e) {
            Log::error('NewTradingApiClient getAccounts error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get a single account by ID
     *
     * @param string $account_id
     * @return TradingAccount
     * @throws \Exception
     */
    public function getAccount($account_id)
    {
        try {
            $endpoint = "/detail/{$account_id}";
            $response = $this->sendRequest('GET', $endpoint);
            
            if ($response['account'] != null) {
                $account = new TradingAccount();
                $account->fill($response['account']);
                return $account;

            } else {
                return null;
            }
            
        } catch (\Exception $e) {
            Log::error('NewTradingApiClient getAccount error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if an account exists by login
     *
     * @param string $login
     * @return string Account ID if exists, '0' if not
     * @throws \Exception
     */
    public function accountExists($login)
    {
        try {
            $endpoint = '/accounts/search';
            $params = ['login' => $login];
            
            $response = $this->sendRequest('GET', $endpoint, $params);
            
            if (isset($response['account_id'])) {
                return $response['account_id'];
            }
            
            return '0';
        } catch (\Exception $e) {
            Log::error('NewTradingApiClient accountExists error: ' . $e->getMessage());
            return '0';
        }
    }

    /**
     * Create a new account
     *
     * @param array $account_data
     * @return TradingAccount
     * @throws \Exception
     */
    public function createAccount($account_data)
    {
        try {
            $endpoint = '/accounts';
            
            // Transform data to match new API format
            $apiData = $this->transformAccountDataForCreation($account_data);
            
            $response = $this->sendRequest('POST', $endpoint, [], $apiData);
            
            $account = new TradingAccount();
            $account->fill($response['account'] ?? []);
            
            return $account;
        } catch (\Exception $e) {
            Log::error('NewTradingApiClient createAccount error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing account
     *
     * @param array $account_data
     * @return TradingAccount
     * @throws \Exception
     */
    public function updateAccount($account_data)
    {
        try {
            $account_id = $account_data['account_id'];
            $endpoint = "/accounts/{$account_id}";
            
            // Transform data to match new API format
            $apiData = $this->transformAccountDataForUpdate($account_data);
            
            $response = $this->sendRequest('PUT', $endpoint, [], $apiData);
            
            $account = new TradingAccount();
            $account->fill($response['account'] ?? []);
            
            return $account;
        } catch (\Exception $e) {
            Log::error('NewTradingApiClient updateAccount error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an account
     *
     * @param string $account_id
     * @return TradingAccount
     * @throws \Exception
     */
    public function deleteAccount($account_id)
    {
        try {
            $endpoint = "/accounts/{$account_id}";
            $response = $this->sendRequest('DELETE', $endpoint);
            
            $account = new TradingAccount();
            $account->fill($response['account'] ?? []);
            
            return $account;
        } catch (\Exception $e) {
            Log::error('NewTradingApiClient deleteAccount error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send HTTP request to the MetaTrader API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param array $data
     * @return array
     * @throws \Exception
     */
    protected function sendRequest($method, $endpoint, $params = [], $data = [])
    {
        $url = $this->base_url . $endpoint;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        try {
            // Use connection pooling and shorter timeout for better concurrency
            $response = Http::withHeaders($headers)
                ->timeout(600)
                ->retry(2, 1000) // Retry failed requests
                ->send($method, $url, $data ? ['json' => $data] : []);

            if ($response->successful()) {
                return $response->json();
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error'] ?? $response->body();
                $errorCode = $errorData['code'] ?? $response->status();
                
                // Log rate limiting errors specifically
                if ($response->status() === 429) {
                    Log::warning('MetaTrader API rate limit exceeded', [
                        'url' => $url,
                        'method' => $method,
                        'retry_after' => $response->header('Retry-After')
                    ]);
                }
                
                throw new \Exception($errorMessage, $errorCode);
            }
        } catch (\Exception $e) {
            Log::error('NewTradingApiClient HTTP request failed: ' . $e->getMessage(), [
                'method' => $method,
                'url' => $url,
                'data' => $data,
                'error_code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Transform account data for creation API
     *
     * @param array $account_data
     * @return array
     */
    protected function transformAccountDataForCreation($account_data)
    {
        return [
            'login' => $account_data['login'] ?? '',
            'password' => $account_data['password'] ?? '',
            'server' => $account_data['server'] ?? '',
            'name' => $account_data['name'] ?? '',
            'email' => $account_data['email'] ?? '',
            'groupid' => $account_data['groupid'] ?? '',
            'subscription' => $account_data['subscription'] ?? '',
            'environment' => $account_data['environment'] ?? 'Real',
            'status' => $account_data['status'] ?? '1',
            'broker' => $account_data['broker'] ?? 'mt4',
            'user_id' => $account_data['user_id'] ?? '',
            'platform_type' => $account_data['platform_type'] ?? 'mt4',
        ];
    }

    /**
     * Transform account data for update API
     *
     * @param array $account_data
     * @return array
     */
    protected function transformAccountDataForUpdate($account_data)
    {
        $updateData = [];
        
        if (isset($account_data['name'])) {
            $updateData['name'] = $account_data['name'];
        }
        
        if (isset($account_data['status'])) {
            $updateData['status'] = $account_data['status'];
        }
        
        if (isset($account_data['groupid'])) {
            $updateData['group'] = $account_data['groupid'];
        }
        
        return $updateData;
    }
} 