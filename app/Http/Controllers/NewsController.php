<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use App\Models\News;
use Image;

class NewsController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $news = News::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $news->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $news = $news->take($take)->skip($skip);
        } else {
            $news = $news->take($take)->skip(0);
        }

        return response()->json([
            'data' => NewsResource::collection($news->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }


  /**
   * @param NewsRequest $request
   *
   * @return NewsResource
   */
    public function store(NewsRequest $request)
    {
      $data = $request->all();
      if ($request->has('image')) {
        $image = $request->image;
        $imageName = time() . '-' . uniqid();
        $destinationPath = storage_path('app/public/news/images');
        $this->imageUpload($destinationPath, $image, $imageName);
        $data['image'] = $imageName;
        $data['image_extension'] = $image->extension();
      }
      if ($request->has('video')) {
          $video = $request->video;
          $data['video'] = $this->videoUpload($video);
          $data['video_iframe'] = null;
      }
      if ($request->has('video_iframe')) {
          $data['video'] = null;
      }

      $news = News::create($data);

      return new NewsResource($news);
    }

  /**
   * @param $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
    public function show($id)
    {
      $news = News::withTrashed()->findOrFail($id);

      return response()->json([
        'data' => new NewsResource($news)
      ], 200);
    }

  /**
   * @param NewsRequest $request
   * @param             $id
   *
   * @return NewsResource
   */
    public function update(NewsRequest $request, $id)
    {
      $data = $request->except(['video_type']);

      if ($request->has('image')) {
        $image = $request->image;
        $imageName = time().'-'.uniqid();
        $destinationPath = storage_path('app/public/news/images');
        $this->imageUpload($destinationPath, $image, $imageName);
        $data['image'] = $imageName;
        $data['image_extension'] = $image->extension();;
      }

      if ($request->has('video')) {
          $video = $request->video;
          $data['video'] = $this->videoUpload($video);
          $data['video_iframe'] = null;
      }
      if ($request->has('video_iframe')) {
          $data['video'] = null;
      }

      News::withTrashed()->where('id', $id)->update($data);

      return new NewsResource(News::findOrFail($id));
    }

  /**
   * @param $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function restore($id)
  {

    $news = News::withTrashed()->findOrFail($id);

    $news->restore();

    return response()->json([
      'status'   => true,
      'faq_category' => new NewsResource($news),
      'message'  => 'News has been restored successfully!'
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

      $news = News::withTrashed()->findOrFail($id);

      $deleteType = null;

      if(!$news->trashed()){
        $news->delete();
        $deleteType = 'delete';
      }
      else {
        $deleteType = 'forceDelete';
        $news->forceDelete();
      }

      return response()->json([
        'status' => true,
        'deleteType' => $deleteType,
        'message' => 'News has been deleted successfully!'
      ], 200);
    }

  /**
   * @param $destinationPath
   * @param $file
   * @param $name
   */
    public function imageUpload($destinationPath, $file, $name) {
      $img = Image::make($file->path());
      foreach (News::IMAGE_SIZES as $key => $size) {
        $img->resize($size, $size)->save($destinationPath . '/' . $name
          . '_' . $key . '.' . $file->extension());
      }
    }

    /**
     * @param $file
     *
     * @return mixed
     */
    public function videoUpload($file)
    {
        $videoName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/news/videos';
        $file->storeAs($destinationPath, $videoName);

        return $videoName;
    }
}
