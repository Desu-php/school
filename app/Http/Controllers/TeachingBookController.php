<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeachingBookResource;
use App\Models\TeachingBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeachingBookController extends Controller
{
    /**
     * Display a listing of the books.
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $teaching_books = TeachingBook::withTrashed()->orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $teaching_books->count();


        if ($page) {
            $skip = $take * ($page - 1);
            $teaching_books = $teaching_books->take($take)->skip($skip);
        } else {
            $teaching_books = $teaching_books->take($take)->skip(0);
        }

        return response()->json([
            'data' => TeachingBookResource::collection($teaching_books->get()),
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        }


        $data = $request->all();

        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/teaching-book/image';
            $file->storeAs($destinationPath, $imageName);
            $data['image'] = $imageName;
        }

        if ($request->has('file') && $request->file) {
            $file = $request->file;

            $fileName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/teaching-book/file';
            $file->storeAs($destinationPath, $fileName);
            $data['file'] = $fileName;
        }

        if ($request->has('audio') && $request->audio) {
            $file = $request->audio;

            $audioName = time().'-'.uniqid().'.'. $file->extension();
            $destinationPath = 'public/teaching-book/audio';
            $file->storeAs($destinationPath, $audioName);
            $data['audio'] = $audioName;
        }


        $teaching_book = TeachingBook::create($data);

        return response()->json([
            'data' => new TeachingBookResource($teaching_book)
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $teaching_book = TeachingBook::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new TeachingBookResource($teaching_book)
        ], 200);
    }


    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        }
        $data = $request->all();

        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/teaching-book/image';
            $file->storeAs($destinationPath, $imageName);
            $data['image'] = $imageName;
        }

        if ($request->has('file') && $request->file) {
            $file = $request->file;
            $fileName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/teaching-book/file';
            $file->storeAs($destinationPath, $fileName);
            $data['file'] = $fileName;
        }

        if ($request->has('audio') && $request->audio) {
            $file = $request->audio;
            $audioName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/teaching-book/audio';
            $file->storeAs($destinationPath, $audioName);
            $data['audio'] = $audioName;
        }

        TeachingBook::where('id', $id)->update($data);

        return $this->show($id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $teaching_book = TeachingBook::withTrashed()->findOrFail($id);

        $teaching_book->restore();

        return response()->json([
            'status'   => true,
            'data' => new TeachingBookResource($teaching_book),
            'message'  => 'Book been restored successfully!'
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
        $teaching_book = TeachingBook::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$teaching_book->trashed()){
            $teaching_book->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $teaching_book->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Book has been deleted successfully!'
        ], 200);
    }
}
