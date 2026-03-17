<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@sambayanan.com',
            'password'          => Hash::make('admin1234'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        DB::table('settings')->insert([
            'key'        => 'emergency_mode',
            'value'      => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }   

}

