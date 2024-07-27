<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'طبيب',
            'is_active' => 1
        ]);

        Category::create([
            'name' => 'ندوات',
            'is_active' => 1
        ]);

        Category::create([
            'name' => 'عملاء',
            'is_active' => 1
        ]);

        Category::create([
          'name' => 'صيدليات',
          'is_active' => 1
        ]);

        Category::create([
          'name' => 'مراكز',
          'is_active' => 1
        ]);

        Category::create([
            'name' => 'أخرى',
            'is_active' => 1
        ]);
    }
}
