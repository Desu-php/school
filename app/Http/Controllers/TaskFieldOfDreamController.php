<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskFieldOfDream;
use App\Models\TaskFile;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function React\Promise\all;

class TaskFieldOfDreamController extends Controller
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
            'select_dictionary' => 'required',
            'field_of_dreams' => 'required',
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

        if ($request->select_dictionary === "true") {
//            dd($request->field_of_dreams['amount_numbers']);
            $word = [
                'word' => 'Слово из словаря (код не дописан)',
                'amount_numbers' => $request->field_of_dreams['amount_numbers'],
                'prompt' => null,                                //Если выбран слово из словаря, то подсказки не будет
                'select_dictionary' => 1,
                'task_id' => $task->id
            ];
        } else {
            $word = [
                'word' => $request->field_of_dreams['word'],
                'amount_numbers' => null,
                'prompt' => $request->field_of_dreams['prompt'],
                'select_dictionary' => 0,
                'task_id' => $task->id
            ];
        }

        TaskFieldOfDream::create($word);

        return response()->json([
            'task' =>  new TaskResource($task),
            'word'=> $word
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $task = Task::find($id);

        $word = TaskFieldOfDream::where('task_id', $id)->first();

        return response()->json([
            'task' =>  new TaskResource($task),
            'word'=> $word
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
            'select_dictionary' => 'required',
            'field_of_dreams' => 'required',
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



        if ($request->select_dictionary === "true") {
            $word = [
                'word' => 'Слово из словаря (код не дописан)',
                'amount_numbers' => $request->field_of_dreams['amount_numbers'],
                'prompt' => null,                                //Если выбран слово из словаря, то подсказки не будет
                'select_dictionary' => 1,
                'task_id' => $task->id
            ];
        } else {
            $word = [
                'word' => $request->field_of_dreams['word'],
                'amount_numbers' => null,
                'prompt' => $request->field_of_dreams['prompt'],
                'select_dictionary' => 0,
                'task_id' => $task->id
            ];
        }

        TaskFieldOfDream::withTrashed()->where('task_id', $id)->update($word);

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
            'word'=> $word
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
