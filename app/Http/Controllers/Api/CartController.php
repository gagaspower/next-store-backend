<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartTemp;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index($user_id)
    {
        $data = CartTemp::with('product', 'variants_stock', 'user')->where('user_id', $user_id)->get();

        return response()->json($data, JsonResponse::HTTP_OK);
    }

    public function createTemp(Request $request)
    {
        DB::beginTransaction();
        try {
            // cek if exists
            $data = CartTemp::where('product_id', $request->product_id)
                        ->where('user_id', $request->user_id)
                        ->where('product_variant_stock_id', $request->variant_id)
                        ->first();

            if ($data) {
                $data->product_qty += $request->product_qty;
                $data->save();
            } else {
                $data                           = new CartTemp();
                $data->cart_date                = Carbon::now();
                $data->product_qty              = $request->product_qty;
                $data->product_id               = $request->product_id;
                $data->product_variant_stock_id = $request->variant_id;
                $data->user_id                  = $request->user_id;
                $data->save();
            }

            DB::commit();

            return response()->json($data, JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCart($id)
    {
        DB::beginTransaction();
        try {
            $cart = CartTemp::find($id);
            if ($cart) {
                $cart->delete();
            }
            DB::commit();

            return response()->json(['message' => 'Cart has been delete'], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => 'Can`t delete cart'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCart(Request $request)
    {
        DB::beginTransaction();
        try {
            // cek if exists
            $data              = CartTemp::find($request->cartId);
            $data->product_qty = $request->qty;
            $data->save();

            DB::commit();

            return response()->json($data, JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json($th->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
