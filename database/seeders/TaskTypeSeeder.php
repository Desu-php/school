<?php

namespace Database\Seeders;

use App\Models\TaskType;
use Illuminate\Database\Seeder;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      TaskType::insert([
          [
              'name' => '{"ru": "Задание - тест"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - пропущенные буквы/слова"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - подобрать перевод"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - распределить по категориям"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - составить текст"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - проговорить текст"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - кроссворд"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - поле чудес"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - виселица"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - колода карт"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - колесо фортуны"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - запомни и найди"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
          [
              'name' => '{"ru": "Задание - соберите предложение / или текст"}',
              'description' => '{"ru": "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged."}',
              'image' => 'tasks.jpg'
          ],
      ]);
    }
}
