<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $currencies = Currency::orderBy('is_main', "desc")->orderBy('id', "desc");

        return response()->json([
            'data' => CurrencyResource::collection($currencies->get()),
        ], 200);
    }
}
