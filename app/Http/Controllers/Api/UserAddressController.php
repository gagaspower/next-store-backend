<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddressRequest;
use App\Models\User;
use App\Models\UsersAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = UsersAddress::with('user', 'provinsi', 'kota')->paginate(10);

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
    public function store(UserAddressRequest $request)
    {
        DB::beginTransaction();
        try {
            $address                       = new UsersAddress();
            $address->address              = $request->address;
            $address->user_address_prov_id = $request->user_address_prov_id;
            $address->user_address_kab_id  = $request->user_address_kab_id;
            $address->user_address_kodepos = $request->user_address_kodepos;
            $address->user_id              = 3;
            $address->save();

            DB::commit();

            return response()->json([
                'message' => 'Success',
                'data'    => $address
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::debug('Error created User Address : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! Something wrong',
                'data'    => null
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = UsersAddress::with('user')->find($id);

        return response()->json([
            'message' => 'Success',
            'data'    => $data
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
    public function update(UserAddressRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $address                       = UsersAddress::find($id);
            $address->address              = $request->address;
            $address->user_address_prov_id = $request->user_address_prov_id;
            $address->user_address_kab_id  = $request->user_address_kab_id;
            $address->user_address_kodepos = $request->user_address_kodepos;
            $address->user_id              = Auth::user()->id;
            $address->save();

            DB::commit();

            return response()->json([
                'message' => 'Success',
                'data'    => $address
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::debug('Error update User Address : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! Something wrong',
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
            $address = UsersAddress::find($id);

            if (!$address) {
                return response()->json([
                    'message' => 'Address ID Does not found',
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            $address->delete();
            DB::commit();

            return response()->json([
                'message' => 'User Address has been delete',
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::debug('Error Delete User Address : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! Something wrong',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * for public data
     */
    public function getUserAddress(User $user)
    {
        $data = UsersAddress::with('user', 'provinsi', 'kota')
                    ->where('user_id', Auth::user()->id)
                    ->get();

        return response()->json([
            'message' => 'Success',
            'data'    => $data
        ], JsonResponse::HTTP_OK);
    }

    public function setDefaultAddress(Request $request)
    {
        DB::commit();
        try {
            $data = DB::table('users_address')
                        ->where('id', $request->addressId)
                        ->update([
                            'isDefault' => true
                        ]);

            if ($data) {
                DB::table('users_address')
                    ->where('user_id', Auth::user()->id)
                    ->where('id', '<>', $request->addressId)
                    ->update([
                        'isDefault' => false
                    ]);
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Default address has been set'], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['status' => false, 'message' => $th->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
