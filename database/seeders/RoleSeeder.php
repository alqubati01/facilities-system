<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Role::create([
        'name' => 'admin',
        'slug' => 'مدير النظام'
      ]);

      Role::create([
        'name' => 'area-manager',
        'slug' => 'مدير منطقة'
      ]);

      Role::create([
        'name' => 'supervisor',
        'slug' => 'مشرف'
      ]);

      Role::create([
        'name' => 'representative',
        'slug' => 'مندوب'
      ]);
    }
}
