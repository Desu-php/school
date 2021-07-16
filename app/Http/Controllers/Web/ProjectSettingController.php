<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProjectSetting;
use Illuminate\Http\Request;

class ProjectSettingController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $project_setting = ProjectSetting::all()->pluck('value', 'key');

        return response()->json([
            'data' => $project_setting,
        ], 200);
    }
}
