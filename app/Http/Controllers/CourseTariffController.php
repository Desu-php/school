<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseTariffResource;
use App\Http\Resources\InterestingResource;
use App\Models\CourseTariff;
use App\Models\Interesting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseTariffController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $course_tariff = CourseTariff::withTrashed()->orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $course_tariff->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $course_tariff = $course_tariff->take($take)->skip($skip);
        } else {
            $course_tariff = $course_tariff->take($take)->skip(0);
        }

        return response()->json([
            'data' => CourseTariffResource::collection($course_tariff->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return CourseTariffResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'duration' => 'required',
            'access_extend' => 'required',
            'automatic_check_tasks' => 'required',
            'access_independent_work' => 'required',
            'access_additional_materials' => 'required',
            'additional_course_gift' => 'required',
            'access_dictionary' => 'required',
            'access_grammar' => 'required',
            'access_chat' => 'required',
            'access_fb_chat' => 'required',
            'access_notes' => 'required',
            'feedback_experts' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'price',
            'duration',
            'automatic_check_tasks',
            'freezing_possibility',
            'access_independent_work',
            'access_additional_materials',
            'additional_course_gift',
            'access_dictionary',
            'access_grammar',
            'access_notes',
            'access_chat',
            'access_fb_chat',
            'access_extend',
            'feedback_experts',
            'access_upgrade_tariff',
            'access_materials_after_purchasing_course',
            'discount_for_family',
            'consultation'
        );

        $course_tariff = CourseTariff::create($data);

        return new CourseTariffResource($course_tariff);
    }

    /**
     * @param $id
     *
     * @return CourseTariffResource
     */
    public function show($id)
    {
        $course_tariff = CourseTariff::withTrashed()->findOrFail($id);
        return new CourseTariffResource($course_tariff);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return CourseTariffResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'duration' => 'required',
            'access_extend' => 'required',
            'automatic_check_tasks' => 'required',
            'access_independent_work' => 'required',
            'access_additional_materials' => 'required',
            'additional_course_gift' => 'required',
            'access_dictionary' => 'required',
            'access_grammar' => 'required',
            'access_chat' => 'required',
            'access_fb_chat' => 'required',
            'access_notes' => 'required',
            'feedback_experts' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'price',
            'duration',
            'automatic_check_tasks',
            'freezing_possibility',
            'access_independent_work',
            'access_additional_materials',
            'additional_course_gift',
            'access_dictionary',
            'access_grammar',
            'access_notes',
            'access_chat',
            'access_fb_chat',
            'access_extend',
            'feedback_experts',
            'access_upgrade_tariff',
            'access_materials_after_purchasing_course',
            'discount_for_family',
            'consultation'
        );

        CourseTariff::withTrashed()->where('id', $id)->update($data);

        return $this->show($id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $course_tariff = CourseTariff::withTrashed()->findOrFail($id);

        $course_tariff->restore();

        return response()->json([
            'status'   => true,
            'data' => new CourseTariffResource($course_tariff),
            'message'  => 'Tariff has been restored successfully!'
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
        $course_tariff = CourseTariff::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$course_tariff->trashed()){
            $course_tariff->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $course_tariff->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Tariff has been deleted successfully!'
        ], 200);
    }
}
