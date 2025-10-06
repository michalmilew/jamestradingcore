<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetatraderFieldsToUserAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('user_accounts', 'server')) {
                $table->string('server')->nullable()->after('login');
            }
            if (!Schema::hasColumn('user_accounts', 'platform_type')) {
                $table->enum('platform_type', ['MT4', 'MT5'])->default('MT4')->after('server');
            }
            if (!Schema::hasColumn('user_accounts', 'balance')) {
                $table->decimal('balance', 15, 2)->nullable()->after('disconnected_at');
            }
            if (!Schema::hasColumn('user_accounts', 'equity')) {
                $table->decimal('equity', 15, 2)->nullable()->after('balance');
            }
            if (!Schema::hasColumn('user_accounts', 'template_id')) {
                $table->unsignedBigInteger('template_id')->nullable()->after('equity');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'server', 
                'platform_type',
                'balance',
                'equity',
                'template_id'
            ]);
        });
    }
}
