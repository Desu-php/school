<?php

use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\Web\SubscribeNewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DynamicPageTextController;
use App\Http\Controllers\DynamicPageController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\InterestingController;
use App\Http\Controllers\TeachingLanguageController;
use App\Http\Controllers\ContactsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('test-webhook', function (Request $request) {
    \Illuminate\Support\Facades\Log::info(json_encode($request->all()));
});

// Web
Route::group(['prefix' => 'web'], function () {
    Route::post('login-fb', [UserController::class, 'loginFb']);
    Route::post('login-vk', [UserController::class, 'loginVk']);
    Route::post('login-google', [UserController::class, 'loginGoogle']);

//    Route::get('login-test', [UserController::class, 'test']);

    Route::post('register', [UserController::class, 'register']);
    Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('password/reset', [UserController::class, 'passwordReset'])->name('password.reset');

    Route::get('language', [LanguageController::class, 'index']);
    Route::get('search', [\App\Http\Controllers\web\SearchController::class, 'index']);
    Route::get('language/{code}', [LanguageController::class, 'getLocalizationJson']);
    Route::get('faq-category', [\App\Http\Controllers\Web\FaqCategoryController::class, 'index']);
    Route::get('faq', [FaqController::class, 'index']);
    Route::get('news', [\App\Http\Controllers\Web\NewsController::class, 'index']);
    Route::get('category-interesting', [\App\Http\Controllers\Web\CategoryInterestingController::class, 'index']);
    Route::get('best-interestings', [\App\Http\Controllers\Web\InterestingController::class, 'bestInterestings']);
    Route::get('interestings', [\App\Http\Controllers\Web\InterestingController::class, 'index']);
    Route::get('interestings/{id}', [\App\Http\Controllers\Web\InterestingController::class, 'show']);
    Route::get('video', [\App\Http\Controllers\Web\VideoController::class, 'index']);
    Route::get('teaching-languages', [\App\Http\Controllers\Web\TeachingLanguageController::class, 'index']);
    Route::get('news/{id}', [\App\Http\Controllers\Web\NewsController::class, 'show']);
    Route::get('dynamic-page/{id}', [\App\Http\Controllers\Web\DynamicPageController::class, 'show']);
    Route::get('dynamic-page-by-key/{key}', [\App\Http\Controllers\Web\DynamicPageController::class, 'getPageTextByKey']);
    Route::post('contact', [\App\Http\Controllers\Web\ContactsController::class, 'store']);
    Route::post('application', [\App\Http\Controllers\Web\ApplicationController::class, 'store']);
    Route::get('review', [\App\Http\Controllers\Web\ReviewController::class, 'index']);
    Route::get('project-setting', [\App\Http\Controllers\Web\ProjectSettingController::class, 'index']);
    Route::get('course', [\App\Http\Controllers\Web\CourseController::class, 'index']);
    Route::get('available-course', [\App\Http\Controllers\Web\CourseController::class, 'availableCourse']);
    Route::get('course-teaching-language/{id}', [\App\Http\Controllers\Web\CourseController::class, 'getCourseTeachingLanguage']);
    Route::get('course-buy/{id}/module', [\App\Http\Controllers\Web\CourseController::class, 'showBoughtCourseModule']);
    Route::get('course-video/list', [\App\Http\Controllers\Web\CourseController::class, 'getCourseVideoMaterials']);
    Route::get('course/bought', [\App\Http\Controllers\Web\CourseController::class, 'boughtCourses']);
    Route::get('course/subscription', [\App\Http\Controllers\Web\CourseController::class, 'subscriptionCourses']);
    Route::post('course/subscription-update-expiry', [\App\Http\Controllers\Web\CourseController::class, 'subscriptionUpdateExpiry']);
    Route::get('lesson/{id}', [\App\Http\Controllers\Web\LessonController::class, 'show']);
    Route::get('lessons', [\App\Http\Controllers\Web\LessonController::class, 'index']);
    Route::get('free-lesson/{id}', [\App\Http\Controllers\Web\LessonController::class, 'getFreeLesson']);
    Route::get('payment-method', [\App\Http\Controllers\Web\PaymentMethodController::class, 'index']);
    Route::get('menu-courses', [\App\Http\Controllers\Web\CourseController::class, 'menuCourses']);
    Route::get('course/{id}', [\App\Http\Controllers\Web\CourseController::class, 'show']);
    Route::get('currency', [\App\Http\Controllers\Web\CurrencyController::class, 'index']);
    Route::get('email/verify/{id}', [\App\Http\Controllers\VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('email/resend', [\App\Http\Controllers\VerificationController::class, 'resend'])->name('verification.resend');
    Route::group(['middleware' => ['jwt.verify:api']], function () {
        Route::get('verify-email', [UserController::class, 'sendVerifyEmail']);
    });

    Route::post('login', [UserController::class, 'login']);

    Route::group(['middleware' => ['jwt.verify:api']], function () {
        Route::get('logout', [UserController::class, 'logout']);
        Route::get('me', [UserController::class, 'userProfile']);
        Route::post('review', [\App\Http\Controllers\Web\ReviewController::class, 'store']);
        Route::put('user', [UserController::class, 'update']);
        Route::get('notifications', [UserController::class, 'notifications']);
        Route::get('chat-notifications', [UserController::class, 'chatNotifications']);
        Route::get('unread-notifications-count', [UserController::class, 'unreadNotificationsCount']);
        Route::get('mark-as-read-notifications', [UserController::class, 'markAsReadNotifications']);
        Route::get('mark-notification-as-read/{id}', [UserController::class, 'markNotificationAsRead']);

        Route::group(['prefix' => 'user'], function () {
            Route::put('password', [UserController::class, 'updatePassword']);
            Route::post('avatar', [UserController::class, 'updateAvatar']);
            Route::delete('avatar', [UserController::class, 'deleteAvatar']);
        });

        Route::group(['prefix' => 'support-ticket'], function () {
            Route::get('/{id}', [\App\Http\Controllers\Web\SupportTicketController::class, 'show']);
            Route::get('/', [\App\Http\Controllers\Web\SupportTicketController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Web\SupportTicketController::class, 'store']);
            Route::post('message/{id}', [\App\Http\Controllers\Web\SupportTicketController::class, 'sendMessage']);
            Route::get('/mark-as-read/{id}', [\App\Http\Controllers\Web\SupportTicketController::class, 'markAsRead']);
        });

        Route::get('support-ticket-category', [\App\Http\Controllers\Web\SupportTicketCategoryController::class, 'index']);
        Route::resource('note', \App\Http\Controllers\Web\NoteController::class);

        Route::get('my-review', [\App\Http\Controllers\Web\ReviewController::class, 'myReviews']);
        Route::get('teaching-book', [\App\Http\Controllers\Web\TeachingBookController::class, 'index']);
        Route::get('teaching-video', [\App\Http\Controllers\Web\TeachingVideoController::class, 'index']);
        Route::get('teaching-video/{id}', [\App\Http\Controllers\Web\TeachingVideoController::class, 'show']);

        Route::get('teaching-audio', [\App\Http\Controllers\Web\TeachingAudioController::class, 'index']);
        Route::get('other-material', [\App\Http\Controllers\Web\OtherMaterialController::class, 'index']);
        Route::get('other-material/{id}', [\App\Http\Controllers\Web\OtherMaterialController::class, 'show']);
        Route::get('other-material/{id}/download-files', [\App\Http\Controllers\Web\OtherMaterialController::class, 'downloadFiles']);

        Route::post('buy-course', [\App\Http\Controllers\Web\PaymentController::class, 'buyCourse']);
        Route::get('payment-history', [\App\Http\Controllers\Web\PaymentController::class, 'paymentHistory']);

        Route::get('course-module/{id}', [\App\Http\Controllers\Web\CourseModuleController::class, 'courseModule']);

        Route::get('course-chat', [\App\Http\Controllers\Web\CourseChatController::class, 'index']);
        Route::post('course-chat', [\App\Http\Controllers\Web\CourseChatController::class, 'store']);
        Route::post('course-chat-leave', [\App\Http\Controllers\Web\CourseChatController::class, 'chatLeave']);
        Route::get('course-chat/{id}/accept-invitation', [\App\Http\Controllers\Web\CourseChatController::class, 'acceptInvitation']);
        Route::get('course-chat/{id}/decline-invitation', [\App\Http\Controllers\Web\CourseChatController::class, 'declineInvitation']);

        Route::get('course-chat/{id}', [\App\Http\Controllers\Web\CourseChatController::class, 'show']);
        Route::post('course-chat/{id}', [\App\Http\Controllers\Web\CourseChatController::class, 'sendMessage']);

        Route::post('chat-course-expert', [\App\Http\Controllers\Web\ChatExpertController::class, 'sendMessage']);
        Route::group(['prefix' => 'course-expert'], function () {
            Route::post('call-expert', [\App\Http\Controllers\Web\ChatExpertController::class, 'callExpert']);
            Route::post('accept-call', [\App\Http\Controllers\Web\ChatExpertController::class, 'acceptCall']);
        });

        Route::get('chat-course-expert/{course_id}', [\App\Http\Controllers\Web\ChatExpertController::class, 'index']);

        //tasks
        Route::resource('task', \App\Http\Controllers\Web\TaskController::class);
        Route::post('check-gallows-task-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkGallowsTaskLetter']);
        Route::post('check-test-task-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkTestTaskLetter']);
        Route::post('check-test-align-words-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkAlignWordsTaskLetter']);
        Route::post('check-test-missing-words-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkMissingWordTaskLetter']);
        Route::post('check-test-categorize-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkCategorizeTaskLetter']);
        Route::post('check-test-compose-text-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkComposeTextTaskLetter']);
        Route::post('check-task-offer', [\App\Http\Controllers\Web\TaskController::class, 'makeAnOfferTask']);
        Route::post('check-test-choose-translation-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkChooseTranslationTaskLetter']);
        Route::post('check-test-wheel-fortune-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkWheelFortuneTaskLetter']);
        Route::post('check-test-find-remember-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkFindRememberTaskLetter']);
        Route::post('check-field-dreams-task-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkFieldDreamsTaskLetter']);
        Route::post('check-test-repeat-text-letter', [\App\Http\Controllers\Web\TaskController::class, 'checkRepeatTextTaskLetter']);
        Route::post('check-crossword-task', [\App\Http\Controllers\Web\TaskController::class, 'checkCrosswordTask']);

        // statistics
        Route::get('statistics-course', [\App\Http\Controllers\Web\StatisticsController::class, 'statisticsCourse']);
        Route::get('statistics-lessons', [\App\Http\Controllers\Web\StatisticsController::class, 'statisticsLessons']);
        Route::get('statistics-tasks', [\App\Http\Controllers\Web\StatisticsController::class, 'statisticsTasks']);

        //test
        Route::get('user-course-module-test', [\App\Http\Controllers\Web\CourseTestController::class, 'getCourseModuleTest']);

        //subscribe news
        Route::resource('subscribe', SubscribeNewsController::class);
    });
});
// ADMIN PANEL
Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [AdminController::class, 'login']);
    Route::post('/forgot-password', [AdminController::class, 'forgotPassword']);
    Route::post('password/reset', [AdminController::class, 'sendResetResponse'])->name('admin.password.reset');

    Route::group(['middleware' => ['jwt.verify:admin']], function () {
        Route::get('logout', [AdminController::class, 'logout']);
        Route::get('me', [AdminController::class, 'userProfile']);

        //Admin
        Route::get('admin', [AdminController::class, 'index']);
        Route::post('admin', [AdminController::class, 'store']);
        Route::post('admin/{id}', [AdminController::class, 'update']);
        Route::post('admin/{id}/update-password', [AdminController::class, 'updatePassword']);
        Route::get('admin/{id}', [AdminController::class, 'show']);
        Route::delete('admin/{id}', [AdminController::class, 'destroy']);
        Route::get('admin/{id}/restore', [AdminController::class, 'restore']);

        //dashboard
        Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
        //language
        Route::get('language/get-all', [LanguageController::class, 'getAll']);
        Route::get('language/{id}/restore', [LanguageController::class, 'restore']);
        Route::resource('language', LanguageController::class);
        //faq-category
        Route::post('faq-category/update-sort', [FaqCategoryController::class, 'updateSort']);
        Route::get('faq-category/get-all', [FaqCategoryController::class, 'getAll']);
        Route::get('faq-category/{id}/restore', [FaqCategoryController::class, 'restore']);
        Route::resource('faq-category', FaqCategoryController::class);
        //faq
        Route::get('faq/get-all', [FaqController::class, 'getAll']);
        Route::post('faq/update-sort', [FaqController::class, 'updateSort']);
        Route::get('faq/{id}/restore', [FaqController::class, 'restore']);
        Route::resource('faq', FaqController::class);
        //news
        Route::get('news/get-all', [NewsController::class, 'getAll']);
        Route::get('news/{id}/restore', [NewsController::class, 'restore']);
        Route::post('news/{id}', [NewsController::class, 'update']);
        Route::resource('news', NewsController::class);

        //video
        Route::get('video/{id}/restore', [VideoController::class, 'restore']);
        Route::post('video/{id}', [VideoController::class, 'update']);
        Route::resource('video', VideoController::class);

        //subscribe news
        Route::get('subscribe/{id}/restore', [\App\Http\Controllers\SubscribeController::class, 'restore']);
        Route::resource('subscribe', SubscribeController::class);

        //Dynamic pages with Texts
        Route::get('dynamic-page/{id}/restore', [DynamicPageController::class, 'restore']);
        Route::get('dynamic-page/get-all', [DynamicPageController::class, 'getAll']);
        Route::resource('dynamic-page', DynamicPageController::class);

        Route::get('dynamic-page-text/{id}/restore', [DynamicPageTextController::class, 'restore']);
        Route::get('dynamic-page-text/{id}/set-current', [DynamicPageTextController::class, 'setCurrent']);
        Route::resource('dynamic-page-text', DynamicPageTextController::class);

        Route::get('review/{id}/restore', [\App\Http\Controllers\ReviewController::class, 'restore']);
        Route::get('review/{id}/{status}', [\App\Http\Controllers\ReviewController::class, 'updateStatus']);
        Route::post('review/{id}/answer-review', [\App\Http\Controllers\ReviewController::class, 'answerReview']);
        Route::resource('review', \App\Http\Controllers\ReviewController::class);

        //User
        Route::resource('user', \App\Http\Controllers\UserController::class);
        Route::get('user/{id}/restore', [\App\Http\Controllers\UserController::class, 'restore']);
        Route::get('user/{id}/lock', [\App\Http\Controllers\UserController::class, 'lockUser']);
        Route::post('user/update-password', [\App\Http\Controllers\UserController::class, 'updateUserPassword']);
        Route::get('user/{id}/bought-courses', [\App\Http\Controllers\UserController::class, 'userBoughtCourses']);
        Route::post('user/update-course-Level', [\App\Http\Controllers\UserController::class, 'updateCourseLevel']);
        Route::post('user/update-course-tariff', [\App\Http\Controllers\UserController::class, 'updateCourseTariff']);
        Route::post('user/send-message', [\App\Http\Controllers\UserController::class, 'sendUserMessage']);

        //Interesting
        Route::get('interesting/{id}/restore', [InterestingController::class, 'restore']);
        Route::post('interesting/{id}', [InterestingController::class, 'update']);
        Route::delete('interesting/{id}/delete-file', [InterestingController::class, 'deleteFile']);
        Route::resource('interesting',InterestingController::class);

        //Category Interesting
        Route::post('category-interesting/update-sort', [\App\Http\Controllers\CategoryInterestingController::class, 'updateSort']);
        Route::get('category-interesting/{id}/restore', [\App\Http\Controllers\CategoryInterestingController::class, 'restore']);
        Route::resource('category-interesting',\App\Http\Controllers\CategoryInterestingController::class);

        //Language-Category
        Route::post('teaching-language/{id}/update', [TeachingLanguageController::class, 'updateTeachingLang']);
        Route::get('teaching-language/{id}/restore', [TeachingLanguageController::class, 'restore']);
        Route::resource('teaching-language', TeachingLanguageController::class);

        Route::resource('contacts', ContactsController::class);
        Route::get('project-setting',  [\App\Http\Controllers\ProjectSettingController::class, 'index']);
        Route::post('project-setting', [\App\Http\Controllers\ProjectSettingController::class, 'store']);


        Route::get('admin',  [\App\Http\Controllers\AdminController::class, 'index']);

        Route::get('support-ticket-category/get-list', [\App\Http\Controllers\SupportTicketCategoryController::class, 'getSupportTicketCategory']);
        Route::resource('support-ticket-category', \App\Http\Controllers\SupportTicketCategoryController::class);
        Route::get('support-ticket-category/{id}/restore', [\App\Http\Controllers\SupportTicketCategoryController::class, 'restore']);

        Route::resource('support-ticket', \App\Http\Controllers\SupportTicketController::class);
        Route::post('support-ticket/send-message/{id}', [\App\Http\Controllers\SupportTicketController::class, 'sendMessage']);
        Route::get('support-ticket/mark-as-read/{id}', [\App\Http\Controllers\SupportTicketController::class, 'markAsRead']);

        Route::post('announcement/{id}/update', [\App\Http\Controllers\AnnouncementController::class, 'updateAnnouncement']);
        Route::resource('announcement', \App\Http\Controllers\AnnouncementController::class);
        Route::get('announcement/{id}/restore', [\App\Http\Controllers\AnnouncementController::class, 'restore']);
        Route::get('announcement/update-show-home-status/{id}', [\App\Http\Controllers\AnnouncementController::class, 'updateShowHomeStatus']);

        Route::get('course/list', [\App\Http\Controllers\CourseController::class, 'getList']);
        Route::resource('course', \App\Http\Controllers\CourseController::class);
        Route::get('course/{id}/restore', [\App\Http\Controllers\CourseController::class, 'restore']);
        Route::post('course/{id}', [\App\Http\Controllers\CourseController::class, 'update']);

        Route::get('course-module/course/{id}', [\App\Http\Controllers\CourseModuleController::class, 'getCourseModulesByCourse']);
        Route::resource('course-module', \App\Http\Controllers\CourseModuleController::class);
        Route::get('course-module/{id}/restore', [\App\Http\Controllers\CourseModuleController::class, 'restore']);

        Route::resource('course-tariff', \App\Http\Controllers\CourseTariffController::class);
        Route::get('course-tariff/{id}/restore', [\App\Http\Controllers\CourseTariffController::class, 'restore']);

        //Teaching Books
        Route::resource('teaching-book', \App\Http\Controllers\TeachingBookController::class);
        Route::post('teaching-book/{id}', [\App\Http\Controllers\TeachingBookController::class, 'update']);

        //Teaching Video
        Route::resource('teaching-video', \App\Http\Controllers\TeachingVideoController::class);
        Route::get('teaching-video/{id}/restore', [\App\Http\Controllers\TeachingVideoController::class, 'restore']);
        Route::post('teaching-video/{id}', [\App\Http\Controllers\TeachingVideoController::class, 'update']);

        Route::resource('teaching-audio', \App\Http\Controllers\TeachingAudioController::class);
        Route::post('teaching-audio/{id}', [\App\Http\Controllers\TeachingAudioController::class, 'update']);
        Route::get('teaching-audio/{id}/restore', [\App\Http\Controllers\TeachingAudioController::class, 'restore']);

        Route::get('currency/update-exchange-rate', [\App\Http\Controllers\CurrencyController::class, 'updateExchangeRate']);
        Route::resource('currency', \App\Http\Controllers\CurrencyController::class);
        Route::get('currency/{id}/restore', [\App\Http\Controllers\CurrencyController::class, 'restore']);
        Route::get('currency/{id}/set-main', [\App\Http\Controllers\CurrencyController::class, 'setIsMain']);

        Route::resource('application', \App\Http\Controllers\ApplicationController::class);

        // Payment methods
        Route::post('payment-method/update-sort', [\App\Http\Controllers\PaymentMethodController::class, 'updateSort']);
        Route::resource('payment-method', \App\Http\Controllers\PaymentMethodController::class);
        Route::post('payment-method/{id}', [\App\Http\Controllers\PaymentMethodController::class, 'update']);
        Route::get('payment-method/{id}/restore', [\App\Http\Controllers\PaymentMethodController::class, 'restore']);

        Route::resource('other-material', \App\Http\Controllers\OtherMaterialController::class);
        Route::post('other-material/{id}', [\App\Http\Controllers\OtherMaterialController::class, 'update']);
        Route::get('other-material/{id}/restore', [\App\Http\Controllers\OtherMaterialController::class, 'restore']);
        Route::delete('other-material/{id}/delete-file', [\App\Http\Controllers\OtherMaterialController::class, 'deleteFile']);

        Route::get('lessons-list/module/{id}', [\App\Http\Controllers\LessonController::class, 'getLessonsByModule']);
        Route::resource('lesson', \App\Http\Controllers\LessonController::class);
        Route::post('lesson/{id}', [\App\Http\Controllers\LessonController::class, 'update']);
        Route::get('lesson/{id}/restore', [\App\Http\Controllers\LessonController::class, 'restore']);


        Route::resource('task-type', \App\Http\Controllers\TaskTypeController::class);

        Route::get('task/{id}/block', [\App\Http\Controllers\TaskController::class, 'getTasksByLessonBlock']);
        Route::get('task/{id}/restore', [\App\Http\Controllers\TaskController::class, 'restore']);
        Route::delete('task/{id}',  [\App\Http\Controllers\TaskController::class, 'destroy']);

        Route::post('task-question/delete-answers', [\App\Http\Controllers\TaskTestController::class, 'deleteAnswers']);
        Route::post('task-question/delete-questions', [\App\Http\Controllers\TaskTestController::class, 'deleteQuestions']);
        Route::post('task-question/{id}', [\App\Http\Controllers\TaskTestController::class, 'update']);
        Route::resource('task-question', \App\Http\Controllers\TaskTestController::class);
        Route::delete('task-question/{id}/delete-file', [\App\Http\Controllers\TaskTestController::class, 'deleteFile']);

        Route::post('task-missing-word/{id}', [\App\Http\Controllers\TaskMissingWordController::class, 'update']);
        Route::resource('task-missing-word', \App\Http\Controllers\TaskMissingWordController::class);

        Route::post('task-pick-up-translation/delete-translations', [\App\Http\Controllers\TaskPickUpTranslationController::class, 'deleteTranslation']);
        Route::post('task-pick-up-translation/{id}', [\App\Http\Controllers\TaskPickUpTranslationController::class, 'update']);
        Route::resource('task-pick-up-translation', \App\Http\Controllers\TaskPickUpTranslationController::class);

        Route::post('task-field-of-dream/{id}', [\App\Http\Controllers\TaskFieldOfDreamController::class, 'update']);
        Route::resource('task-field-of-dream', \App\Http\Controllers\TaskFieldOfDreamController::class);

        Route::post('task-gallows/{id}', [\App\Http\Controllers\TaskGallowController::class, 'update']);
        Route::resource('task-gallows', \App\Http\Controllers\TaskGallowController::class);

        Route::post('task-speak-text/{id}', [\App\Http\Controllers\TaskSpeakTextController::class, 'update']);
        Route::resource('task-speak-text', \App\Http\Controllers\TaskSpeakTextController::class);

        Route::post('task-remember-find/delete-words', [\App\Http\Controllers\TaskRememberFindController::class, 'deleteWord']);
        Route::post('task-remember-find/{id}', [\App\Http\Controllers\TaskRememberFindController::class, 'update']);
        Route::resource('task-remember-find', \App\Http\Controllers\TaskRememberFindController::class);

        Route::post('task-compose-text/{id}', [\App\Http\Controllers\TaskComposeTextController::class, 'update']);
        Route::resource('task-compose-text', \App\Http\Controllers\TaskComposeTextController::class);

        Route::post('task-suggested-text/delete-words', [\App\Http\Controllers\TaskSuggestedFromWordsController::class, 'deleteWord']);
        Route::post('task-suggested-text/{id}', [\App\Http\Controllers\TaskSuggestedFromWordsController::class, 'update']);
        Route::resource('task-suggested-text', \App\Http\Controllers\TaskSuggestedFromWordsController::class);

        Route::post('task-deck-card/delete-answers', [\App\Http\Controllers\TaskDeckCardController::class, 'deleteAnswers']);
        Route::post('task-deck-card/{id}', [\App\Http\Controllers\TaskDeckCardController::class, 'update']);
        Route::resource('task-deck-card', \App\Http\Controllers\TaskDeckCardController::class);

        Route::post('task-wheel-fortune/delete-answers', [\App\Http\Controllers\TaskWheelFortuneController::class, 'deleteAnswers']);
        Route::post('task-wheel-fortune/{id}', [\App\Http\Controllers\TaskWheelFortuneController::class, 'update']);
        Route::resource('task-wheel-fortune', \App\Http\Controllers\TaskWheelFortuneController::class);

        Route::post('task-categorize/delete-category', [\App\Http\Controllers\TaskCategorizeController::class, 'deleteCategory']);
        Route::post('task-categorize/delete-category-item', [\App\Http\Controllers\TaskCategorizeController::class, 'deleteCategoryItem']);
        Route::post('task-categorize/{id}', [\App\Http\Controllers\TaskCategorizeController::class, 'update']);
        Route::resource('task-categorize', \App\Http\Controllers\TaskCategorizeController::class);

        Route::post('task-crossword/delete-words', [\App\Http\Controllers\TaskCrosswordController::class, 'deleteWord']);
        Route::post('task-crossword/{id}', [\App\Http\Controllers\TaskCrosswordController::class, 'update']);
        Route::resource('task-crossword', \App\Http\Controllers\TaskCrosswordController::class);

        Route::get('course-level/list', [\App\Http\Controllers\CourseLevelController::class, 'courseLevelList']);
        Route::resource('course-level', \App\Http\Controllers\CourseLevelController::class);
        Route::get('course-level/{id}/restore', [\App\Http\Controllers\CourseLevelController::class, 'restore']);

        Route::get('lesson-block/{id}/lesson', [\App\Http\Controllers\LessonBlockController::class, 'getLessonBlockById']);
        Route::post('lesson-block/{id}', [\App\Http\Controllers\LessonBlockController::class, 'update']);
        Route::resource('lesson-block', \App\Http\Controllers\LessonBlockController::class);
        Route::get('lesson-block/{id}/restore', [\App\Http\Controllers\LessonBlockController::class, 'restore']);
        Route::delete('lesson-block/{id}/delete-file', [\App\Http\Controllers\LessonBlockController::class, 'deleteFile']);

        // Course Chat
        Route::resource('course-chat', \App\Http\Controllers\CourseChatController::class);

        // Email Template
        Route::resource('email-template', \App\Http\Controllers\EmailTemplateController::class);
        Route::get('email-template/{id}/restore', [\App\Http\Controllers\EmailTemplateController::class, 'restore']);


        Route::post('chat-course-expert', [\App\Http\Controllers\ChatExpertController::class, 'sendMessage']);
        Route::get('chat-course-expert/{course_id}/{user_id}', [\App\Http\Controllers\ChatExpertController::class, 'index']);
        Route::group(['prefix' => 'course-expert'], function () {
            Route::post('call-user', [\App\Http\Controllers\ChatExpertController::class, 'callUser']);
            Route::post('accept-call', [\App\Http\Controllers\ChatExpertController::class, 'acceptCall']);
        });

        //CourseTest
        Route::get('course-test/{id}/list', [\App\Http\Controllers\CourseTestController::class, 'getCourseTestsListByCourse']);
        Route::get('/course-test/{id}/task-list', [\App\Http\Controllers\CourseTestController::class, 'getTestTaskList']);
        Route::resource('course-test', \App\Http\Controllers\CourseTestController::class);
        Route::get('course-test/{id}/restore', [\App\Http\Controllers\CourseTestController::class, 'restore']);

        //CourseModuleTest
        Route::get('course-module-test/{id}/list', [\App\Http\Controllers\CourseModuleTestController::class, 'getCourseModuleTestsListByCourse']);
        Route::get('/course-module-test/{id}/task-list', [\App\Http\Controllers\CourseModuleTestController::class, 'getModuleTestTaskList']);
        Route::resource('course-module-test', \App\Http\Controllers\CourseModuleTestController::class);
        Route::get('course-module-test/{id}/restore', [\App\Http\Controllers\CourseModuleTestController::class, 'restore']);

        //CourseVideo
        Route::get('course-video/{id}/list', [\App\Http\Controllers\CourseVideoController::class, 'getCourseVideosListByCourse']);
        Route::post('course-video/{id}', [\App\Http\Controllers\CourseVideoController::class, 'update']);
        Route::resource('course-video', \App\Http\Controllers\CourseVideoController::class);
        Route::get('course-video/{id}/restore', [\App\Http\Controllers\CourseVideoController::class, 'restore']);
    });
});
