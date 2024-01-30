<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    protected $path;

    public function __construct()
    {
        $this->path = 'uploads/category';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::all();

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
    public function store(CategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $cat                = new Category();
            $cat->category_name = $request->category_name;
            if ($request->file('category_image')) {
                $file     = $request->file('category_image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($this->path), $filename);
                $cat->category_image = $filename;
            }
            $cat->save();

            DB::commit();

            return response()->json([
                'message' => 'Data has been created',
                'data'    => $cat
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create data',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return response()->json(['message' => 'Category does not exists'], JsonResponse::HTTP_NOT_FOUND);
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
    public function update(CategoryRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $cat = Category::find($id);

            if ($cat->category_image) {
                if ($request->file('category_image')) {
                    $old_image = $this->path . '/' . $cat->category_image;
                    if (File::exists($old_image)) {
                        // jalankan hapus file
                        File::delete($old_image);
                    }
                }
            }

            if ($request->file('category_image')) {
                // new file
                $file     = $request->file('category_image');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($this->path), $filename);
                $cat->category_image = $filename;
            }

            $cat->category_name = $request->category_name;
            $cat->save();

            DB::commit();

            return response()->json([
                'message' => 'Data has been update',
                'data'    => $cat
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update data',
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
            $cat = Category::find($id);

            if (!$cat) {
                return response()->json(['message' => 'Category does not exists'], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($cat && $cat->category_image) {
                $old_image = $this->path . '/' . $cat->category_image;
                if (File::exists($old_image)) {
                    // jalankan hapus file
                    File::delete($old_image);
                }
            }

            $cat->delete();

            DB::commit();

            return response()->json([
                'message' => 'Data has been delete'
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete data',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCategoryBySlug(string $slug)
    {
        $cat = Category::slug($slug)->first();
        if (!$cat) {
            return response()->json(['message' => 'Category does not exists'], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Success', 'data' => $cat], JsonResponse::HTTP_OK);
    }
}
