<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instructional_videos', function (Blueprint $table) {
            $table->id();
            $table->string('language', 2);
            $table->text('url');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('language');
        });
    }

    public function down()
    {
        Schema::dropIfExists('instructional_videos');
    }
}; 