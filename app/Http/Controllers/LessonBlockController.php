<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonBlockResource;
use App\Models\LessonBlock;
use App\Models\LessonBlockFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

class LessonBlockController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLessonBlockById($id)
    {
        $lesson_blocks = LessonBlock::withTrashed()->where('lesson_id',$id)->get();

        return response()->json([
            'data' => LessonBlockResource::collection($lesson_blocks)
        ], 200);
    }

    /**
     * @param Request $request
     * @return LessonBlockResource
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'lesson_id' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only('lesson_id', 'name', 'description');
        $lesson_block = LessonBlock::create($data);


        if ($request->has('gallery') && $request->gallery) {
            $files = $request->file('gallery');
            foreach ($files as $file) {
                $fileName = time() . '-' . uniqid();
                $destinationPath = storage_path('app/public/lesson-block/gallery');
                $this->imageUpload($destinationPath, $file, $fileName);
                $lesson_block->files()->create([
                    'name' => $fileName,
                    'extension' => $file->extension(),
                    'type' => 'gallery'
                ]);
            }
        }

        return response()->json([
            'data' => new LessonBlockResource($lesson_block)
        ], 200);
    }

    /**
     * @param $id
     * @return LessonBlockResource
     */
    public function show($id)
    {
        $lesson_block = LessonBlock::withTrashed()->findOrFail($id);

        return  new LessonBlockResource($lesson_block);
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update (Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'lesson_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->except('files', 'gallery');
        LessonBlock::withTrashed()->where('id', $id)->update($data);
        $lesson_block = LessonBlock::withTrashed()->findOrFail($id);

        if ($request->has('gallery') && $request->gallery) {
            $files = $request->file('gallery');
            if (is_array($files))
                foreach ($files as $file) {
                    $fileName = time() . '-' . uniqid();
                    $destinationPath = storage_path('app/public/lesson-block/gallery');
                    $this->imageUpload($destinationPath, $file, $fileName);
                    $lesson_block ->files()->create([
                        'name' => $fileName,
                        'extension' => $file->extension(),
                        'type' => 'gallery'
                    ]);
                }
        }

        return response()->json([
            'data' => new LessonBlockResource($lesson_block)
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $lesson_block = LessonBlock::withTrashed()->find($id);
        $lesson_block->restore();

        return response()->json([
            'status'   => true,
            'message'  => 'Lesson Block has been restored successfully!'
        ], 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $lesson_block = LessonBlock::withTrashed()->find($id);
        $deleteType = null;

        if(!$lesson_block->trashed()){
            $lesson_block->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $lesson_block->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Lesson Block has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $destinationPath
     * @param $file
     * @param $name
     */
    public function imageUpload($destinationPath, $file, $name) {
        $img = Image::make($file->path());
        foreach (LessonBlockFile::IMAGE_SIZES as $key => $size) {
            $img->resize($size, $size)->save($destinationPath .'/' . $name
                . '_' . $key . '.' . $file->extension());
        }
    }


    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile($id)
    {
        $lesson_block_image = LessonBlockFile::findOrFail($id);
        $lesson_block_image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Material File has been deleted successfully!'
        ], 200);
    }
}
