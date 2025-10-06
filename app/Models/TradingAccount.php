<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
{
    protected $table = 'trading_accounts';

    protected $fillable = [
        'account_id',
        'account_id2',
        'type',
        'name',
        'broker',
        'login',
        'account',
        'password',
        'server',
        'environment',
        'status',
        'state',
        'groupid',
        'subscription_key',
        'pending',
        'stop_loss',
        'take_profit',
        'alert_email',
        'alert_sms',
        'balance',
        'equity',
        'free_margin',
        'credit',
        'ccy',
        'mode',
        'access_token',
        'refresh_token',
        'expiry_token',
        'subscription_name',
        'expiration',
        'lastUpdate',
        'open_trades',
        'account_key',
    ];

    protected $casts = [
        //'account_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        //'groupid' => 'integer',
        'pending' => 'integer',
        'stop_loss' => 'float',
        'take_profit' => 'float',
        'balance' => 'float',
        'equity' => 'float',
        'free_margin' => 'float',
        'credit' => 'float',
        'mode' => 'integer',
        'expiration' => 'datetime',
        //'lastUpdate' => 'datetime',
        'open_trades' => 'integer',
    ];

    public function getClosedPositions(){

        $url="https://www.trade-copier.com/webservice/v4/position/getClosedPositions.php";
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

        $filter = ['account_id' => $this->account_id];
        //$filter = ['from' => '2019-03-26 09:48:58', 'to' => '2019-04-10 20:00:46'];
        //$filter = ['from' => '2019-03-26 09:48:58'];
        //$filter = ['to' => '2019-03-27 20:00:46'];
        //$filter = ['error_only' => '0', 'limit' => '10'];
        //$filter = [];

        curl_setopt($ch, CURLOPT_POST, count($filter));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($filter));

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

        $positions = [];
        if(array_key_exists('data', $json))
        {
            foreach ($json->data as $position ) {
                $tradingPosition = new TradingPosition();
                $tradingPosition->fill(( array )$positions);
                $positions[] = $tradingPosition;
            }
            return $positions;
        }else{
            throw new \Exception($json->code . " : " . $json->error );
        }
    }
    public function getClosedPosition(){
        try {
            $useraccount = UserAccount::where('account_id', $this->account_id)->first();
            $position = $useraccount->lots ?? 0;
            return $position;
        } catch (\Throwable $th) {
            return 0;
        }
    }


    public function userAccounts()
    {
        return $this->hasMany(UserAccount::class ,'account_id' , 'account_id');
    }

}


