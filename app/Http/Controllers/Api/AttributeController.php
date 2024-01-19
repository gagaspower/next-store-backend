<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Attribute::with('attribute_values')->get();

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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                'attribute_name' => 'required'
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => 'validation error', 'errors' => $validation->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $attribute                 = new Attribute();
            $attribute->attribute_name = $request->attribute_name;
            $attribute->save();

            DB::commit();

            return response()->json([
                'message' => 'Attribute has ben save',
                'data'    => $attribute
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create product attribute',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attribute = Attribute::find($id);
        if (!$attribute) {
            return response()->json(['message' => 'Attribute does not exists'], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json($attribute, JsonResponse::HTTP_OK);
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
        DB::beginTransaction();
        try {
            $validation = Validator::make($request->all(), [
                'attribute_name' => 'required'
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => 'validation error', 'errors' => $validation->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $attribute                 = Attribute::find($id);
            $attribute->attribute_name = $request->attribute_name;
            $attribute->save();

            DB::commit();

            return response()->json([
                'message' => 'Attribute has ben save',
                'data'    => $attribute
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update product attribute',
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
            $attribute = Attribute::find($id);
            if (!$attribute) {
                return response()->json(['message' => 'Attribute does not exists'], JsonResponse::HTTP_NOT_FOUND);
            }
            $attribute->delete();

            DB::commit();

            return response()->json([
                'message' => 'Attribute has ben delete'
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete product attribute',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
