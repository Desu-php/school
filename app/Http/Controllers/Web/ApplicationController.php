<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\SendApplicationEmail;
use App\Models\Application;
use App\Models\Contacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'text' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $contact = Application::create($request->all());
        dispatch(new SendApplicationEmail($contact));

        if ($contact) {
            return response()->json([
                'status' => true,
                'message' => 'Your message has been sent successfully!'
            ], 200);
        }
    }
}
