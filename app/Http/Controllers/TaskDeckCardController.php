<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\TaskDeckCardAnswer;
use App\Models\TaskDeckCardQuestion;
use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

class TaskDeckCardController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $tasks = Task::withTrashed();

        return response()->json([
            'data' => TaskResource::collection($tasks->get()),
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'lesson_block_id' => 'nullable',
            'module_test_id' => 'nullable',
            'test_id' => 'nullable',
            'questions' => 'required',
            'task_type_id' => 'required',
            'video_iframe' => 'sometimes',
            'video' => 'sometimes|mimes:mp4,webm,ogg'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $data = $request->only(
            'name',
            'description',
            'lesson_block_id',
            'module_test_id',
            'test_id',
            'task_type_id',
            'video_iframe',
            'video'
        );

        if ($request->video_type === 'video' && $request->has('video')) {
            $video = $request->video;
            $data['video'] = $this->videoUpload($video);
            $data['video_iframe'] = null;
        }

        if ($request->video_type === 'video_iframe' && $request->has('video_iframe')) {
            $data['video'] = null;
        }

        if ($request->has('audio') && $request->audio) {
            $file = $request->audio;

            $audioName = time().'-'.uniqid().'.'. $file->extension();
            $destinationPath = 'public/tasks/audio';
            $file->storeAs($destinationPath, $audioName);
            $data['audio'] = $audioName;
        }

        $task = Task::create($data);

        if ($request->has('gallery') && $request->gallery) {
            $files = $request->file('gallery');
            foreach ($files as $file) {
                $fileName = time() . '-' . uniqid();
                $destinationPath = storage_path('app/public/tasks/gallery');
                $this->imageUpload($destinationPath, $file, $fileName);
                $task->task_file()->create([
                    'name' => $fileName,
                    'extension' => $file->extension(),
                ]);
            }
        }

        $questions = $request->questions;

        foreach ($questions as $question) {
            $questionData = [
                'question' => $question['question'],
                'prompt' => $question['prompt'],
                'task_id' => $task->id
            ];

            $newQuestion = TaskDeckCardQuestion::create($questionData);

            foreach ($question['answers'] as $answer) {
                $answerData = [
                    'answer' => $answer['answer'],
                    'correct_answer' => isset($answer['correct_answer']) ? true : false,
                    'task_deck_card_question_id' => $newQuestion->id
                ];

                TaskDeckCardAnswer::create($answerData);
            }
        }

        return response()->json([
            'task' =>  new TaskResource($task),
            'questions'=> $questions
        ], 200);
    }

    /**
     * @param $id
     * @return TaskResource
     */
    public function show($id)
    {
        $task = Task::find($id);

        $questions = TaskDeckCardQuestion::where('task_id', $id)->get();

        return response()->json([
            'task' =>  new TaskResource($task),
            'questions'=> $questions
        ], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'lesson_block_id' => 'nullable',
            'module_test_id' => 'nullable',
            'test_id' => 'nullable',
            'questions' => 'required',
            'video_iframe'     => 'sometimes',
            'video'       => 'sometimes|mimes:mp4,webm,ogg'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();

        $task = Task::withTrashed()->findOrFail($id);

        $task->name = $data['name'];
        $task->description = $data['description'];
        if ($task->test_id) {
            $task->test_id = $data['test_id'];
        } else if ($task->module_test_id) {
            $task->module_test_id = $data['module_test_id'];
        }  else {
            $task->lesson_block_id = $data['lesson_block_id'];
        }

        $questions = $request->questions;

        foreach ($questions as $question) {
            $questionData = [
                'question' => $question['question'],
                'prompt' => $question['prompt'],
                'task_id' => $task->id
            ];

            TaskDeckCardQuestion::withTrashed()->where('id', $question['id'])->update($questionData);

            foreach ($question['answers'] as $answer) {
                TaskDeckCardAnswer::updateOrCreate(
                    ['id' => $answer['id']],
                    ['answer' => $answer['answer'], 'correct_answer' => $answer['correct_answer'] === "true" ? 1 : 0, 'task_deck_card_question_id' => $question['id']]
                );
            }
        }

        if ($request->video_type === 'video' && $request->has('video')) {
            if ($request->file('video')) {
                $video = $request->video;
                $task['video'] = $this->videoUpload($video);
            }
            $task['video_iframe'] = null;
        }

        if ($request->video_type === 'video_iframe' && $request->has('video_iframe')) {
            $task['video'] = null;
        }

        if ($request->has('audio') && $request->audio) {
            $file = $request->audio;
            $audioName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/tasks/audio';
            $file->storeAs($destinationPath, $audioName);
            $task['audio'] = $audioName;
        }


        if ($request->has('gallery') && $request->gallery) {
            $files = $request->file('gallery');
            if (is_array($files))
                foreach ($files as $file) {
                    $fileName = time() . '-' . uniqid();
                    $destinationPath = storage_path('app/public/tasks/gallery');
                    $this->imageUpload($destinationPath, $file, $fileName);
                    $task->task_file()->create([
                        'name' => $fileName,
                        'extension' => $file->extension(),
                    ]);
                }
        }

        $task->save();
        return response()->json([
            'data' => $task
        ], 200);
    }

    /**
     * @param $file
     * @return string
     */
    public function videoUpload($file)
    {
        $videoName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/tasks/videos';
        $file->storeAs($destinationPath, $videoName);

        return $videoName;
    }

    /**
     * @param $destinationPath
     * @param $file
     * @param $name
     */
    public function imageUpload($destinationPath, $file, $name) {
        $img = Image::make($file->path());
        foreach (TaskFile::IMAGE_SIZES as $key => $size) {
            $img->resize($size, $size)->save($destinationPath .'/' . $name
                . '_' . $key . '.' . $file->extension());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteAnswers(Request $request)
    {
        $answers_id = $request->answers_id;

        TaskDeckCardAnswer::withTrashed()->whereIn('id', $answers_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Answers has been deleted successfully!'
        ], 200);
    }
}
