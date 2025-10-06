<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingApiOrder extends TradingApi
{
    use HasFactory;
    public function getOrders(){

        $url="https://www.trade-copier.com/webservice/v4/order/getOrders.php";
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

        //    $filter = ['master_id' => '58989','account_id' => '72496', 'from' => '2019-03-26 09:48:58', 'to' => '2019-04-10 20:00:46'];
        //$filter = ['from' => '2019-03-26 09:48:58', 'to' => '2019-04-10 20:00:46'];
        //$filter = ['from' => '2019-03-26 09:48:58'];
        //$filter = ['to' => '2019-03-27 20:00:46'];
        //$filter = ['error_only' => '0', 'limit' => '10'];
        //$filter = [];

        //curl_setopt($ch, CURLOPT_POST, count($filter));
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($filter));

        // Execute request
        $response = curl_exec($ch);
        if($response === false) {
            // Log the error message and error code
            $error_message = curl_error($ch);
            $error_code = curl_errno($ch);
            throw new \Exception("cURL error ({$error_code}): {$error_message}");
        }
        //dd($response);
        $json = json_decode($response);

        // Close connection
        curl_close($ch);

        $orders = [];
        if(array_key_exists('orders', $json))
        {
            foreach ($json->orders as $order ) {
                $tradingOrder = new TradingOrder();
                $tradingOrder->fill(( array )$order);
                $orders[] = $tradingOrder;
            }
            return $orders;
        }else{
            throw new \Exception($json->code . " : " . $json->error );
        }
    }
}
