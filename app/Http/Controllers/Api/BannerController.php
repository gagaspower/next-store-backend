<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    protected $path;

    public function __construct()
    {
        $this->path = 'uploads/banner/';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Banner::all();

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
    public function store(BannerRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($request->file('banner_image')) {
                $file     = $request->file('banner_image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($this->path), $filename);
            }

            $banner               = new Banner();
            $banner->banner_title = $request->banner_title;
            $banner->banner_desc  = $request->banner_desc;
            $banner->banner_url   = $request->banner_url;
            $banner->banner_image = $filename;
            $banner->save();

            DB::commit();

            return response()->json([
                'message' => 'Banner has been created',
                'data'    => $banner
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! something wrong',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cat = Banner::find($id);
        if (!$cat) {
            return response()->json(['message' => 'Banner does not exists'], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Success', 'data' => $cat], JsonResponse::HTTP_OK);
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
    public function update(BannerRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            if ($request->file('banner_image')) {
                $file     = $request->file('banner_image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($this->path), $filename);
            }

            $banner = Banner::find($id);

            if ($banner->banner_image) {
                if ($request->file('banner_image')) {
                    $old_image = $this->path . '/' . $banner->banner_image;
                    if (File::exists($old_image)) {
                        // jalankan hapus file
                        File::delete($old_image);
                    }

                    $banner->banner_image = $filename;
                }
            }

            $banner->banner_title = $request->banner_title;
            $banner->banner_desc  = $request->banner_desc;
            $banner->banner_url   = $request->banner_url;
            $banner->save();

            DB::commit();

            return response()->json([
                'message' => 'Banner has been update',
                'data'    => $banner
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! something wrong',
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
            $banner = Banner::find($id);

            if (!$banner) {
                return response()->json(['message' => 'Product does not exits'], JsonResponse::HTTP_NOT_FOUND);
            }

            /** jika produk ada dan ada product image maka unlink pada folder */
            if ($banner && $banner->banner_image) {
                $old_image = $this->path . '/' . $banner->banner_image;
                if (File::exists($old_image)) {
                    // jalankan hapus file
                    File::delete($old_image);
                }
            }

            $banner->delete();

            DB::commit();

            return response()->json([
                'message' => 'Banner has been delete'
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete banner',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
