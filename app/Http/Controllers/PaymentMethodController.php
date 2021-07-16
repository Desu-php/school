<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function index(Request $request)
    {
        $payment_methods = PaymentMethod::withTrashed()->orderBy('sort', "asc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $payment_methods->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $payment_methods = $payment_methods->take($take)->skip($skip);
        } else {
            $payment_methods = $payment_methods->take($take)->skip(0);
        }

        return response()->json([
            'data' => PaymentMethodResource::collection($payment_methods->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required',
            'description' => 'required',
            'image'       => 'required|mimes:jpg,jpeg,png,bmp',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = $validator->validate();


        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/payment-methods';
            $file->storeAs($destinationPath, $imageName);
            $data['image'] = $imageName;
        }

        $payment_method = PaymentMethod::create($data);

        return response()->json([
            'data' => new PaymentMethodResource($payment_method)
        ], 200);
    }

    /**
     * Display the specified resource.
     * @param $id
     *
     * @return PaymentMethodResource
     */
    public function show($id)
    {
        $payment_method = PaymentMethod::withTrashed()->findOrFail($id);
        return new PaymentMethodResource($payment_method);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = $validator->validate();
        $payment_method = PaymentMethod::findOrFail($id);

        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/payment-methods';
            $file->storeAs($destinationPath, $imageName);
            $payment_method->image = $imageName;
        }

        $payment_method->name = $data['name'];
        $payment_method->description = $data['description'];

        $payment_method->save();

        return response()->json([
            'data' => new PaymentMethodResource($payment_method)
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSort(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data.*.sort' => 'required',
            'data.*.id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        foreach ($request->data as $data) {
            PaymentMethod::where('id', $data['id'])->update(['sort' => $data['sort']]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment Method successfully updated!',
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $payment_method = PaymentMethod::withTrashed()->find($id);

        $payment_method->restore();

        return response()->json([
            'status'   => true,
            'data' => new PaymentMethodResource($payment_method),
            'message'  => 'Payment Method has been restored successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {

        $payment_method = PaymentMethod::withTrashed()->find($id);

        $deleteType = null;

        if(!$payment_method->trashed()){
            $payment_method->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $payment_method->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Payment Method has been deleted successfully!'
        ], 200);
    }
}
