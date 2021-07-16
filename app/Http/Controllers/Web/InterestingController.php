<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Interesting;
use App\Http\Resources\InterestingResource;
use Illuminate\Http\Request;

class InterestingController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $interesting = Interesting::orderBy('id', "desc");

        if ($request->has('category_slug')) {
            $category_slug = $request->input('category_slug');
            if ($category_slug !== "all" && $category_slug !== "advices") {
                $interesting = $interesting->whereHas('category_interesting', function ($query) use ($category_slug) {
                    $query->where('slug', $category_slug);
                });
            }
        }

        $except_interesting = $request->input('except_interesting') ? : null;

        if ($except_interesting) {
            $interesting = $interesting->where('id', '!=', $except_interesting);
        }

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 12;
        $count = $interesting->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $interesting = $interesting->take($take)->skip($skip);
        } else {
            $interesting = $interesting->take($take)->skip(0);
        }

        return response()->json([
            'data' =>  InterestingResource::collection($interesting->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $interesting = Interesting::findOrFail($id);

        return response()->json([
            'data' =>  new InterestingResource($interesting)
        ], 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bestInterestings(Request $request)
    {
        $interesting = Interesting::orderBy('id', "desc");

        if ($request->has('category_slug')) {
            $category_slug = $request->input('category_slug');
            if ($category_slug !== "all" && $category_slug !== "advices") {
                $interesting = $interesting->whereHas('category_interesting', function ($query) use ($category_slug) {
                    $query->where('slug', $category_slug);
                });
            }
        }

        $except_interesting = $request->input('except_interesting') ? : null;

        if ($except_interesting) {
            $interesting = $interesting->where('id', '!=', $except_interesting);
        }

        $data = $interesting->take(5)->skip(0)->get();

        if ($interesting->count() < 5) {

            $interestings = Interesting::orderBy('id', "desc");
            if ($except_interesting) {
                $interestings = $interestings->where('id', '!=', $except_interesting);
            }
            $interestings = $interestings->whereHas('category_interesting', function ($query) use ($category_slug) {
                $query->where('slug', '!=',$category_slug);
            });
            $interestings = $interestings->take(5 - $interesting->count())->skip(0)->get();
            $data = $data->merge($interestings);
        }

        return response()->json([
            'data' =>  InterestingResource::collection($data)
        ], 200);
    }
}
