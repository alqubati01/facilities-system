<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create([
            'name' => 'ترويج المكتب العلمي',
            'is_active' => 1
        ]);

        Unit::create([
            'name' => 'ترويج المنتجات',
            'is_active' => 1
        ]);

        Unit::create([
            'name' => 'ترويج العملاء',
            'is_active' => 1
        ]);
    }
}
