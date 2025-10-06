<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_accounts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('account_id')->unique();
            $table->string('account_id2')->nullable();
            $table->integer('type')->nullable();  // Type as integer
            $table->string('name')->nullable();
            $table->string('broker')->nullable();
            $table->string('login')->nullable();
            $table->string('account')->nullable();
            $table->string('password')->nullable();
            $table->string('server')->nullable();
            $table->string('environment')->nullable();
            $table->string('status')->nullable();
            $table->string('state')->nullable();
            $table->integer('groupid')->nullable(); // If 'groupid' needs to be an integer
            $table->string('subscription_key')->nullable();
            $table->integer('pending')->nullable();  // Pending as integer
            $table->float('stop_loss')->nullable();  // Stop loss as float
            $table->float('take_profit')->nullable(); // Take profit as float
            $table->string('alert_email')->nullable();
            $table->string('alert_sms')->nullable();
            $table->float('balance')->nullable();  // Balance as float
            $table->float('equity')->nullable();  // Equity as float
            $table->float('free_margin')->nullable(); // Free margin as float
            $table->float('credit')->nullable(); // Credit as float
            $table->string('ccy')->nullable();  // Currency abbreviation
            $table->integer('mode')->nullable();  // Mode as integer
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestamp('expiry_token')->nullable(); // Token expiry time
            $table->string('subscription_name')->nullable();
            $table->timestamp('expiration')->nullable(); // Expiration as datetime
            $table->timestamp('lastUpdate')->nullable();  // Last update as datetime
            $table->integer('open_trades')->nullable();  // Open trades as integer
            $table->string('account_key')->nullable();  // Account key

            $table->timestamps();  // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trading_accounts');
    }
}
