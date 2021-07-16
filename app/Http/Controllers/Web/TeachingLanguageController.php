<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeachingLangResourse;
use App\Models\TeachingLanguage;
use Illuminate\Http\Request;

class TeachingLanguageController extends Controller
{
    public function index(Request $request)
    {
        $teaching_language = new TeachingLanguage;

        return response()->json([
            'data' =>  TeachingLangResourse::collection($teaching_language->get())
        ], 200);

    }
}
