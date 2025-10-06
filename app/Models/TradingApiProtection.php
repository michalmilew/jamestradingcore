<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingApiProtection extends Model
{
    use HasFactory;
    public function getGroups(){

        $url="https://www.trade-copier.com/webservice/v4/protection/getGlobalProtection.php";
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

        // $filter = ['slave_id' => '29524'];//Optional => Not defined to show the settings for all your Slaves

        // curl_setopt($ch, CURLOPT_POST, count($filter));
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($filter));


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

        $protections = [];
        if(array_key_exists('globalprotections', $json))
        {
            foreach ($json->globalprotections as $item ) {
                $tradingProtection = new TradingProtection();
                $tradingProtection->fill(( array )$item);
                $protections[] = $tradingProtection;
            }
            return $protections;
        }else{
            throw new \Exception($json->code . " : " . $json->error );
        }
    }
}
