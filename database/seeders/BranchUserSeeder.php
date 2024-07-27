<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('branch_user')->insert([
        ['branch_id' => 1, 'user_id' => 1],
        ['branch_id' => 2, 'user_id' => 1],
        ['branch_id' => 3, 'user_id' => 1],
        ['branch_id' => 4, 'user_id' => 1],
        ['branch_id' => 5, 'user_id' => 1],
        ['branch_id' => 6, 'user_id' => 1],
        ['branch_id' => 7, 'user_id' => 1],
        ['branch_id' => 8, 'user_id' => 1],
        ['branch_id' => 9, 'user_id' => 1],
        ['branch_id' => 10, 'user_id' => 1],
        ['branch_id' => 11, 'user_id' => 1],
      ]);
    }
}
