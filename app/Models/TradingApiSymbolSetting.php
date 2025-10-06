<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingApiSymbolSetting extends Model
{
    use HasFactory;

    public function getSymbolSettings(){

        $url="https://www.trade-copier.com/webservice/v4/settings/getSymbolSettings.php";
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


        // slave_id, group_id, master_id, symbol and/or symbol_master filters
        $filter = [
            //'slave_id' => '29524',//Optional => Not defined to show the settings for all your Slaves
            //'group_id' => '538',//Optional =>  Not defined to show the settings for all your Groups
            //'master_id' => '33934',//Optional => Not defined to show the settings for all your Masters or emtpy String for settings that apply to "all your Masters"
            //'symbol' => 'XAUUSD',//Optional => Not defined for all
            //'symbol_master' => 'GOLD',//Optional => Not defined for all
        ];
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

        $settings = [];
        if(array_key_exists('symbolsettingslist', $json))
        {
            foreach ($json->symbolsettingslist as $item ) {
                $tradingSymbolSetting = new TradingSymbolSetting();
                $tradingSymbolSetting->fill(( array )$item);
                $settings[] = $tradingSymbolSetting;
            }
            return $settings;
        }else{
            throw new \Exception($json->code . " : " . $json->error );
        }
    }
}
