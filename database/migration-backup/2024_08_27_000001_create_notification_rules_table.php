<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationRulesTable extends Migration
{
    public function up()
    {
        Schema::create('notification_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the rule
            $table->string('type'); // Type of notification: 'profit', 'invite', 'risk'
            $table->decimal('min_value', 10, 2)->nullable(); // Min PnL or equivalent value
            $table->decimal('max_value', 10, 2)->nullable(); // Max PnL or equivalent value
            $table->unsignedInteger('vip_level')->nullable(); // VIP level for filtering
            $table->string('notification_class'); // The notification class to use
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_rules');
    }
}
