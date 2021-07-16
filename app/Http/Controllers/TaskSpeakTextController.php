<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskSpeakTextResource;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\TaskSpeakText;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskSpeakTextController extends Controller
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
            'speak_text' => 'required',
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

        $taskSpeakText = $request->speak_text;
        $taskSpeakText['task_id'] = $task->id;

        if ($request->speak_video_type === 'video' && isset($taskSpeakText['video'])) {
            $video = $taskSpeakText['video'];
            $taskSpeakText['video'] = $this->videoUploadSpeakText($video);
            $taskSpeakText['video_iframe'] = null;
        }

        if ($request->speak_video_type === 'video_iframe' && isset($taskSpeakText['video_iframe'])) {
            $taskSpeakText['video'] = null;
        }

        if (isset($taskSpeakText['audio'])) {
            $audio = $taskSpeakText['audio'];

            $audioName = time().'-'.uniqid().'.'. $audio->extension();
            $destinationPath = 'public/tasks/speak_text/audio';
            $audio->storeAs($destinationPath, $audioName);
            $taskSpeakText['audio'] = $audioName;
        }

        TaskSpeakText::create($taskSpeakText);

        return response()->json([
            'task' =>  new TaskResource($task),
            'task_speak_text'=> $taskSpeakText
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $task = Task::find($id);

        $taskSpeakText = TaskSpeakText::where('task_id', $id)->first();

        return response()->json(array(
            'task' =>  new TaskResource($task),
            'task_speak_text'=> new TaskSpeakTextResource($taskSpeakText)
        ), 200);
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
            'speak_text' => 'required',
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

        $taskSpeakText = $request->speak_text;
        $taskSpeakText['task_id'] = $task->id;

        if ($request->speak_video_type === 'video' && isset($taskSpeakText['video'])) {
            if ($request->hasFile('speak_text.video')) {
                $video = $taskSpeakText['video'];
                $taskSpeakText['video'] = $this->videoUploadSpeakText($video);
            }
            $taskSpeakText['video_iframe'] = null;
        }

        if ($request->speak_video_type === 'video_iframe' && isset($taskSpeakText['video_iframe'])) {
            $taskSpeakText['video'] = null;
        }

        if (isset($taskSpeakText['audio'])) {
            $file = $taskSpeakText->audio;

            $audioName = time().'-'.uniqid().'.'. $file->extension();
            $destinationPath = 'public/tasks/speak_text/audio';
            $file->storeAs($destinationPath, $audioName);
            $taskSpeakText['audio'] = $audioName;
        }

        TaskSpeakText::withTrashed()->where('task_id', $id)->update($taskSpeakText);

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
            'task_speak_text'=> $taskSpeakText
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
     * @param $file
     * @return string
     */
    public function videoUploadSpeakText($file)
    {
        $videoName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/tasks/speak_text/video';
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
