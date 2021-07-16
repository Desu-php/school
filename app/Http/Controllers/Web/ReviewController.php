<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
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
        $reviews = Review::where('status', Review::STATUS_APPROVE)->orderBy('id', 'desc');
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
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myReviews(Request $request)
    {
        $user_id = auth('api')->id();

        $reviews = Review::where('user_id', $user_id)->orderBy('id', 'desc');

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
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'rating' => 'required',
            'text' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();
        $data['status'] = Review::STATUS_MODERATION;
        $data['user_id'] = auth('api')->id();
        $review = Review::create($data);

        return response()->json([
            'data' => $review
        ], 200);
    }
}
