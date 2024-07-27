<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::create([
            'name' => 'يمني',
            'code' => 'YER',
            'symbol' => 'ر.ي',
            'is_active' => 1,
        ]);

        Currency::create([
            'name' => 'دولار',
            'code' => 'USD',
            'symbol' => '$',
            'is_active' => 1,
        ]);

        Currency::create([
            'name' => 'سعودي',
            'code' => 'SAR',
            'symbol' => 'ر.س',
            'is_active' => 1,
        ]);
    }
}
