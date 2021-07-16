<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $payment_methods = PaymentMethod::orderBy('sort', "asc")->get();

        return response()->json([
            'data' => PaymentMethodResource::collection($payment_methods),
        ], 200);
    }
}
