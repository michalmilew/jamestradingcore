<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingApiSlaveGroup extends TradingApi
{
    use HasFactory;

    public function getSlaveGroupSettings(){

        $url="https://www.trade-copier.com/webservice/v4/settings/getSettings.php";
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


        // slave_id and master_id filter
        $filter = [
            //'slave_id' => '29516',//Optional => Not defined to show the settings for all your Slaves
            'group_id' => '3626',//Optional =>  Not defined to show the settings for all your Groups
            //'master_id' => '',//Optional => Not defined to show the settings for all your Masters or emtpy String for settings that apply to "all your Masters"
        ];

        //   curl_setopt($ch, CURLOPT_POST, count($filter));
        //   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($filter));

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

        $settings = [];
        if(array_key_exists('settingslist', $json))
        {
            foreach ($json->settingslist as $item ) {
                $tradingSlaveGroup = new TradingSlaveGroup();
                $tradingSlaveGroup->fill(( array )$item);
                $settings[] = $tradingSlaveGroup;
            }
            return $settings;
        }else{
            throw new \Exception($json->code . " : " . $json->error );
        }
    }

}
