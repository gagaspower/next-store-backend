<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class RajaOngkir
{
    protected $key;
    protected $url;
    protected $origin;

    public function __construct()
    {
        $key    = config('app.rajaongkir_key');
        $url    = config('app.rajaongkir_url');
        $origin = config('app.rajaongkir_origin');

        $this->url    = $url;
        $this->key    = $key;
        $this->origin = $origin;
    }

    public function getProvinsi()
    {
        $response = Http::withHeaders(['key' => $this->key])->get($this->url . '/province');
        $data     = json_decode($response->body(), true);

        return $data;
    }

    public function getKota()
    {
        $response = Http::withHeaders(['key' => $this->key])->get($this->url . '/city');
        $data     = json_decode($response->body(), true);

        return $data;
    }

    public function getCost($destination, $weight, $courier)
    {
        $response = Http::withHeaders(['key' => $this->key])->post($this->url . '/cost', [
            'origin'      => $this->origin,
            'destination' => $destination,
            'weight'      => $weight,
            'courier'     => $courier,
        ]);
        $data = json_decode($response->body(), true);

        return $data;
    }
}
