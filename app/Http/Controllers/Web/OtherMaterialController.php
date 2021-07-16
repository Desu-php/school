<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\OtherMaterialResource;
use App\Models\OtherMaterial;
use Illuminate\Http\Request;

class OtherMaterialController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $other_materials = OtherMaterial::with('files')->orderBy('id', "desc");
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
     * Display the specified resource.
     * @param $id
     *
     * @return OtherMaterialResource
     */
    public function show($id)
    {
        $other_material = OtherMaterial::findOrFail($id);

        return new OtherMaterialResource($other_material);
    }

    /**
     * @param $id
     *
     * @return bool|string
     */
    public function downloadFiles($id)
    {
        $material = OtherMaterial::findOrFail($id);

        $zip_file = 'material-files.zip';
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $path = storage_path('app/public/other-material/file/' . $material->id);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file)
        {
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();

                $relativePath = 'storage/other-material/file/'. $material->id . '/' . substr($filePath, strlen($path) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return response()->download($zip_file);
    }

}
