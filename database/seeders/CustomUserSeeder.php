<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CustomUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       for ($i = 0; $i < 100000; $i++) {
            DB::table('custom_users')->insert([
                'name' => 'User '.$i,
                'email' => "user$i@test.com",
                'country' => ['MA','FR','US','ES'][rand(0,3)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
