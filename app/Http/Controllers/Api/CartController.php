<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartTemp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $data = CartTemp::with('product', 'variants_stock', 'user')->get();

        return response()->json($data, JsonResponse::HTTP_OK);
    }

    public function createTemp(Request $request) {}
}
