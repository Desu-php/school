<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $announcements = Announcement::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $announcements->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $announcements = $announcements->take($take)->skip($skip);
        } else {
            $announcements = $announcements->take($take)->skip(0);
        }

        return response()->json([
            'data' => AnnouncementResource::collection($announcements->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return AnnouncementResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'teaching_language_id' => 'required|exists:teaching_languages,id',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $input = $request->only(
            'title',
            'description',
            'is_show_in_home',
            'video',
            'video_iframe',
            'teaching_language_id'
        );


        $announcement = Announcement::create($input);

        return new AnnouncementResource($announcement);
    }

    /**
     * @param $id
     *
     * @return AnnouncementResource
     */
    public function show($id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);

        return new AnnouncementResource($announcement);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return AnnouncementResource|\Illuminate\Http\JsonResponse
     */
    public function updateAnnouncement(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'teaching_language_id' => 'required|exists:teaching_languages,id',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $input = $request->only(
            'title',
            'description',
            'is_show_in_home',
            'video',
            'video_iframe',
            'teaching_language_id'
        );

        if ($request->video_type === 'video' && $request->has('video')) {
            $video = $request->video;
            $input['video'] = $this->videoUpload($video);
            $input['video_iframe'] = null;
        }

        if ($request->video_type === 'video_iframe' && $request->has('video_iframe')) {
            $input['video'] = null;
        }

        $announcement = Announcement::withTrashed()->where('id', $id)->update($input);

        if ($announcement) {
            return new AnnouncementResource(Announcement::find($id));
        }
    }

    public function updateShowHomeStatus($id)
    {
        $announcement = Announcement::withTrashed()->find($id);
        if ($announcement->is_show_in_home) {
            $announcement->is_show_in_home = false;
        } else {
            $announcement->is_show_in_home = true;
        }

        $announcement->save();

        return response()->json([
            'status'   => true,
            'data' => new AnnouncementResource($announcement),
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $announcement = Announcement::withTrashed()->find($id);

        $announcement->restore();

        return response()->json([
            'status'   => true,
            'data' => new AnnouncementResource($announcement),
            'message'  => 'Announcement has been restored successfully!'
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
        $announcement = Announcement::withTrashed()->find($id);

        $deleteType = null;

        if(!$announcement->trashed()){
            $announcement->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $announcement->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Announcement has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $file
     * @return string
     */
    public function videoUpload($file)
    {
        $videoName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/announcement/video';
        $file->storeAs($destinationPath, $videoName);

        return $videoName;
    }
}
