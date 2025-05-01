<?php

namespace App\Integrations\YandexDostavka;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class YandexDeliveryService
{
    protected string $baseUrl, $token, $pickUpPoint;

    public function __construct($baseUrl, $token, $pickUpPoint)
    {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
        $this->pickUpPoint = $pickUpPoint;
    }

    /**
     * Базовый метод отправки запроса
     */
    protected function request(string $method, string $endpoint, $data = [], array $query = [])
    {
        $response = Http::withToken($this->token)
            ->baseUrl($this->baseUrl)
            ->acceptJson();

        if ($method === 'get') {
            $response = $response->get($endpoint, ['query' => $query]);
        } else {
            $jsonData = json_encode($data, JSON_THROW_ON_ERROR);
            $response = $response->withHeaders([
                'Content-Type' => 'application/json',
            ])->withBody($jsonData, 'application/json')->send($method, $endpoint);
        }

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

    /**
     * Получение списка пунктов самовывоза
     */
    public function getPickupPoints(array $filters = []): array
    {
        $payload = [
            'latitude' => $filters['latitude'] ?? ['from' => 55.751310, 'to' => 55.886191],
            'longitude' => $filters['longitude'] ?? ['from' => 37.584622, 'to' => 37.588269],
            'page' => $filters['page'] ?? 1,
            'page_size' => $filters['page_size'] ?? 100
        ];

        return $this->request('post', '/b2b/platform/pickup-points/list', $payload);
    }
    /**
     * Получение списка городов на основе ПВЗ
     */
    public function getPickupCities(array $filters = []): array
    {
        $response = $this->getPickupPoints($filters);

        return collect($response['points'] ?? [])
            ->pluck('address.locality')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }


    public function getGeoIdByAddress(string $address): ?int
    {
        $response = Http::withToken($this->token)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->post('/b2b/platform/location/detect', ['location' => $address]);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json('geo_id');
    }

    public function getPickupPointsByGeoId(int $geoId, int $page = 1, int $pageSize = 100): array
    {
        $payload = [
            'geo_id' => $geoId,
            'page' => $page,
            'page_size' => $pageSize
        ];

        return $this->request('post', '/b2b/platform/pickup-points/list', $payload);
    }
}