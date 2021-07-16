<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCategorizeResource;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskCategorize;
use App\Models\TaskCategorizeCategory;
use App\Models\TaskCategorizeCategoryItem;
use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

class TaskCategorizeController extends Controller
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
            'categorize' => 'required',
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
            'test_id',
            'module_test_id',
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

            $audioName = time() . '-' . uniqid() . '.' . $file->extension();
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

        $categorizeData = [
            'prompt' => $request->categorize['prompt'],
            'task_id' => $task->id
        ];

        $newCategorize = TaskCategorize::create($categorizeData);

        $categories = $request->categorize['category'];

        foreach ($categories as $category) {
            if (isset($category['image'])) {
                $imag = $category['image'];
                $fileName = uniqid() . '.' . $imag->getClientOriginalExtension();
                $imag->storeAs('public/tasks/category/image', $fileName);
            } else {
                $fileName = null;
            }

            $categoryData = [
                'name' => $category['name'],
                'image' => $fileName,
                'categorize_id' => $newCategorize->id
            ];

            $newCategorizeCategory = TaskCategorizeCategory::create($categoryData);

            foreach ($category['category_items'] as $categoryItem) {
                if (isset($categoryItem['image'])) {
                    $itemImag = $categoryItem['image'];
                    $itemFileName = uniqid() . '.' . $itemImag->getClientOriginalExtension();
                    $itemImag->storeAs('public/tasks/category/item/image', $itemFileName);
                } else {
                    $itemFileName = null;
                }

                $categorizeCategoryItemData = [
                    'name' => $categoryItem['name'],
                    'image' => $itemFileName,
                    'categorize_category_id' => $newCategorizeCategory->id
                ];

                TaskCategorizeCategoryItem::create($categorizeCategoryItemData);
            }
        }

        return response()->json([
            'task' => new TaskResource($task),
            'categorize' => $newCategorize
        ], 200);
    }

    /**
     * @param $id
     * @return TaskResource
     */
    public function show($id)
    {
        $task = Task::find($id);

        $categorize = TaskCategorize::where('task_id', $id)->first();

        return response()->json([
            'task' => new TaskResource($task),
            'categorize' => new TaskCategorizeResource($categorize)
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
            'categorize' => 'required',
            'video_iframe' => 'sometimes',
            'video' => 'sometimes|mimes:mp4,webm,ogg'
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
        } else {
            $task->lesson_block_id = $data['lesson_block_id'];
        }

        $categorize = $request->categorize;

        $categorizeData = [
            'prompt' => $categorize['prompt'],
            'task_id' => $task->id
        ];

        TaskCategorize::withTrashed()->where('id', $request->categorize_id)->update($categorizeData);

        $categories = $request->categorize['category'];

        foreach ($categories as  $category) {

            $data = ['name' => $category['name'], 'categorize_id' => $request->categorize_id];
            if (!empty($category['image'])){
                $data['image'] = $this->fileUpload($category['image'], 'image');
            }
            if (!empty($category['id'])){
                TaskCategorizeCategory::where('id', $category['id'])
                    ->update($data);
                $category_id = $category['id'];
            }else{
                $taskCategorizeCategory = TaskCategorizeCategory::create($data);
                $category_id = $taskCategorizeCategory->id;
            }

            foreach ($category['category_items'] as $categoryItem) {
                $data = ['name' => $categoryItem['name'], 'categorize_category_id' => $category_id];
                if (!empty($categoryItem['image'])){
                    $data['image'] = $this->fileUpload($categoryItem['image'], 'item/image');
                }
                if (!empty($categoryItem['id'])){
                    TaskCategorizeCategoryItem::where('id', $categoryItem['id'])
                        ->update($data);
                }else{
                    TaskCategorizeCategoryItem::create($data);
                }
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
            $audioName = time() . '-' . uniqid() . '.' . $file->extension();
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
        $videoName = time() . '-' . uniqid() . '.' . $file->extension();
        $destinationPath = 'public/tasks/videos';
        $file->storeAs($destinationPath, $videoName);

        return $videoName;
    }

    /**
     * @param $destinationPath
     * @param $file
     * @param $name
     */
    public function imageUpload($destinationPath, $file, $name)
    {
        $img = Image::make($file->path());
        foreach (TaskFile::IMAGE_SIZES as $key => $size) {
            $img->resize($size, $size)->save($destinationPath . '/' . $name
                . '_' . $key . '.' . $file->extension());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteCategory(Request $request)
    {
        $category_id = $request->category_id;

        TaskCategorizeCategory::withTrashed()->whereIn('id', $category_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category has been deleted successfully!'
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteCategoryItem(Request $request)
    {
        $category_item_id = $request->category_item_id;

        TaskCategorizeCategory::withTrashed()->whereIn('id', $category_item_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category Item has been deleted successfully!'
        ], 200);
    }

    private function fileUpload($itemImag, $path)
    {
        $itemFileName = uniqid() . '.' . $itemImag->getClientOriginalExtension();
        $itemImag->storeAs('public/tasks/category/'.$path, $itemFileName);
        return $itemFileName;
    }

}
