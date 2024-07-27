<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Status::create([
        'name' => 'جديد',
        'is_active' => 1,
      ]);

      Status::create([
        'name' => 'قيد المعاملة',
        'is_active' => 1,
      ]);

      Status::create([
        'name' => 'تم الإرسال',
        'is_active' => 1,
      ]);

      Status::create([
        'name' => 'ملغي',
        'is_active' => 1,
      ]);
    }
}
