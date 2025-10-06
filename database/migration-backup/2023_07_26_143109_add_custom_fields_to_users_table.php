<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('notes')->nullable();
            $table->string('ig_user')->nullable();
            $table->string('id_broker')->nullable();
            $table->string('ftd')->nullable();
            $table->string('lots')->nullable();
            $table->enum('paid', ['Yes', 'No'])->nullable();
            $table->enum('broker', ['IronFX', 'T4Trade'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notes', 'ig_user', 'id_broker', 'ftd', 'lots', 'paid', 'broker']);
        });
    }
}
