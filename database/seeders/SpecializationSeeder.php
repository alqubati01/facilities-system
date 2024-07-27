<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specialization::create([
            'name' => 'قلب',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'باطنة',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'سكر',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'جهاز تنفسي',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'نفسية',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'جراحة عامة',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'عظام',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'أعصاب',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'أطفال',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'انف واذن وحنجرة',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'نساء وولادة',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'جلدية',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'مسالك بولية',
            'is_active' => 1
        ]);
        Specialization::create([
            'name' => 'اسنان',
            'is_active' => 1
        ]);

        Specialization::create([
            'name' => 'أخرى',
            'is_active' => 1
        ]);
    }
}
