<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class RajaOngkir {

    protected $key;
    protected $url;


    public function __construct(){

        $key = config('app.rajaongkir_key');
        $url = config('app.rajaongkir_url');

        $this->url = $url;
        $this->key = $key;

    }

    public function getProvinsi(){
                $response = Http::withHeaders(['key' => $this->key])->get($this->url.'/province');
            $data = json_decode($response->body(), true);
             return $data;
    }

    public function getKota(){
           $response = Http::withHeaders(['key' => $this->key])->get($this->url.'/city');
        $data = json_decode($response->body(), true);
        return $data;
    }

}