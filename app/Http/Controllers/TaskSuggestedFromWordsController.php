<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\TaskSuggestedFromText;
use App\Models\TaskSuggestedFromTextWord;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskSuggestedFromWordsController extends Controller
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
            'prompt' => 'required',
            'words' => 'required',
            "words.*"  => "required",
            "words.*.word"  => "required",
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
            'module_id',
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

        $data = [
            'prompt' => $request->prompt,
            'task_id' => $task->id
        ];

        $suggestedFromWords = TaskSuggestedFromText::create($data);

        $words = $request->words;

        foreach ($words as $word) {
            $wordData = [
                'word' => $word['word'],
                'number' => $word['number'],
                'word_select' => isset($word['word_select']) ? true : false,
                'suggested_text_id' => $suggestedFromWords->id
            ];

            TaskSuggestedFromTextWord::create($wordData);
        }

        return response()->json([
            'task' =>  new TaskResource($task),
            'suggested_from_words'=> $suggestedFromWords
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $task = Task::find($id);

        $suggested = TaskSuggestedFromText::where('task_id', $id)->first();

        return response()->json([
            'task' =>  new TaskResource($task),
            'suggested_from_words'=> $suggested
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
            'prompt' => 'required',
            'words' => 'required',
            "words.*"  => "required",
            "words.*.word"  => "required",
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

        $suggestedWords = [
            'prompt' => $request->prompt,
            'task_id' => $task->id
        ];

        TaskSuggestedFromText::withTrashed()->where('task_id', $id)->update($suggestedWords);
        $suggestedWord = TaskSuggestedFromText::withTrashed()->where('task_id', $id)->first();

        $words = $request->words;

        foreach ($words as $word) {
            TaskSuggestedFromTextWord::updateOrCreate(
                ['id' => $word['id']],
                ['word' => $word['word'], 'word_select' => $word['word_select'] && $word['word_select'] !== "false" &&  $word['word_select'] !== "0" ? 1 : 0, 'number' => $word['number'], 'suggested_text_id' => $suggestedWord->id]
            );
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
            'data' => $task,
            'suggested words'=> $suggestedWord
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteWord(Request $request)
    {
        $words_id = $request->words_id;

        TaskSuggestedFromTextWord::withTrashed()->whereIn('id', $words_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Words/Fragment has been deleted successfully!'
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
}
