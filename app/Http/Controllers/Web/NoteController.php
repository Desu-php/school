<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $notes = Note::orderBy('id', "desc")->where('user_id', auth('api')->id());
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 15;
        $count = $notes->count();


        if ($page) {
            $skip = $take * ($page - 1);
            $notes = $notes->take($take)->skip($skip);
        } else {
            $notes = $notes->take($take)->skip(0);
        }

        return response()->json([
            'data' => NoteResource::collection($notes->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'title'     => 'required|string',
            'text'       => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data['user_id'] = auth('api')->id();

        $note = Note::create($data);

        return response()->json([
            'data' => new NoteResource($note)
        ], 200);
    }

    /**
     * @param Note $note
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Note $note)
    {
        return response()->json([
            'data' => new NoteResource($note)
        ], 200);
    }

    /**
     * @param Request $request
     * @param Note    $note
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Note $note)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'title'     => 'required|string',
            'text'       => 'required|string'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $note->title = $data['title'];
        $note->text = $data['text'];
        $note->save();

        return response()->json([
            'data' => new NoteResource($note)
        ], 200);
    }

    /**
     * @param Note $note
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return response()->json([
            'status' => true,
            'message' => 'Note has been deleted successfully!'
        ], 200);
    }
}
