<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Permission::create([
        'name' => 'manage dashboard'
      ]);

      Permission::create([
        'name' => 'show facility'
      ]);

      Permission::create([
        'name' => 'create facility'
      ]);

      Permission::create([
        'name' => 'edit facility'
      ]);

      Permission::create([
        'name' => 'delete facility'
      ]);

      Permission::create([
        'name' => 'edit facility status'
      ]);

      Permission::create([
        'name' => 'export facilities'
      ]);

      Permission::create([
        'name' => 'show reports'
      ]);

      Permission::create([
        'name' => 'manage settings'
      ]);

      Permission::create([
        'name' => 'manage users'
      ]);
    }
}
