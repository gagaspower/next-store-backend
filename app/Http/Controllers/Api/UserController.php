<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::with('address')->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'message' => 'Success',
            'data'    => $data
        ], JsonResponse::HTTP_OK);
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
    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user           = new User();
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->roles    = $request->roles;
            $user->password = $request->password;
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Success',
                'data'    => $user
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::debug('Error created User : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! something wrong',
                'data'    => null
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $check = User::find($id);
        if (!$check) {
            return response()->json([
                'message' => "User doesn't exists",
                'data'    => null
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Success',
            'data'    => $check
        ], JsonResponse::HTTP_OK);
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
    public function update(UserRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            if ($request->password) {
                $user->password = $request->password;
            }

            $user->name  = $request->name;
            $user->email = $request->email;
            $user->roles = $request->roles;
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Success',
                'data'    => $user
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::debug('Error update User : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! something wrong',
                'data'    => null
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
            $check = User::find($id);
            if (!$check) {
                return response()->json([
                    'message' => "User doesn't exists",
                    'data'    => null
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            $check->delete();
            DB::commit();

            return response()->json([
                'message' => 'Data has been deleted!',
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::debug('Error delete User : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! something wrong'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
