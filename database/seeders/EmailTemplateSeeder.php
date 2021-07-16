<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailTemplate::insert([
            [
                'name' => 'Verify Email Template',
                'blade_name' => 'verify_email',
            ],
            [
                'name' => 'Application',
                'blade_name' => 'application',
            ]
        ]);
    }
}
