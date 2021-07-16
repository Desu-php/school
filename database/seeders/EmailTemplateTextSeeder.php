<?php

namespace Database\Seeders;

use App\Models\EmailTemplateText;
use Illuminate\Database\Seeder;

class EmailTemplateTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailTemplateText::insert([
            [
                'key'               => 'greeting',
                'value'             => '{"ru":"Hello!"}',
                'email_template_id' => 1,
            ],
            [
                'key'               => 'intro_texts',
                'value'             => '{"ru":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s"}',
                'email_template_id' => 1,
            ],
            [
                'key'               => 'action_text',
                'value'             => '{"ru":"Verify"}',
                'email_template_id' => 1,
            ],
            [
                'key'               => 'outro_texts',
                'value'             => '{"ru":"Lorem Ipsum is simply dummy text of the printing and typesetting industry!"}',
                'email_template_id' => 1,
            ],
            [
                'key'               => 'regards',
                'value'             => '{"ru":"Regards."}',
                'email_template_id' => 1,
            ],
            [
                'key'               => 'app_name',
                'value'             =>'{"ru":"L2G Team."}',
                'email_template_id' => 1,
            ],
            [
                'key'               => 'footer_texts',
                'value'             => '{"ru":"If you’re having trouble clicking the Verify button, copy and paste the URL below into your web browser:"}',
                'email_template_id' => 1,
            ],
            [
                'key'               => 'name',
                'value'             => '{"ru":"Имя"}',
                'email_template_id' => 2,
            ],
            [
                'key'               => 'email',
                'value'             => '{"ru":"E-mail"}',
                'email_template_id' => 2,
            ],
            [
                'key'               => 'phone_number',
                'value'             => '{"ru":"Телефон"}',
                'email_template_id' => 2,
            ],
            [
                'key'               => 'message',
                'value'             => '{"ru":"Вопрос"}',
                'email_template_id' => 2,
            ],
        ]);
    }
}
