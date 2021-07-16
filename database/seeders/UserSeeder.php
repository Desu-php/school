<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run()
    {
        User::insert([
            [
                'first_name' => 'New',
                'last_name' => 'User',
                'email' => 'new_user@mail.ru',
                'login' => 'new_user',
                'phone' => '+777777777777',
                'birthday' => new Carbon("1995/12/12"),
                'gender' => 'male',
                'avatar' => null,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
