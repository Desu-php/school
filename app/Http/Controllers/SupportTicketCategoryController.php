<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupportTicketCategoryResource;
use App\Models\SupportTicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketCategoryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $support_ticket_categories = SupportTicketCategory::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $support_ticket_categories->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $support_ticket_categories = $support_ticket_categories->take($take)->skip($skip);
        } else {
            $support_ticket_categories = $support_ticket_categories->take($take)->skip(0);
        }

        return response()->json([
            'data' => SupportTicketCategoryResource::collection($support_ticket_categories->get()),
            'pagination' =>[
            'count_pages' => ceil($count / $take),
            'count' => $count
            ]
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupportTicketCategory()
    {
        $support_ticket_category = SupportTicketCategory::all();

        return response()->json([
            'data' => SupportTicketCategoryResource::collection($support_ticket_category)
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return SupportTicketCategoryResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $support_ticket_category = SupportTicketCategory::create($data);

        return new SupportTicketCategoryResource($support_ticket_category);
    }

    /**
     * @param $id
     *
     * @return SupportTicketCategoryResource
     */
    public function show($id)
    {
        $support_ticket_category = SupportTicketCategory::withTrashed()->findOrFail($id);

        return new SupportTicketCategoryResource($support_ticket_category);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return SupportTicketCategoryResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $support_ticket_category = SupportTicketCategory::withTrashed()->findOrFail($id);
        $support_ticket_category->name = $data['name'];
        $support_ticket_category->save();

        return new SupportTicketCategoryResource($support_ticket_category);

    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $support_ticket_category = SupportTicketCategory::withTrashed()->find($id);

        $support_ticket_category->restore();

        return response()->json([
            'status'   => true,
            'faq_category' => new SupportTicketCategoryResource($support_ticket_category),
            'message'  => 'Category has been restored successfully!'
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

        $support_ticket_category = SupportTicketCategory::withTrashed()->find($id);

        $deleteType = null;

        if(!$support_ticket_category->trashed()){
            $support_ticket_category->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $support_ticket_category->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Category has been deleted successfully!'
        ], 200);
    }
}
