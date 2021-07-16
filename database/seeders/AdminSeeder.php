<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Admin::insert([
        [
          'name' => 'Admin',
          'email' => 'admin@mail.ru',
          'role_id' => 1,
          'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'Super Admin',
          'email' => 'super_admin@mail.ru',
          'role_id' => 2,
          'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'Content Manager',
          'email' => 'content_manager@mail.ru',
          'role_id' => 3,
          'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'Support',
          'email' => 'support@mail.ru',
          'role_id' => 4,
          'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
          'created_at' => now(),
          'updated_at' => now(),
        ],
      ]);
    }
}
