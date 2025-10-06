<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCleanupTable extends Migration
{
    public function up()
    {
        Schema::create('cleanup', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_balance', 10, 2)->default(0.01);
            $table->decimal('max_balance', 10, 2)->default(10.00);
            $table->integer('cleanup_period')->default(7);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cleanup');
    }
}
