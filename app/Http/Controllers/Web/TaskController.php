<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCategorizeCategoryItemResource;
use App\Http\Resources\TaskCategorizeResource;
use App\Http\Resources\TaskCrosswordResource;
use App\Http\Resources\TaskDeckCardQuestionResource;
use App\Http\Resources\TaskGallowResource;
use App\Http\Resources\TaskQuestionResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskSpeakTextResource;
use App\Http\Resources\TaskWheelFortuneQuestionResource;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LessonBlock;
use App\Models\Task;
use App\Models\TaskAnswer;
use App\Models\TaskCategorize;
use App\Models\TaskCategorizeCategoryItem;
use App\Models\TaskComposeText;
use App\Models\TaskCrossword;
use App\Models\TaskDeckCardAnswer;
use App\Models\TaskDeckCardQuestion;
use App\Models\TaskFieldOfDream;
use App\Models\TaskGallow;
use App\Models\TaskMissingWord;
use App\Models\TaskPickUpTranslation;
use App\Models\TaskQuestion;
use App\Models\TaskRememberFind;
use App\Models\TaskSpeakText;
use App\Models\TaskSuggestedFromText;
use App\Models\TaskSuggestedFromTextWord;
use App\Models\TaskTranslation;
use App\Models\TaskWheelFortuneAnswer;
use App\Models\TaskWheelFortuneQuestion;
use App\Models\TeachingLanguage;
use App\Models\UserLesson;
use App\Models\UserLessonBlock;
use App\Models\UserModule;
use App\Models\UserTask;
use App\Models\UserTaskPoint;
use App\Servicss\TaskService;
use Database\Factories\TaskWheelFortuneQuestionFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**-
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        $data = Task::findOrFail($id);

        $lessonTaskCount = 0;
        $lessonTaskNumberArray = [];
        $lessonTaskNumber = 0;


        if (!$data->test_id && !$data->module_test_id) {
            $lessonBlock = LessonBlock::where('id', $data->lesson_block_id)->first();
            $lessonBlocks = LessonBlock::where('lesson_id', $lessonBlock->lesson_id)->get();

            foreach ($lessonBlocks as $block) {
                $lessonTaskCount += count($block->tasks);
                foreach ($block->tasks as $task) {
                    array_push($lessonTaskNumberArray, $task->id);
                }
            }
        }

        $lessonBlock = LessonBlock::where('id', $data->lesson_block_id)->first();
        $lesson = Lesson::where('id', $lessonBlock->lesson_id)->first();
        $course_module = CourseModule::where('id', $lesson->course_module_id)->first();
        $course = Course::where('id', $course_module->course_id)->first();

        $nextTaskId = '';
        foreach ($lessonTaskNumberArray as $key => $number) {
            if ($number === +$id) {
                $lessonTaskNumber = $key + 1;
                if ($key + 1 !== count($lessonTaskNumberArray)) {
                    $nextTaskId = [
                       'next_task_id' => $lessonTaskNumberArray[$key + 1],
                       "next_task_type_id" => Task::findOrFail($lessonTaskNumberArray[$key + 1])->task_type_id
                    ] ;
                } else {
                    $nextTaskId = null;
                }
            }
        }

        switch ($data->task_type_id) {
            case 1:
                $taskQuestion = TaskQuestion::where('task_id', $id)->get();
                $task = TaskQuestionResource::collection($taskQuestion);
                break;
            case 2:
                $task = TaskMissingWord::where('task_id', $id)->first();
                $array = preg_split('/(?<=\[)(.*?)(?=\])/', $task->missing_words_text);
                $string = implode("", $array);
                $task->missing_words_text = $string;
                break;
            case 3:
                $task = TaskPickUpTranslation::where('task_id', $id)->first();
                $taskTranslations = $task->translations()->get();
                $task_translations = array();
                $task_phrases = array();
                foreach ($taskTranslations as $translation) {
                    array_push($task_translations, $translation->translation);
                    array_push($task_phrases, $translation->phrase);
                }
                unset($task->translations);
                shuffle($task_phrases);
                shuffle($task_translations);
                $task['task_phrases'] = $task_phrases;
                $task['task_translations'] = $task_translations;
                break;
            case 4:
                $taskCategorize = TaskCategorize::where('task_id', $id)->with(['categories' => function($query){
                    $query->orderBy(DB::raw('RAND()'));
                }])->first();
                $task = new TaskCategorizeResource($taskCategorize);
                $categoriesId = $task->categories->pluck('id');
                $categoriesItems = TaskCategorizeCategoryItem::orderBy(DB::raw('RAND()'))->whereIn('categorize_category_id', $categoriesId)->get();
                break;
            case 5:
                $task = TaskComposeText::where('task_id', $id)->first();
                $textArray = preg_split('/(?<=\[)(.*?)(?=\])/', $task->missing_words_text);
                $re = '/(?<=\[)([^]]+)(?=\])/m';
                preg_match_all($re, $task->missing_words_text, $matches, PREG_SET_ORDER, 0);
                $wordArr = $matches;

                $string = implode("", $textArray);
                $task->missing_words_text = $string;
                $task->words = $wordArr;
                break;
            case 6:
                $taskSpeak = TaskSpeakText::where('task_id', $id)->first();
                $task = new TaskSpeakTextResource($taskSpeak);
                break;
            case 7:
                $taskCrossword = TaskCrossword::where('task_id', $id)->first();
                $task = new TaskCrosswordResource($taskCrossword);
                break;
            case 8:
                $taskFieldOfDream = TaskFieldOfDream::where('task_id', $id)->first();
                $task = new TaskGallowResource($taskFieldOfDream);
                break;
            case 9:
                $taskGallow = TaskGallow::where('task_id', $id)->first();
                $task = new TaskGallowResource($taskGallow);
                break;
            case 10:
                $taskQuestion = TaskDeckCardQuestion::where('task_id', $id)->get();
                $task = TaskDeckCardQuestionResource::collection($taskQuestion);
                break;
            case 11:
                $taskQuestion = TaskWheelFortuneQuestion::where('task_id', $id)->get();
                $task = TaskWheelFortuneQuestionResource::collection($taskQuestion);
                break;
            case 12:
                $task = TaskRememberFind::where('task_id', $id)->first();
                $taskWords = $task['words']->toArray();
                $wordsArray = array();
                foreach ($taskWords as $word) {
                    $word['code'] = "a" . $word['id'];
                    array_push($wordsArray, $word);
                    $word['code'] = "b" . $word['id'];
                    array_push($wordsArray, $word);
                }
                shuffle($wordsArray);
                unset($task->words);
                $task['words'] = $wordsArray;
                break;
            case 13:
                $task = TaskSuggestedFromText::where('task_id', $id)->first();
                $wordsArray = array();
                foreach ($task->words as $word) {
                    array_push($wordsArray, $word);
                }
                shuffle($wordsArray);
                unset($task->words);
                $task['words'] = $wordsArray;
                break;
        }

        return response()->json([
            'data' => new TaskResource($data),
            'task' => $task,
            'lesson_task_count' => $lessonTaskCount,
            'next_task' => $nextTaskId,
            'course_teaching_language' => $course->teaching_language,
            'lesson_number' => $lessonTaskNumber
        ], 200);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // task 1
    public function checkTestTaskLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required',
            'answer_id' => 'required',
            'module' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        if ($request->refresh) {
            $user_task = UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first();
            if ($user_task) {
                $lesson_block_id = Task::where('id', $request->task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                if ($user_task && $user_lesson ) {
                    $lesson_point = (+$user_lesson) - (+$user_task->point);
                    $lesson_block_point = (+$user_lesson_block) - (+$user_task->point);
                    UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                    UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                }
                $user_module = UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->first();
                if ($user_task && $user_module ) {
                    $module_point = (+$user_module->point) - (+$user_task->point);
                    UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->update(['point' => $module_point]);
                }
                $d = [
                    'point' => '0'
                ];
                $user_task->update($d);
                UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->update(['point' => '0']);
                return response()->json([
                    "answer" => false,
                    'message' => 'The answer was chosen incorrectly'
                ], 200);
            }

        }

        $task_id = TaskQuestion::where('id', $request->question_id)->pluck('task_id')->first();


        $task_servis = new TaskService();
        $task_servis->user_task_point($request->question_id, $task_id);
        $taskTest = TaskAnswer::where('id', $request->answer_id )->where('correct_answer', 1 )->where('task_question_id',  $request->question_id )->first();

        $task = Task::where('id', $task_id)->first();

        if (!$taskTest) {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        } else {
            if (!$task->test_id) {
                $task_servis->user_task_many($request->question_id, $task_id, $request->finish, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module );
            }
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // task 2
    public function checkMissingWordTaskLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'index' => 'required|array',
            'text' => 'required|array',
            'id' => 'required',
            'task_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $task_servis = new TaskService();
        $task_servis->user_task_point($request->id, $request->task_id);

        $textArr = $request->text;
        $task = TaskMissingWord::where('id',  $request->id)->where('task_id',  $request->task_id)->first()->missing_words_text;
        $wordArr = preg_split("/[\s\[]*[\s\]]*[\s\[]/", $task );

        $sum = 0;
        for ($i = 0; $i < count($wordArr); $i++){
            if ($textArr[$i] === $wordArr[$i]) {
              $sum ++ ;
            }
        }
        if (count($wordArr) === $sum ){
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        } else {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    //task 3
    public function checkAlignWordsTaskLetter (Request $request)
    {

        $validator = Validator::make($request->all(), [
            'word' => 'required|array',
            'translations' => 'required|array',
            'id' => 'required',
            'task_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $task_servis = new TaskService();
        $task_servis->user_task_point($request->id, $request->task_id);

        $wordArray = $request->word;
        $translationsArray = $request->translations;

        for ($i = 0; $i < count($wordArray); $i ++) {
            $task = TaskTranslation::where('phrase', $wordArray[$i])->where('translation', $translationsArray[$i])->first();
            if ($task) {
                $d[] = $task;
                $a = true;
            } else {
                $a = false;
            }
        }
        if ($a) {
            if (count($d) === count($wordArray) ){
                $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module );
                return response()->json([
                    "answer" => true,
                    'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                    'course_module' => $task_servis->course_modal_finish,
                    'course' => $task_servis->course_finish,
                    'message' => 'The answer was chosen correctly'
                ], 200);
            } else {
                return response()->json([
                    "answer" => false,
                    'message' => 'The answer was chosen incorrectly'
                ], 200);
            }

        } else {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    //4
    public function checkCategorizeTaskLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categorize' => 'required|array',
            'id' => 'required',
            'task_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $task_servis = new TaskService();
        $task_servis->user_task_point($request->id, $request->task_id);

        $arr = [];
        foreach ($request->categorize as $categorize) {
            foreach ($categorize['items'] as $item) {
                $taskCategorize = TaskCategorizeCategoryItem::where('id', $item['id'])->where('categorize_category_id', $categorize['id'])->first();
                array_push($arr, $taskCategorize);
            }
        }
        $sum = 0;
         foreach ($arr as $item) {
             if ($item === null) {
                 $sum ++;
             }
         }
        if (!$sum){
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        } else {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // task 5
    public function checkComposeTextTaskLetter (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'composeText' => 'required|array',
            'id' => 'required',
            'task_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $task_servis = new TaskService();
        $task_servis->user_task_point($request->id, $request->task_id);

//        $composeText = $request->composeText;
//        $task = TaskComposeText::where('task_id', $request->task_id)->first();
//        dump($task->missing_words_text , $composeText);
//        $arr = [];
//        for ($i = 0; $i < count($composeText); $i++){
//            array_push($arr);
//        }
//        $sum = 0;
//        foreach ($arr as $item) {
//            if ($item === null) {
//                $sum ++;
//            }
//        }

        $textArr = $request->composeText;
        $task = TaskComposeText::where('id',  $request->id)->where('task_id',  $request->task_id)->first()->missing_words_text;
        $wordArr = preg_split("/[\s\[]*[\s\]]*[\s\[]/", $task );

        $sum = 0;
        for ($i = 0; $i < count($wordArr); $i++){
            if ($textArr[$i] === $wordArr[$i]) {
                $sum ++ ;
            }
        }

        if (count($wordArr) === $sum){
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        } else {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // task 6
    public function checkRepeatTextTaskLetter (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required',
            'id' => 'required',
            'task_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $task_servis = new TaskService();
        $task_servis->user_task_point($request->id, $request->task_id);

        $task = TaskSpeakText::where('id',  $request->id)->where('task_id',  $request->task_id)->first()->answer_text;
        $wordArr = preg_split("/[\s,:'`?.]*[\s,]*[\s]/", $task );
        $textArr = preg_split("/[\s,:'`?.]*[\s,]*[\s]/", $request->text);
        $sum = 0;
        if (count($wordArr) === count($textArr)) {
            for ($i = 0; $i < count($wordArr); $i++){
                if ($textArr[$i] === $wordArr[$i]) {
                    $sum ++ ;
                }
            }
        }
        if (count($wordArr) === $sum ){
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        } else {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    //task 7
    public function checkCrosswordTask(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $task_servis = new TaskService();
        $task_servis->user_task_point($request->id, $request->task_id);

        if ($request->type === 'incorrect') {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        } elseif ($request->type === 'correct') {
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // task 8
    public function checkFieldDreamsTaskLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'letter' => 'required',
            'task_id' => 'required',
            'id' => 'required',
            'word_arr' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $taskGallow = TaskFieldOfDream::where('task_id', $request->task_id)->first();
        $task = new TaskGallowResource($taskGallow);

        $wordUpper = strtoupper($task->word);
        $word = str_split($wordUpper);

        $word_array = $request->word_arr;
        if (in_array($request->letter, $word)) {
            for ($i = 0; $i < count($word); $i++) {
                if ($word[$i] === $request->letter) {
                    $word_array[$i] = $request->letter;
                }
            }
            $dataLetter['letter_includes'] = true;

            $data = [
                'question_id' => $request->id,
                'task_id' => $request->task_id,
                'user_id' => auth('api')->id(),
                'point' => 1

            ];
            UserTaskPoint::create($data);
        } else {
            $dataLetter['letter_includes'] = false;
            $questionSum = UserTaskPoint::where('question_id', $request->id)->where('task_id', $request->task_id)->where('point', '!=', '1')->where('user_id', auth('api')->id())->get();
            $data = [
                'question_id' => $request->id,
                'task_id' => $request->task_id,
                'user_id' => auth('api')->id(),
            ];
            if (count($questionSum) >= 0 && count($questionSum) <= 3) {
                $data['point'] = 0.5;
                UserTaskPoint::create($data);
            }  else if (count($questionSum) > 3 && count($questionSum) <= 8) {
                $data['point'] = 0.25;
                UserTaskPoint::create($data);
            } else {
                $data['point'] = 0;
                UserTaskPoint::create($data);
            }
        }

        $dataLetter['word_array'] = $word_array;

        $sum = 0;
        foreach ($word_array as $item) {
            if ($item === null) {
                $sum ++;
            }
        }
        if (!$sum){
            $task_servis = new TaskService();
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module );
            return response()->json([
                'data' => $dataLetter,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        } else {
            return response()->json([
                'data' => $dataLetter,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // task 9
    public function checkGallowsTaskLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'letter' => 'required',
            'task_id' => 'required',
            'id' => 'required',
            'word_arr' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $taskGallow = TaskGallow::where('task_id', $request->task_id)->first();
        $task = new TaskGallowResource($taskGallow);

        $wordUpper = strtoupper($task->word);
        $word = str_split($wordUpper);

        $word_array = $request->word_arr;
        if (in_array($request->letter, $word)) {
            for ($i = 0; $i < count($word); $i++) {
                if ($word[$i] === $request->letter) {
                    $word_array[$i] = $request->letter;
                }
            }
            $dataLetter['letter_includes'] = true;

            $data = [
                'question_id' => $request->id,
                'task_id' => $request->task_id,
                'user_id' => auth('api')->id(),
                'point' => 1

            ];
            UserTaskPoint::create($data);
        } else {
            $dataLetter['letter_includes'] = false;
            $questionSum = UserTaskPoint::where('question_id', $request->id)->where('task_id', $request->task_id)->where('point', '!=', '1')->where('user_id', auth('api')->id())->get();
            $data = [
                'question_id' => $request->id,
                'task_id' => $request->task_id,
                'user_id' => auth('api')->id(),
            ];
            if (count($questionSum) >= 0 && count($questionSum) <= 3) {
                $data['point'] = 0.5;
                UserTaskPoint::create($data);
            }  else if (count($questionSum) > 3 && count($questionSum) <= 8) {
                $data['point'] = 0.25;
                UserTaskPoint::create($data);
            } else {
                $data['point'] = 0;
                UserTaskPoint::create($data);
            }
        }

        $dataLetter['word_array'] = $word_array;

        $sum = 0;
        foreach ($word_array as $item) {
            if ($item === null) {
                $sum ++;
            }
        }
        if (!$sum){
            $task_servis = new TaskService();
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module );
            return response()->json([
                'data' => $dataLetter,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        } else {
            return response()->json([
                'data' => $dataLetter,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    //task 10
    public function checkChooseTranslationTaskLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required',
            'answer_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->refresh) {
            $user_task = UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first();
            if ($user_task) {
                $lesson_block_id = Task::where('id', $request->task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                if ($user_task && $user_lesson ) {
                    $lesson_point = (+$user_lesson) - (+$user_task->point);
                    $lesson_block_point = (+$user_lesson_block) - (+$user_task->point);
                    UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                    UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                }
                $user_module = UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->first();
                if ($user_task && $user_module ) {
                    $module_point = (+$user_module->point) - (+$user_task->point);
                    UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->update(['point' => $module_point]);
                }
                $d = [
                    'point' => '0'
                ];
                $user_task->update($d);
                UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->update(['point' => '0']);
                return response()->json([
                    "answer" => false,
                    'message' => 'The answer was chosen incorrectly'
                ], 200);
            }

        }


        $task_id = TaskDeckCardQuestion::where('id', $request->question_id)->pluck('task_id')->first();
        $task_servis = new TaskService();
        $task_servis->user_task_point($request->question_id, $task_id);

        $taskTest = TaskDeckCardAnswer::where('id', $request->answer_id )->where('correct_answer', 1 )->where('task_deck_card_question_id',  $request->question_id )->first();

        if (!$taskTest) {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        } else {
            $task_servis->user_task_many($request->question_id, $task_id, $request->finish, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'question_id' => $request->question_id,
                'message' => 'The answer was chosen correctly'
            ], 200);
        }
    }


    // task 11
    public function checkWheelFortuneTaskLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'answer_id' => 'required',
            'task_id' => 'required',
            'question_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->refresh) {
            $user_task = UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first();
            if ($user_task) {
                $lesson_block_id = Task::where('id', $request->task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                if ($user_task && $user_lesson ) {
                    $lesson_point = (+$user_lesson) - (+$user_task->point);
                    $lesson_block_point = (+$user_lesson_block) - (+$user_task->point);
                    UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                    UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                }
                $user_module = UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->first();
                if ($user_task && $user_module ) {
                    $module_point = (+$user_module->point) - (+$user_task->point);
                    UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->update(['point' => $module_point]);
                }
                $d = [
                    'point' => '0'
                ];
                $user_task->update($d);
                UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->update(['point' => '0']);
                return response()->json([
                    "answer" => false,
                    'message' => 'The answer was chosen incorrectly'
                ], 200);
            }

        }


        $task_servis = new TaskService();
        $task_servis->user_task_point($request->question_id, $request->task_id);
         $checkAnswer = TaskWheelFortuneAnswer::where('id', $request->answer_id)->where('task_wheel_fortune_question_id', $request->question_id)->where('correct_answer', 1)->first();
        if ($request->miss) {
            $taskPoint = UserTask::where('task_id',  $request->task_id)->where('user_id', auth('api')->id())->first();
            if (!$taskPoint) {
                $data = [
                    'task_id' => $request->task_id,
                    'point' => 0,
                    'status' => $request->finish ? 'done' : 'not_performed',
                    'user_id' => auth('api')->id(),
                ];
                UserTask::create($data);
                UserTaskPoint::where('question_id', $request->question_id)->where('task_id',  $request->task_id)->where('user_id', auth('api')->id())->delete();
            } else {
                $sum =  (+$taskPoint->point) + 0;
                $data = [
                    'task_id' => $request->task_id,
                    'point' => $sum,
                    'status' => $request->finish ? 'done' : 'not_performed',
                    'user_id' => auth('api')->id(),
                ];
                UserTask::where('task_id',  $request->task_id)->where('user_id', auth('api')->id())->update($data);
                UserTaskPoint::where('question_id', $request->question_id)->where('task_id',  $request->task_id)->where('user_id', auth('api')->id())->delete();
            }

            $user_task = UserTask::where('task_id',   $request->task_id)->where('user_id', auth('api')->id())->first()->point;
            $lesson_block_id =Task::where('id',  $request->task_id)->pluck('lesson_block_id')->first();
            $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
            $user_lesson =  UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->first()->point;
            $lesson_point = (+$user_lesson) + (+$user_task);
            $task_point = [
                'point' => $lesson_point
            ];
            UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update($task_point);
        }
        if (!$checkAnswer) {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        } else {
            $task_servis->user_task_many($request->question_id, $request->task_id, $request->finish, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
     // task 12
    public function checkFindRememberTaskLetter (Request $request)
    {

        $validator = Validator::make($request->all(), [
            'card' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->refresh) {
            $user_task = UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first();
            if ($user_task) {
                $lesson_block_id = Task::where('id', $request->task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                if ($user_task && $user_lesson ) {
                    $lesson_point = (+$user_lesson) - (+$user_task->point);
                    $lesson_block_point = (+$user_lesson_block) - (+$user_task->point);
                    UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                    UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                }
                $user_module = UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->first();
                if ($user_task && $user_module ) {
                    $module_point = (+$user_module->point) - (+$user_task->point);
                    UserModule::where('module_id', $request->module)->where('user_id', auth('api')->id())->update(['point' => $module_point]);
                }
                $d = [
                    'point' => '0'
                ];
                $user_task->update($d);
                UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->update(['point' => '0']);
                return response()->json([
                    "answer" => false,
                    'message' => 'The answer was chosen incorrectly'
                ], 200);
            }

        }

             $code = $request->card[0]['code'];
             $code2 = $request->card[1]['code'];
        for ($i = 0; $i < count($request->card); $i++) {
            $remember_find_id = $request->card[$i]['remember_find_id'];
            $id = $request->card[$i]['id'];
            $task_id = TaskRememberFind::where('id', $remember_find_id)->pluck('task_id')->first();
            $questionSum = UserTaskPoint::where('question_id', $id)->where('task_id', $task_id)->where('code', $code)->where('user_id', auth('api')->id())->get();
            $data = [
                'question_id' => $id,
                'task_id' => $task_id,
                'code' => $code,
                'user_id' => auth('api')->id(),
            ];

            if (count($questionSum) === 0) {
                $data['point'] = 1;
                UserTaskPoint::create($data);
            } else if (count($questionSum) === 1) {
                $data['point'] = 1;
                UserTaskPoint::create($data);
            } else if (count($questionSum) === 2) {
                $data['point'] = 0.5;
                UserTaskPoint::create($data);
            } else if (count($questionSum) === 3) {
                $data['point'] = 0.25;
                UserTaskPoint::create($data);
            } else {
                $data['point'] = 0;
                UserTaskPoint::create($data);
            }

            $codeId = substr($code, 1,1000);
            $codeId2 = substr($code2, 1,1000);
        }

        if ($codeId !== $codeId2 ) {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        } else {
            $task_servis = new TaskService();
            $task_servis->user_task_many($id, $task_id, $request->finish, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    //task 13
    public function makeAnOfferTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offer' => 'required|array',
            'id' => 'required',
            'task_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $task_servis = new TaskService();
        $task_servis->user_task_point($request->id, $request->task_id);

        $offer = $request->offer;
        $arr = [];


        for ($i = 0; $i < count($offer); $i++){
            $compose = TaskSuggestedFromTextWord::where('suggested_text_id', $offer[$i]['suggested_text_id'])->where('id', $offer[$i]['id'])->where('word_select', 1)->where('number', ($i + 1))->first();
            array_push($arr,$compose);
        }
        $sum = 0;
        foreach ($arr as $item) {
            if ($item === null) {
                $sum ++;
            }
        }
        if (!$sum){
            $task_servis->user_task($request->id, $request->task_id, $request->lesson_id, $request->lesson_next_id, $request->module,$request->next_module_lesson,$request->next_module);
            return response()->json([
                "answer" => true,
                'task_point' => UserTask::where('task_id', $request->task_id)->where('user_id', auth('api')->id())->first()->point,
                'course_module' => $task_servis->course_modal_finish,
                'course' => $task_servis->course_finish,
                'message' => 'The answer was chosen correctly'
            ], 200);
        } else {
            return response()->json([
                "answer" => false,
                'message' => 'The answer was chosen incorrectly'
            ], 200);
        }
    }
}


