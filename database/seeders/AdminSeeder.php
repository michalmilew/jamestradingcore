<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert a new admin record
        DB::table('admins')->insert([
            'name' => 'Yassir Farihi',
            'email' => 'yassir@example.com', // Replace with the actual email address
            'password' => Hash::make('password'), // Replace 'password' with a secure password
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        echo "Admin user 'Yassir Farihi' created successfully in the admins table.\n";
    }
}
