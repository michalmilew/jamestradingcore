<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationIntervalsTable extends Migration
{
    public function up()
    {
        Schema::create('notification_intervals', function (Blueprint $table) {
            $table->id();
            $table->string('notification_type');
            $table->enum('interval', ['Daily', 'Weekly', 'Monthly', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_intervals');
    }
}
