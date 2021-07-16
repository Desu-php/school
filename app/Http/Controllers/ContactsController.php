<?php

namespace App\Http\Controllers;

use App\Http\Resources\InterestingResource;
use App\Models\Contacts;
use App\Models\Interesting;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $contacts = Contacts::orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $contacts->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $contacts = $contacts->take($take)->skip($skip);
        } else {
            $contacts = $contacts->take($take)->skip(0);
        }

        return response()->json([
            'data' =>  $contacts->get(),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $contact = Contacts::findOrFail($id);

        if ($contact) {
            return response()->json([
                'data' => $contact,
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
