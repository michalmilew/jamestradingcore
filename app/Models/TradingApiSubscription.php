<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingApiSubscription extends TradingApi
{
    public function getSubscriptions(){

        $url="https://www.trade-copier.com/webservice/v4/subscription/getSubscriptions_v2.php";
        $headers =  array(
            'Content-Type: application/x-www-form-urlencoded',
            'Auth-Username: '. $this->username,
            'Auth-Token: '. $this->auth_token,
        );
        // Open connection
        $ch = curl_init();

        // Setting the options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute request
        $response = curl_exec($ch);
        if($response === false ) {
            // Log the error message and error code
            $error_message = curl_error($ch);
            $error_code = curl_errno($ch);
            throw new \Exception("cURL error ({$error_code}): {$error_message}");
        }
        //dd($response);
        $json = json_decode($response);

        // Close connection
        curl_close($ch);

        $subscriptions = [];
        if(array_key_exists('subscriptions', $json))
        {
            foreach ($json->subscriptions as $subscription ) {
                $tradingSubscription = new TradingSubscription();
                $tradingSubscription->fill(( array )$subscription);
                $subscriptions[] = $tradingSubscription;
            }
            return $subscriptions;
        }else{
            throw new \Exception($json->code . " : " . $json->error );
        }
    }
}
