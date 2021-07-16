<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscribeNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

class SubscribeNewsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:subscribe_news'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'email'
        );
        $SPApiClient = new ApiClient(env('SENDPULSE_API_USER_ID', 'b12601c6c3f861ec6c7d4629c2dbc81c'), env('SENDPULSE_API_SECRET', '1384b902b9b87fb2375f825ed8472820'), new FileStorage());


        $books = $SPApiClient->listAddressBooks();

        $bookID = $books[0]->id;

        $emails = array(
            array(
                'email' => $data['email'],
                'variables' => array(
                    'name' => $data['name'],
                )
            )
        );

        $SPApiClient->addEmails($bookID, $emails);

        $subscribe = SubscribeNews::create($data);

        return $subscribe;
    }
}
