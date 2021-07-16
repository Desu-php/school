<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      AdminRole::insert([
        [
          'name' => 'admin',
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'super_admin',
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'content_manager',
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'support',
          'created_at' => now(),
          'updated_at' => now(),
        ]
      ]);
    }
}
