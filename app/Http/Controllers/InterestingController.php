<?php

namespace App\Http\Controllers;

use App\Models\Interesting;
use App\Models\InterestingFile;
use App\Http\Resources\InterestingResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Image;

class InterestingController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $interestings = Interesting::with('category_interesting')->orderBy('id', "desc")->withTrashed();
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $interestings->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $interestings = $interestings->take($take)->skip($skip);
        } else {
            $interestings = $interestings->take($take)->skip(0);
        }

        return response()->json([
            'data' => InterestingResource::collection($interestings->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return InterestingResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'category_interesting_id' => 'required|exists:category_interestings,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();


        $interesting = Interesting::create($data);

        $this->fileUpload($request, $interesting);

        return new InterestingResource($interesting);
    }

    /**
     * @param $id
     *
     * @return InterestingResource
     */
    public function show($id)
    {
        $interesting = Interesting::withTrashed()->findOrFail($id);
        return new InterestingResource($interesting);

    }


    /**
     * @param Request $request
     * @param         $id
     *
     * @return InterestingResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'short_description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'title',
            'description',
            'seo_title',
            'seo_description',
            'category_interesting_id',
            'short_description',
            'video_iframe'
        );

        $interesting = Interesting::where('id', $id)->update($data);

        $this->fileUpload($request, Interesting::find($id));

        if ($interesting) {
            return new InterestingResource(Interesting::find($id));
        }
    }

    /**
     * @param $request
     * @param $interesting
     */
    public function fileUpload($request, $interesting)
    {
        if ($request->has('gallery')) {
            $images = $request->gallery;
            $destinationPath = storage_path('app/public/interesting/gallery');
            foreach($images as $key => $image){
                $file = $this->imageUpload($destinationPath, $image, InterestingFile::TYPE_GALLERY);
                $interesting->files()->create([
                    'name' => $file['name'],
                    'extension' => $file['extension'],
                    'type' => InterestingFile::TYPE_GALLERY,
                ]);
            }
        }

        if ($request->has('image')) {
            $image = $request->image;
            $destinationPath = storage_path('app/public/interesting/image');
            $file =  $this->imageUpload($destinationPath, $image, InterestingFile::TYPE_IMAGE);
            $interesting->files()->create([
                'name' => $file['name'],
                'extension' => $file['extension'],
                'type' => InterestingFile::TYPE_IMAGE,
            ]);
        }

        if ($request->has('video')) {
            $path = 'public/interesting/video';
            $file = $this->mediaUpload($path, $request->video);

            $interesting->files()->create([
                'name' => $file['name'],
                'extension' => $file['extension'],
                'type' => InterestingFile::TYPE_VIDEO,
            ]);
        }

        if ($request->has('audio')) {
            $path = 'public/interesting/audio';
            $file = $this->mediaUpload($path, $request->audio);

            $interesting->files()->create([
                'name' => $file['name'],
                'extension' => $file['extension'],
                'type' => InterestingFile::TYPE_AUDIO,
            ]);
        }

        if ($request->has('files')) {
            $path = 'public/interesting/files';

            $files = $request->file('files');
            foreach ($files as $file) {
                $interestingFile = $this->mediaUpload($path, $file);

                $interesting->files()->create([
                    'name' => $interestingFile['name'],
                    'extension' => $interestingFile['extension'],
                    'type' => InterestingFile::TYPE_FILE,
                ]);
            }
        }
    }
    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $interesting = Interesting::withTrashed()->find($id);
        $interesting->restore();

        return response()->json([
            'status'   => true,
            'message'  => 'Interesting Article  has been restored successfully!'
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
        $interesting = Interesting::withTrashed()->find($id);
        $deleteType = null;

        if(!$interesting->trashed()){
            $interesting->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $interesting->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Interesting Article  has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $path
     * @param $file
     *
     * @return array
     */
    private function mediaUpload($path, $file)
    {
        $fileName = $file->getClientOriginalName();
        $extension = $file->extension();
        $result= explode('.' . $extension, $fileName);
        $fileName = $result[0];
        Storage::disk('local')->put($path . '/' . $fileName . '.' . $extension, File::get($file));

        return [
          'name' => $fileName,
          'extension' => $extension
        ];
    }

    /**
     * @param $destinationPath
     * @param $file
     * @param $type
     *
     * @return array
     */
    public function imageUpload($destinationPath, $file, $type) {
        $img = Image::make($file->path());
        $sizes = Interesting::IMAGE_SIZES;
        $fileName = time() . '-' . uniqid();
        $extension = $file->extension();

        foreach ($sizes[$type] as $key => $size) {
            if (!is_array($size)) {
                $width = $size;
                $height = $size;
            } else {
                $width = $size[0];
                $height = $size[1];
            }

            $img->resize($width, $height)->save($destinationPath . '/' . $fileName . '_' . $key . '.' . $extension);
        }

        return [
            'name' => $fileName,
            'extension' => $extension
        ];
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile($id)
    {

        $file = InterestingFile::findOrFail($id);

        $file->delete();

        return response()->json([
            'status' => true,
            'message' => 'File has been deleted successfully!'
        ], 200);
    }
}
