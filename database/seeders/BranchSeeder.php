<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'صنعاء',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'القاعدة',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'الحديدة',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'إب',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'ذمار',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'عمران',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'تعز',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'عدن',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'المكلا',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'سيئون',
            'is_active' => 1
        ]);

        Branch::create([
            'name' => 'مأرب',
            'is_active' => 1
        ]);
    }
}
