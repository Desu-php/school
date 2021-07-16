<?php

namespace App\Http\Controllers;

use App\Http\Resources\OtherMaterialResource;
use App\Models\OtherMaterial;
use App\Models\OtherMaterialFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;
use ZipArchive;

class OtherMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $other_materials = OtherMaterial::with('files')->withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $other_materials->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $other_materials = $other_materials->take($take)->skip($skip);
        } else {
            $other_materials = $other_materials->take($take)->skip(0);
        }

        return response()->json([
            'data' => OtherMaterialResource::collection($other_materials->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        $other_material = OtherMaterial::create($data);

        if ($request->has('gallery') && $request->gallery) {
            $files = $request->file('gallery');
            foreach ($files as $file) {
                $fileName = time() . '-' . uniqid();
                $destinationPath = storage_path('app/public/other-material/gallery');
                $this->imageUpload($destinationPath, $file, $fileName);
                $other_material->files()->create([
                    'name' => $fileName,
                    'extension' => $file->extension(),
                    'type' => 'gallery'
                ]);
            }
        }

        if ($request->has('files') && $request->files) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $fileName = time().'-'.uniqid().'.'. $file->extension();
                $destinationPath = 'public/other-material/file/' . $other_material->id;
                $file->storeAs($destinationPath, $fileName);
                $other_material->files()->create([
                    'name' => $fileName,
                    'extension' => $file->extension(),
                    'type' => 'file'
                ]);
            }
        }

        return response()->json([
            'data' => new OtherMaterialResource($other_material)
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param $id
     *
     * @return OtherMaterialResource
     */
    public function show($id)
    {
        $other_material = OtherMaterial::withTrashed()->findOrFail($id);

        return new OtherMaterialResource($other_material);
    }

    /**
     * Update the specified resource in storage.

     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->except('files', 'gallery');
        OtherMaterial::withTrashed()->where('id', $id)->update($data);
        $material = OtherMaterial::withTrashed()->findOrFail($id);

        if ($request->has('gallery') && $request->gallery) {
            $files = $request->file('gallery');
            if (is_array($files))
            foreach ($files as $file) {
                $fileName = time() . '-' . uniqid();
                $destinationPath = storage_path('app/public/other-material/gallery');
                $this->imageUpload($destinationPath, $file, $fileName);
                $material->files()->create([
                    'name' => $fileName,
                    'extension' => $file->extension(),
                    'type' => 'gallery'
                ]);
            }
        }

        if ($request->has('files') && $request->files) {
            $files = $request->file('files');
            if (is_array($files))
            foreach ($files as $file) {
                $fileName = time().'-'.uniqid().'.'. $file->extension();
                $destinationPath = 'public/other-material/file/' . $material->id;
                $file->storeAs($destinationPath, $fileName);
                $material->files()->create([
                    'name' => $fileName,
                    'extension' => $file->extension(),
                    'type' => 'file'
                ]);
            }
        }

        return response()->json([
            'data' => new OtherMaterialResource($material)
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $material = OtherMaterial::withTrashed()->findOrFail($id);

        $material->restore();

        return response()->json([
            'status'   => true,
            'data' => new OtherMaterialResource($material),
            'message'  => 'Material has been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $material = OtherMaterial::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$material->trashed()){
            $material->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $material->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Material has been deleted successfully!'
        ], 200);
    }


    /**
     * @param $destinationPath
     * @param $file
     * @param $name
     */
    public function imageUpload($destinationPath, $file, $name) {
        $img = Image::make($file->path());
        foreach (OtherMaterialFile::IMAGE_SIZES as $key => $size) {
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
        $material_file = OtherMaterialFile::findOrFail($id);
        $material_file->delete();

        return response()->json([
            'status' => true,
            'message' => 'Material File has been deleted successfully!'
        ], 200);
    }
}
