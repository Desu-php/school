<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Http\Resources\ReviewResource;
use App\Models\News;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $reviews = Review::with('user')->withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $reviews->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $reviews = $reviews->take($take)->skip($skip);
        } else {
            $reviews = $reviews->take($take)->skip(0);
        }

        return response()->json([
            'data' => ReviewResource::collection($reviews->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param $id
     *
     * @return ReviewResource
     */
    public function show($id)
    {
        $review = Review::withTrashed()->findOrFail($id);

        return new ReviewResource($review);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return ReviewResource
     */
    public function answerReview($id, Request $request)
    {
        Validator::make($request->all(), [
            'answer' => 'required|string'
        ]);

        $data = $request->only(['answer']);
        $data['admin_id'] = auth('admin')->id();

        $review = Review::withTrashed()->find($id);

        $review->answer = $data['answer'];
        $review->admin_id = $data['admin_id'];
        $review->save();

        return new ReviewResource(Review::findOrFail($id));
    }
    /**
     * @param $id
     *
     * @return ReviewResource
     */
    public function updateStatus($id, $status)
    {
        Review::withTrashed()->where('id', $id)->update(['status' => $status]);

        return new ReviewResource(Review::findOrFail($id));
    }


    /**
     * @param Request $request
     * @param         $id
     *
     * @return ReviewResource
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'rating' => 'required|integer',
            'status' => 'required|integer',
            'text' => 'required|string',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $data = $request->only([
            'full_name',
            'email',
            'rating',
            'status',
            'text',
            'user_id',
        ]);

        Review::withTrashed()->where('id', $id)->update($data);

        return new ReviewResource(Review::findOrFail($id));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $review = Review::withTrashed()->findOrFail($id);

        $review->restore();

        return response()->json([
            'status'   => true,
            'data' => new ReviewResource($review),
            'message'  => 'Review has been restored successfully!'
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

        $review = Review::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$review->trashed()){
            $review->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $review->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Review has been deleted successfully!'
        ], 200);
    }
}
