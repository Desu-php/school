<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;

class CurrencyController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $currencies = Currency::withTrashed()->orderBy('is_main', "desc")->orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $currencies->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $currencies = $currencies->take($take)->skip($skip);
        } else {
            $currencies = $currencies->take($take)->skip(0);
        }

        return response()->json([
            'data' => CurrencyResource::collection($currencies->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     * @return CurrencyResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'code'   => 'required',
            'symbol' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $currencies = Currency::create($validator->validate());

        return new CurrencyResource($currencies);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $currency = Currency::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new CurrencyResource($currency)
        ], 200);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'code'   => 'required',
            'symbol' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        Currency::withTrashed()->where('id', $id)->update($validator->validate());

        return $this->show($id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $currency = Currency::withTrashed()->findOrFail($id);

        $currency->restore();

        return response()->json([
            'status'   => true,
            'data' => new CurrencyResource($currency),
            'message'  => 'Currency been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $currency = Currency::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$currency->trashed()){
            $currency->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $currency->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Currency has been deleted successfully!'
        ], 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setIsMain($id)
    {
        $currency = Currency::withTrashed()->findOrFail($id);
        Currency::where('is_main', true)->update(['is_main' => false]);

        $currency->is_main = true;
        $currency->save();

        return response()->json([
            'status' => true,
            'message' => $currency->name . ' is set main currency successfully!'
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateExchangeRate()
    {
        Artisan::call("check:exchange-rate");

        return response()->json([
            'status' => true,
            'message' => 'Exchange Rate is updaited successfully!'
        ], 200);
    }
}
