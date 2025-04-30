<?php

namespace App\Integrations\YandexDostavka;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class YandexDeliveryService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct($integrationId)
    {
        $this->baseUrl = config('yandex_delivery.base_url');
        $this->token = config('yandex_delivery.oauth_token');
    }

    /**
     * Базовый метод отправки запроса
     */
    protected function request(string $method, string $endpoint, array $data = [], array $query = [])
    {
        $response = Http::withToken($this->token)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->{$method}($endpoint, $method === 'get' ? ['query' => $query] : $data);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json();
    }

    /**
     * Пример запроса списка курьеров
     */
    public function getCouriersList(array $query = [])
    {
        return $this->request('get', '/v1/couriers/list', [], $query);
    }

    /**
     * Пример создания заявки на доставку
     */
    public function createOrder(array $payload)
    {
        return $this->request('post', '/v1/claims/create', $payload);
    }
}