<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        User::factory(2)->create();
        User::factory()->newUser()->create();
//        User::factory()->newUser2()->create();
//        User::factory()->newUser3()->create();
    }
}
