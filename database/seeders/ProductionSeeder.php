<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insertOrIgnore([
            'key'   => 'emergency_mode',
            'value' => '0'
        ]);

        \App\Models\User::firstOrCreate(
            ['email' => 'admin@pagkainang.com'],
            [
                'name'                => 'Admin',
                'password'            => bcrypt('admin1234'),
                'role'                => 'admin',
                'verification_status' => 'approved',
            ]
        );
    }
}