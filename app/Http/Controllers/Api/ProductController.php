<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductVarian;
use App\Models\ProductVarianStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    protected $path;

    public function __construct()
    {
        $this->path = 'uploads/product/';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::with('category')->orderBy('id', 'desc')->paginate(10);

        return response()->json(['message' => 'Success', 'data' => $data], JsonResponse::HTTP_OK);
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
    public function store(ProductRequest $request)
    {
        $varian_data      = json_decode($request->product_varian, true);
        $varian_stok_data = json_decode($request->product_varian_stock, true);
        DB::beginTransaction();
        try {
            if ($request->file('product_image')) {
                $file     = $request->file('product_image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($this->path), $filename);
            }

            $product                      = new Product();
            $product->product_name        = $request->product_name;
            $product->product_sku         = $request->product_sku;
            $product->product_category_id = $request->product_category_id;
            $product->product_desc        = $request->product_desc;
            $product->product_stock       = $request->product_stock ?? 0;
            $product->product_price       = $request->product_price ?? 0;
            $product->product_weight      = $request->product_weight;
            $product->user_id             = 4;
            $product->product_image       = $filename;
            $product->isVarian            = $request->isVarian === 'true' ? true : false;
            $product->save();

            if ($request->isVarian === 'true') {
                foreach ($varian_data as $v) {
                    $varian               = new ProductVarian();
                    $varian->varian_group = $v['varian_group'];
                    $varian->varian_item  = $v['varian_item'];
                    $varian->product_id   = $product->id;
                    $varian->save();
                }

                foreach ($varian_stok_data as $varianStock) {
                    $varian_stock                       = new ProductVarianStock();
                    $varian_stock->product_varian_name  = $varianStock['product_varian_name'];
                    $varian_stock->product_varian_stock = $varianStock['product_varian_stock'];
                    $varian_stock->product_varian_price = $varianStock['product_varian_price'];
                    $varian_stock->product_varian_sku   = $varianStock['product_varian_sku'];
                    $varian_stock->product_id           = $product->id;
                    $varian_stock->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product has been created',
                'data'    => $product
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => $th->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Product::with('category', 'variants', 'variants_stock')->where('id', $id)->first();

        if (!$data) {
            return response()->json(['message' => 'Product does not exists'], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Success', 'data' => $data], JsonResponse::HTTP_OK);
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
    public function update(ProductRequest $request, string $id)
    {
        $varian_data      = json_decode($request->product_varian, true);
        $varian_stok_data = json_decode($request->product_varian_stock, true);
        DB::beginTransaction();
        try {
            if ($request->file('product_image')) {
                $file     = $request->file('product_image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($this->path), $filename);
            }

            $product = Product::find($id);

            if ($product->product_image) {
                if ($request->file('product_image')) {
                    $old_image = $this->path . '/' . $product->product_image;
                    if (File::exists($old_image)) {
                        // jalankan hapus file
                        File::delete($old_image);
                    }

                    $product->product_image = $filename;
                }
            }

            $product->product_name        = $request->product_name;
            $product->product_sku         = $request->product_sku;
            $product->product_category_id = $request->product_category_id;
            $product->product_desc        = $request->product_desc;
            $product->product_stock       = $request->product_stock ?? 0;
            $product->product_price       = $request->product_price ?? 0;
            $product->product_weight      = $request->product_weight;
            $product->user_id             = 4;
            $product->isVarian            = $request->isVarian === 'true' ? true : false;
            $product->save();

            // remove old product varian and product varian stock
            ProductVarian::where('product_id', $id)->delete();
            ProductVarianStock::where('product_id', $id)->delete();

            if ($request->isVarian === 'true') {
                foreach ($varian_data as $v) {
                    $varian               = new ProductVarian();
                    $varian->varian_group = $v['varian_group'];
                    $varian->varian_item  = $v['varian_item'];
                    $varian->product_id   = $product->id;
                    $varian->save();
                }

                foreach ($varian_stok_data as $varianStock) {
                    $varian_stock                       = new ProductVarianStock();
                    $varian_stock->product_varian_name  = $varianStock['product_varian_name'];
                    $varian_stock->product_varian_stock = $varianStock['product_varian_stock'];
                    $varian_stock->product_varian_price = $varianStock['product_varian_price'];
                    $varian_stock->product_varian_sku   = $varianStock['product_varian_sku'];
                    $varian_stock->product_id           = $product->id;
                    $varian_stock->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product has been update',
                'data'    => $product
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => $th->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Product does not exits'], JsonResponse::HTTP_NOT_FOUND);
            }

            /** jika produk ada dan ada product image maka unlink pada folder */
            if ($product && $product->product_image) {
                $old_image = $this->path . '/' . $product->product_image;
                if (File::exists($old_image)) {
                    // jalankan hapus file
                    File::delete($old_image);
                }
            }
            ProductVarian::where('product_id', $id)->delete();
            ProductVarianStock::where('product_id', $id)->delete();
            $product->delete();

            DB::commit();

            return response()->json([
                'message' => 'Product has been delete'
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete product',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
