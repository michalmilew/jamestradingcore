<?php

namespace App\Models;
use App\Models\TradingAccount;

class TradingApiClient extends TradingApi
 {

    public function listAccounts(){
        $url = "https://www.trade-copier.com/webservice/v4" . '/account/getAccounts.php';
        $response = $this->sendRequest( 'GET', $url );

        if ( $response[ 'status' ] !== 200 ) {
            throw new \Exception( "Failed to retrieve accounts: {$response['body']}" );
        }

        $accounts = [];
        foreach ( $response[ 'body' ][ 'accounts' ] as $account ) {
            $tradingAccount = new TradingAccount();
            $tradingAccount->fill(( array )$account);
            $accounts[] = $tradingAccount;
        }

        return $accounts;
    }
    public function getAccounts($account_id = null){
        $url="https://www.trade-copier.com/webservice/v4/account/getAccounts.php";
        $headers =  array(
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: jamespereiraofficial',
            'Auth-Token: jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR',
        );
        // Open connection
        $ch = curl_init();

        // Setting the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($account_id != null){
            $filter = ['account_id' => $account_id];
            //$filter = ['account_id' => ['190792', '190793']];
            //$filter = [];

            curl_setopt($ch, CURLOPT_POST, count($filter));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($filter));
        }


        // Execute request
        $json = json_decode(curl_exec($ch));

        // Close connection
        curl_close($ch);

        $accounts = [];
        if(array_key_exists('accounts', $json))
        {
            foreach ($json->accounts as $account ) {
                $tradingAccount = new TradingAccount();
                $tradingAccount->fill(( array )$account);

                $accounts[] = $tradingAccount;
            }
        }
        else
        {
            //This is an error message that can be access like this:
            //echo $json->code." ".$json->error;
            throw new \Exception( $json->code." ".$json->error );
        }


        return $accounts;
    }

    /**
     * Checks if an account with the given login exists.
     *
     * @param string $login The login of the account to check.
     * @return bool True if the account exists, false otherwise.
     * @throws \Exception If there is an error retrieving the accounts.
     */
    public function accountExists($login)
    {
        try {
            $accounts = $this->getAccounts(); // Retrieve all accounts
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve accounts: " . $e->getMessage());
        }

        // Check if the account with the given login exists
        foreach ($accounts as $account) {
            if ($account->login === $login) {
                return $account->account_id;
            }
        }

        return '0';
    }
    public function getAccounts4($account_id = null){

        $url="https://www.trade-copier.com/webservice/v4/account/getAccounts.php";
        $headers =  array(
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: jamespereiraofficial',
            'Auth-Token: jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR',
        );
        // Open connection
        $ch = curl_init();

        // Setting the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($account_id != null){
            $filter = ['account_id' => $account_id];
            //$filter = ['account_id' => ['190792', '190793']];
            //$filter = [];

            curl_setopt($ch, CURLOPT_POST, count($filter));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($filter));
        }


        // Execute request
        $json = json_decode(curl_exec($ch));

        // Close connection
        curl_close($ch);

        $accounts = [];
        if(array_key_exists('accounts', $json))
        {
            foreach ($json->accounts as $account ) {
                $tradingAccount = new TradingAccount();
                $tradingAccount->fill(( array )$account);


                $accounts[] = $tradingAccount;
            }
        }
        else
        {
            //This is an error message that can be access like this:
            //echo $json->code." ".$json->error;
            throw new \Exception( $json->code." ".$json->error );
        }


        return $accounts;
    }
    public function getAccount($account_id){

        $url="https://www.trade-copier.com/webservice/v4/account/getAccounts.php";
        $headers =  array(
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: jamespereiraofficial',
            'Auth-Token: jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR',
        );
        // Open connection
        $ch = curl_init();

        // Setting the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $filter = ['account_id' => $account_id];
        //$filter = ['account_id' => ['190792', '190793']];
        //$filter = [];

        curl_setopt($ch, CURLOPT_POST, count($filter));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($filter));

        // Execute request
        $json = json_decode(curl_exec($ch));

        // Close connection
        curl_close($ch);

        $accounts = [];
        if(array_key_exists('accounts', $json))
        {
            foreach ($json->accounts as $account ) {
                $tradingAccount = new TradingAccount();
                $tradingAccount->fill(( array )$account);
                $accounts[] = $tradingAccount;
            }
            return $accounts[0];
        }
        else
        {
            //This is an error message that can be access like this:
            //echo $json->code." ".$json->error;
            throw new \Exception( $json->code." ".$json->error );
        }

    }
    public function deleteAccount($account_id){

        $url="https://www.trade-copier.com/webservice/v4/account/deleteAccount.php";
        $headers =  array(
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: jamespereiraofficial',
            'Auth-Token: jM3NTc1N2YxNDFmODIwZjQ3NTg3OWR',
        );
        // Open connection
        $ch = curl_init();

        // Setting the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Account ID to be removed
	    $account = [
            'account_id' => $account_id
        ];

        curl_setopt($ch, CURLOPT_POST, count($account));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($account));

        // Execute request
        $json = json_decode(curl_exec($ch));

        // Close connection
        curl_close($ch);
        //We access the updated account data
        if(array_key_exists('account', $json))
        {
            $tradingAccount = new TradingAccount();
            $tradingAccount->fill(( array )$json->account);
            return $tradingAccount;
        }
        else
        {
            //This is an error message that can be access like this:
            //echo $json->code." ".$json->error;
            throw new \Exception( $json->code." ".$json->error );
        }
    }

    public function createAccount( $account_data ){
        $url="https://www.trade-copier.com/webservice/v4/account/addAccount.php";
        $headers =  array(
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: '. $this->username,
            'Auth-Token: '. $this->auth_token,
        );

        // Open connection
        $ch = curl_init();

        // Setting the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Adding data to POST
        $account = $account_data;
        if(isset($account['groupid'])){
            $account['group'] = $account['groupid'];
            unset($account['groupid']);
        }

        //echo http_build_query($account);

        curl_setopt($ch, CURLOPT_POST, count($account));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($account));

        // Execute request
        $json = json_decode(curl_exec($ch));

        // Close connection
        curl_close($ch);

        if(array_key_exists('account', $json))
        {
            $tradingAccount = new TradingAccount();
            $tradingAccount->fill(( array )$json->account);
            return $tradingAccount;
        }
        else
        {
            //This is an error message that can be access like this:
            //echo $json->code." ".$json->error;
            throw new \Exception( $json->code." ".$json->error );
        }
    }

    public function updateAccount( $account_data ){
        $url="https://www.trade-copier.com/webservice/v4/account/updateAccount.php";
        $headers =  array(
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: '. $this->username,
            'Auth-Token: '. $this->auth_token,
        );

        // Open connection
        $ch = curl_init();

        // Setting the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //If needed, we update the Risk Factor's group, you can get the existing groups with the "getGroups.php" webservice
        $group = '2Lots';//Group's name
        //$group = '';//No group

        //If needed, we update the subscription for the account, you can get available subscriptions with the "getSubscriptions.php" webservice
        $subscription = 'auto';//Automaticaly select an available subscription
        //$subscription = ['name' => 'Free v1', 'expiration_date' => '0000-00-00 00:00:00'];
        //$subscription = "";//NULL string to remove the subscription

        // Account data to be updated
        $account = $account_data;
        if(isset($account['subscription']))
            unset($account['subscription']);

        if(isset($account['groupid'])){
            $account['group'] = $account['groupid'];
            unset($account['groupid']);
        }


        curl_setopt($ch, CURLOPT_POST, count($account));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($account));

        // Execute request
        $json = json_decode(curl_exec($ch));

        // Close connection
        curl_close($ch);

        //We access the updated account data
        if(array_key_exists('account', $json))
        {
            $tradingAccount = new TradingAccount();
            $tradingAccount->fill(( array )$json->account);
            return $tradingAccount;
        }
        else
        {
            //This is an error message that can be access like this:
            //echo $json->code." ".$json->error;
            throw new \Exception( $json->code." ".$json->error );
        }
    }

    /**
    * Sends a HTTP request to the API server and returns the response.
    *
    * @param string $method The HTTP method ( GET, POST, PUT, DELETE, etc. ).
    * @param string $endpoint The API endpoint to send the request to.
    * @param array $data An associative array of data to send with the request ( optional ).
    *
    * @return mixed The API response parsed from JSON into an associative array, or null on error.
    */

    private function sendRequest( string $method, string $endpoint, array $data = [] ){
        $headers =  array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Auth-Username' =>  $this->username,
            'Auth-Token' =>  $this->auth_token,
        );
        // Set up the HTTP client
        $client = new \GuzzleHttp\Client( [
            'base_uri' => $this->base_url,
            'timeout' => "30",
            'headers' => $headers,
        ] );

        try {
            // Send the HTTP request and get the response
            $response = $client->request( $method, $endpoint, [
                'json' => $data,
            ] );

            // Parse the JSON response into an associative array
            $response_data = json_decode( $response->getBody()->getContents(), true );

            // Check for any errors in the API response
            if ( isset( $response_data[ 'error' ] ) ) {
                throw new \Exception( $response_data[ 'error' ], $response_data[ 'code' ] );
            }

            // Return the API response as an associative array
            return ['status' => $response->getStatusCode(), 'body' => $response_data];

        } catch ( \GuzzleHttp\Exception\RequestException $e ) {
            // Handle any HTTP errors that occur
            $response = $e->getResponse();
            if ( $response !== null ) {
                $response_data = json_decode( $response->getBody()->getContents(), true );
                $error_message = $response_data[ 'error' ] ?? $e->getMessage();
                $error_code = $response_data[ 'code' ] ?? $e->getCode();
            } else {
                $error_message = $e->getMessage();
                $error_code = $e->getCode();
            }

            throw new \Exception( $error_message, $error_code );

        } catch ( \Exception $e ) {
            // Handle any other errors that occur
            throw new \Exception( $e->getMessage(), $e->getCode() );
        }
    }
}
