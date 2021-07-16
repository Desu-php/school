<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\CategoryInteresting;
use App\Models\Course;
use App\Models\CourseChat;
use App\Models\CourseModule;
use App\Models\CourseTariff;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Interesting;
use App\Models\Lesson;
use App\Models\LessonBlock;
use App\Models\News;
use App\Models\OtherMaterial;
use App\Models\Review;
use App\Models\Task;
use App\Models\TaskAnswer;
use App\Models\TaskCategorize;
use App\Models\TaskCategorizeCategory;
use App\Models\TaskCategorizeCategoryItem;
use App\Models\TaskComposeText;
use App\Models\TaskDeckCardAnswer;
use App\Models\TaskDeckCardQuestion;
use App\Models\TaskFieldOfDream;
use App\Models\TaskGallow;
use App\Models\TaskMissingWord;
use App\Models\TaskPickUpTranslation;
use App\Models\TaskQuestion;
use App\Models\TaskRememberFind;
use App\Models\TaskRememberFindWord;
use App\Models\TaskTranslation;
use App\Models\TaskWheelFortuneAnswer;
use App\Models\TaskWheelFortuneQuestion;
use App\Models\TeachingAudio;
use App\Models\TeachingBook;
use App\Models\TeachingVideo;
use App\Models\User;
use App\Models\Video;
use Database\Factories\TaskFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminRoleSeeder::class,
            AdminSeeder::class,
            LanguageSeeder::class,
            TeachingLanguageSeeder::class,
            DynamicPageSeeder::class,
            DynamicPageTextSeeder::class,
            ProjectSettingSeeder::class,
            CurrencySeeder::class,
            UserSeeder::class,
            SupportTicketCategorySeeder::class,
            CourseTariffSeeder::class,
            PaymentMethodSeeder::class,
            TaskTypeSeeder::class,
            CourseLevelSeeder::class,
            EmailTemplateSeeder::class,
            EmailTemplateTextSeeder::class,
        ]);

        User::factory()->count(50)->create();
        FaqCategory::factory()->count(3)->create();
        Faq::factory()->count(20)->create();
        $faq_category = FaqCategory::all();
        Faq::all()->each(function ($faq) use ($faq_category) {
            $faq->categories()->attach(
                $faq_category->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        News::factory()->count(50)->create();
        Review::factory()->count(200)->create();
        Video::factory()->count(50)->create();
        CategoryInteresting::factory()->count(3)->create();
        Interesting::factory()->count(80)->create();

        //Course
        Announcement::factory()->count(80)->create();
        Course::factory()->count(80)->create();
        CourseModule::factory()->count(200)->create();
        Lesson::factory()->count(500)->create();
        LessonBlock::factory()->count(500)->create();
        Task::factory()->count(1200)->create();
        CourseChat::factory()->count(80)->create();

        //Tasks
        TaskMissingWord::factory()->count(99)->create();
        TaskPickUpTranslation::factory()->count(101)->create();
        TaskTranslation::factory()->count(401)->create();
        TaskCategorize::factory()->count(101)->create();
        TaskCategorizeCategory::factory()->count(301)->create();
        TaskCategorizeCategoryItem::factory()->count(900)->create();
        TaskComposeText::factory()->count(101)->create();
        TaskFieldOfDream::factory()->count(100)->create();
        TaskGallow::factory()->count(100)->create();
        TaskDeckCardQuestion::factory()->count(1000)->create();
        TaskDeckCardAnswer::factory()->count(4000)->create();
        TaskWheelFortuneQuestion::factory()->count(800)->create();
        TaskWheelFortuneAnswer::factory()->count(3200)->create();
        TaskRememberFind::factory()->count(102)->create();
        TaskRememberFindWord::factory()->count(404)->create();
        TaskQuestion::factory()->count(800)->create();
        TaskAnswer::factory()->count(3200)->create();



        TeachingBook::factory()->count(80)->create();
        TeachingVideo::factory()->count(80)->create();
        TeachingAudio::factory()->count(80)->create();
        OtherMaterial::factory()->count(80)->create();

        $course_tariff = CourseTariff::all();
        Course::all()->each(function ($course) use ($course_tariff) {
            $course->tariffs()->attach(
                $course_tariff->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
