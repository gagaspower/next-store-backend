<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class Midtrans
{
    protected $clientId;
    protected $serverId;
    protected $url;
    protected array $header = [];
    protected $authorized;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $clientId = config('app.midtrans_client');
        $serverId = config('app.midtrans_server');
        $url = config('app.midtrans_url');
        $authorized = $serverId . ':';
        $header = [
            'Content-type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($authorized),
        ];

        $this->clientId = $clientId;
        $this->serverId = $serverId;
        $this->url      = $url;
        $this->header   = $header;
    }

    protected function apiCall(string $urlPath, array $payload = [], string $method = 'GET')
    {
        $url = $this->url . '/' . ltrim($urlPath, '/');

        return Http::withHeaders($this->header)->{strtolower($method)}($url, $payload);
    }

    public function createPayment(array $payload = [])
    {
        return $this->apiCall('/charge', $payload, 'POST');
    }
}
