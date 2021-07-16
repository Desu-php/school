<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectSettingResources;
use App\Models\ProjectSetting;
use Illuminate\Http\Request;

class ProjectSettingController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $project_settings = ProjectSetting::all();

        return response()->json([
            'data' => ProjectSettingResources::collection($project_settings),
        ], 200);
    }

    public function store(Request $request)
    {
        $settings = $request->all();
        try {
            foreach ($settings as $setting) {
                if (isset($setting['id'])) {
                    ProjectSetting::where('id', $setting['id'])->update($setting);
                } else {
                    ProjectSetting::create($setting);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 402);
        }

        $project_settings = ProjectSetting::all();

        return response()->json([
            'data' => ProjectSettingResources::collection($project_settings),
        ], 200);
    }
}
