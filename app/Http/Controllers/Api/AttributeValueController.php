<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttributeValues;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = AttributeValues::with('attribute')->get();

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
                'attribute_id' => 'required',
                'value'        => 'required'
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => 'validation error', 'errors' => $validation->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $attribute               = new AttributeValues();
            $attribute->attribute_id = $request->attribute_id;
            $attribute->value        = $request->value;
            $attribute->save();

            DB::commit();

            return response()->json([
                'message' => 'Attribute has ben save',
                'data'    => $attribute
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function showByAttribute(string $id)
    {
        $attribute = AttributeValues::where('attribute_id', $id)->get();
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
                'attribute_id' => 'required',
                'value'        => 'required'
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => 'validation error', 'errors' => $validation->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $attribute               = AttributeValues::find($id);
            $attribute->attribute_id = $request->attribute_id;
            $attribute->value        = $request->value;
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
            $attribute = AttributeValues::find($id);
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
