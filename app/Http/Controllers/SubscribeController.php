<?php

namespace App\Http\Controllers;

use App\Models\SubscribeNews;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $subscribers = SubscribeNews::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ?: 1;
        $take = $request->input('count') ?: 6;
        $count = $subscribers->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $subscribers = $subscribers->take($take)->skip($skip);
        } else {
            $subscribers = $subscribers->take($take)->skip(0);
        }

        return response()->json([
            'data'       => $subscribers->get(),
            'pagination' => [
                'count_pages' => ceil($count / $take),
                'count'       => $count
            ]
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $subscribe = SubscribeNews::withTrashed()->findOrFail($id);

        $subscribe->restore();

        return response()->json([
            'status'   => true,
            'data' => $subscribe,
            'message'  => 'Subscribe has been restored successfully!'
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
        $subscribe = SubscribeNews::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$subscribe->trashed()){
            $subscribe->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $subscribe->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Subscribe has been deleted successfully!'
        ], 200);
    }
}
