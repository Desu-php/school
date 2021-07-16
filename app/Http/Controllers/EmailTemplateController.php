<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $email_templates = EmailTemplate::with('email_template_texts')->withTrashed()->orderBy('id', "asc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $email_templates->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $email_templates = $email_templates->take($take)->skip($skip);
        } else {
            $email_templates = $email_templates->take($take)->skip(0);
        }

        return response()->json([
            'data' => EmailTemplateResource::collection($email_templates->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return EmailTemplateResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                 => 'required',
            'blade_name'           => 'required|unique:email_templates',
            'email_template_texts' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validate();
        $email_template = EmailTemplate::create($data);

        foreach ($data['email_template_texts'] as $email_template_text) {
            EmailTemplateText::create([
                'key'               => $email_template_text['key'],
                'value'             => $email_template_text['value'],
                'email_template_id' => $email_template->id,
            ]);
        }

        return new EmailTemplateResource($email_template->load('email_template_texts'));
    }

    /**
     * @param $id
     *
     * @return EmailTemplateResource
     */
    public function show($id)
    {
        $email_template = EmailTemplate::withTrashed()->findOrFail($id);
        return new EmailTemplateResource($email_template->load('email_template_texts'));
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param         $id
     *
     * @return EmailTemplateResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'                         => 'required',
            'blade_name'                   => 'required|unique:email_templates,blade_name,'. $id,
            'email_template_texts'         => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validate();
        $email_template = EmailTemplate::withTrashed()->findOrFail($id);

        $email_template->name = $data['name'];
        $email_template->blade_name = $data['blade_name'];
        $email_template->save();

        if ($request->has('deleted_email_template_texts') && $request->deleted_email_template_texts){
            EmailTemplateText::destroy($request->deleted_email_template_texts);
        }

            foreach ($data['email_template_texts'] as $email_template_text) {

            if (isset($email_template_text['id']) && $email_template_text['id']) {
                EmailTemplateText::where('id', $email_template_text['id'])->update([
                    'key'               => $email_template_text['key'],
                    'value'             => $email_template_text['value'],
                    'email_template_id' => $id
                ]);
            } else {
                EmailTemplateText::create([
                    'key'               => $email_template_text['key'],
                    'value'             => $email_template_text['value'],
                    'email_template_id' => $id
                ]);
            }

        }

        return new EmailTemplateResource($email_template->load('email_template_texts'));
    }
    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $email_template = EmailTemplate::withTrashed()->findOrFail($id);

        $email_template->restore();

        return response()->json([
            'status'   => true,
            'faq_category' => new EmailTemplateResource($email_template),
            'message'  => 'Email Template has been restored successfully!'
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
        $email_template = EmailTemplate::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$email_template->trashed()){
            $email_template->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $email_template->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Email Template has been deleted successfully!'
        ], 200);
    }
}
