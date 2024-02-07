<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kota;
use App\Models\Provinsi;
use App\Service\RajaOngkir;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NusantaraController extends Controller
{
    private $rajaongkir;

    public function __construct()
    {
        $rajaongkir = new RajaOngkir();

        $this->rajaongkir = $rajaongkir;
    }

    public function province()
    {
        $insert_data = [];
        $data        = $this->rajaongkir->getProvinsi();

        foreach ($data['rajaongkir']['results'] as $d) {
            $insert_data[] = [
                'province_id'   => $d['province_id'],
                'province_name' => $d['province']
            ];
        }

        DB::beginTransaction();
        try {
            Provinsi::upsert($insert_data, ['province_id'], [
                'province_name',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Data province has been saved'
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::debug('Error insert data province : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! something wrong'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function kota()
    {
        $city_insert = [];
        $data        = $this->rajaongkir->getKota();

        foreach ($data['rajaongkir']['results'] as $c) {
            $city_insert[] = [
                'city_id'          => $c['city_id'],
                'city_name'        => $c['city_name'],
                'city_postal_code' => $c['postal_code'],
                'city_province_id' => $c['province_id'],
            ];
        }

        DB::beginTransaction();
        try {
            Kota::upsert($city_insert, ['city_id'], [
                'city_name',
                'city_postal_code',
                'city_province_id'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Data Kota has been saved'
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::debug('Error insert data kota : ' . $th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Whoops! something wrong'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCost(Request $request)
    {
        $data = $this->rajaongkir->getCost($request->destination, $request->weight, $request->courier);

        return response()->json($data);
    }

    public function showAllProv()
    {
        $data = Provinsi::all();

        return response()->json($data, JsonResponse::HTTP_OK);
    }

    public function showAllKota($provinsi_id)
    {
        $data = Kota::where('city_province_id', $provinsi_id)->get();

        return response()->json($data, JsonResponse::HTTP_OK);
    }
}
