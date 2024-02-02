<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessOrder;
use App\Mail\OrderEmail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderExpedition;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\ProductVarianStock;
use App\Models\User;
use App\Service\Midtrans;
use App\Traits\GenerateCode;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{
    use GenerateCode;

    protected $payment;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $payment = new Midtrans();

        $this->payment = $payment;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $orderQuery = Order::with('orders_detail.product', 'expedisi', 'payment_bank', 'user');
        if ($user->roles == 'user') {
            $order = $orderQuery->where('orders.user_id', $user->id)->get();
        } else {
            $order = $orderQuery->get();
        }

        return response()->json(['data' => $order], JsonResponse::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // define cart request
        $carts                = $request->carts;
        $expedition_name      = $request->expedition_name;
        $expedition_service   = $request->expedition_service;
        $expedition_price     = $request->expedition_price;
        $expedition_estimated = $request->expedition_estimated;
        $total_weight         = $request->total_weight;
        $total_price          = $request->total_price;

        $kode = $this->getKode();

        DB::beginTransaction();
        try {
            // insert for orders table

            $orders                     = new Order();
            $orders->order_date         = Carbon::now();
            $orders->order_code         = $kode;
            $orders->order_amount       = $total_price;
            $orders->order_status       = 'wait for payment';
            $orders->order_total_weight = $total_weight;
            $orders->user_id            = Auth::user()->id;
            $orders->save();

            if ($orders) {
                // store detail order for item list
                foreach ($carts as $cart) {
                    // searching product price
                    $price = 0;
                    if (isset($cart['cart_product_variant_id']) && $cart['cart_product_variant_id'] != null && $cart['cart_product_variant_id'] != 'undefined') {
                        $variasi = ProductVarianStock::find($cart['cart_product_variant_id']);
                        $price   = $variasi->product_varian_price;

                        /* update stock variant */
                        $variasiUpdate                        = ProductVarianStock::lockForUpdate()->find($cart['cart_product_variant_id']);
                        $variasiUpdate->product_varian_stock -= $cart['cart_product_qty'];
                        $variasiUpdate->save();
                    } else {
                        $product = Product::find($cart['cart_product_id']);
                        $price   = $product->product_price;

                        $productUpdate                 = Product::lockForUpdate()->find($cart['cart_product_id']);
                        $productUpdate->product_stock -= $cart['cart_product_qty'];
                        $productUpdate->save();
                    }

                    $details                     = new OrderDetail();
                    $details->order_id           = $orders->id;
                    $details->product_id         = $cart['cart_product_id'];
                    $details->product_qty        = $cart['cart_product_qty'];
                    $details->product_variant_id = $cart['cart_product_variant_id'] ?? 0;
                    $details->product_price      = $price;
                    $details->save();
                }

                // store expedition data
                $exp                       = new OrderExpedition();
                $exp->order_id             = $orders->id;
                $exp->expedition_name      = $expedition_name;
                $exp->expedition_service   = $expedition_service;
                $exp->expedition_price     = $expedition_price;
                $exp->expedition_estimated = $expedition_estimated;
                $exp->save();
            }

            $payment      = $this->create_va($kode, $total_price, $request->bank);
            $responseBody = json_decode($payment->getBody(), true);

            // // store payment data into database
            $pay                         = new OrderPayment();
            $pay->order_id               = $orders->id;
            $pay->order_bank             = $request->bank;
            $pay->payment_transaction_id = $responseBody['transaction_id'];
            $pay->payment_provider       = 'Midtrans';
            $pay->payment_merchant_id    = $responseBody['merchant_id'];
            $pay->payment_gross_amount   = $responseBody['gross_amount'];
            $pay->payment_type           = $request->bank == 'permata' ? 'permata' : 'bank_transfer';
            $pay->payment_datetime       = $responseBody['transaction_time'];
            $pay->payment_expired        = $responseBody['expiry_time'];
            $pay->payment_status         = $responseBody['transaction_status'];
            $pay->payment_va_numbers     = $responseBody['va_numbers'][0]['va_number'];
            $pay->save();

            $result = [
                'orders'         => $orders,
                'payment_result' => $pay
            ];

            $this->generatePdf($kode);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order has been created ' . $orders->order_code,
                'data'    => $result
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Error when created order : ' . $th->getMessage(),
                'data'    => null
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $invoce)
    {
        $orderQuery = Order::with(['orders_detail.product' => function ($query) {
            $query->select('id', 'product_name');
        }, 'orders_detail.product_variants' => function ($query) {
            $query->select('id', 'product_varian_name');
        }, 'expedisi', 'payment_bank', 'user.address' => function ($query) {
            $query->where('isDefault', true);
        }, 'user.address.provinsi', 'user.address.kota'])->where('order_code', $invoce)->first();

        return response()->json(['data' => $orderQuery], JsonResponse::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function create_va(string $kode, int $total = 0, string $bank)
    {
        $transaction_details = array(
            'order_id'     => $kode,
            'gross_amount' => $total + 4500
        );

        $bank_transfer = array(
            'bank' => $bank
        );

        $customer_details = array(
            'first_name' => Auth::user()->name,
            'email'      => Auth::user()->email
        );

        /* seting waktu expired va number ke 1 hari */
        $custom_expiry = array(
            'order_time'      => date('Y-m-d H:m:s O'),
            'expiry_duration' => 1,
            'unit'            => 'day'  // minute, hour, day
        );

        $payload = array(
            'payment_type'        => $bank == 'permata' ? 'permata' : 'bank_transfer',
            'transaction_details' => $transaction_details,
            'custom_expiry'       => $custom_expiry,
            'bank_transfer'       => $bank_transfer,
            'customer_details'    => $customer_details
        );

        return $this->payment->createPayment($payload);
    }

    public function generatePdf($kode)
    {
        $orderQuery = Order::with(['orders_detail.product' => function ($query) {
            $query->select('id', 'product_name');
        }, 'orders_detail.product_variants' => function ($query) {
            $query->select('id', 'product_varian_name');
        }, 'expedisi', 'payment_bank', 'user.address' => function ($query) {
            $query->where('isDefault', true);
        }, 'user.address.provinsi', 'user.address.kota'])
                          ->where('order_code', $kode)
                          ->first();

        ProcessOrder::dispatch($orderQuery);
    }
}
