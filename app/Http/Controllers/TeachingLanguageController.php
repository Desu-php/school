<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeachingLangResourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TeachingLanguage;

class TeachingLanguageController extends Controller
{

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TeachingLangResourse::collection(TeachingLanguage::withTrashed()->get());
    }

    /**
     * @param Request $request
     *
     * @return TeachingLangResourse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required',
            'letters' => 'required',
            'flag' => 'required',
            'color' => 'required',
        ]);

        $file = $request->flag;
        $data = $validator->validate();
        $flagName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/languages/flags';
        $file->storeAs($destinationPath, $flagName);

        $data['flag'] = $flagName;

        $teaching_lang_category = TeachingLanguage::create($data);

        return new TeachingLangResourse($teaching_lang_category);
    }

    /**
     * @param $id
     *
     * @return TeachingLangResourse
     */
    public function show($id)
    {
        $teaching_language = TeachingLanguage::withTrashed()->findOrFail($id);
        return new TeachingLangResourse($teaching_language);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return TeachingLangResourse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateTeachingLang(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'code'  => 'required',
            'letters'  => 'required',
            'color' => 'required'
        ]);

        $data = $validator->validate();

        if ($request->has('flag') && $request->hasFile('flag') && $request->flag) {
            $file = $request->flag;
            $flagName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/languages/flags';
            $file->storeAs($destinationPath, $flagName);

            $data['flag'] = $flagName;
        }


        $teaching_lang = TeachingLanguage::where('id', $id)->update($data);

        if ($teaching_lang) {
            return new TeachingLangResourse(TeachingLanguage::find($id));
        }
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $teachingLang = TeachingLanguage::withTrashed()->find($id);
        $teachingLang->restore();

        return response()->json([
            'status'   => true,
            'teaching_lang' => new TeachingLangResourse($teachingLang),
            'message'  => 'Teaching Language has been restored successfully!'
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

        $teachingLang = TeachingLanguage::withTrashed()->find($id);
        $deleteType = null;

        if(!$teachingLang->trashed()){
            $teachingLang->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $teachingLang->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Teaching Language Category has been deleted successfully!'
        ], 200);
    }
}
