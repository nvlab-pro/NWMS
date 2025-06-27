<?php

namespace App\Integrations\YandexDostavka;

use App\Models\rwOrder;
use App\Services\CustomTranslator;
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

    // **************************************************
    // *** Загружаем заказ в сервис доставки
    public function uploadOrderToDeliveryService(rwOrder $order): ?array
    {

        $items = [];
        foreach ($order->offers as $offer) {
            $items[] = [
                'count' => $offer->oo_qty,
                'name' => $offer->offer->of_name ?? 'Товар',
                'article' => $offer->offer->of_article ?? '',
                'uin' => $offer->offer->of_article ?? '',
                'billing_details' => [
                    'inn' => '5048036702',
                    'nds' => 20,
                    'unit_price' => $offer->oo_price ?? 0,
                    'assessed_unit_price' => $offer->oo_oc_price ?? 0
                ],
                'physical_dims' => [
                    'dx' => $offer->of_dimension_x ?? 0,
                    'dy' => $offer->of_dimension_y ?? 0,
                    'dz' => $offer->of_dimension_z ?? 0,
                    'predefined_volume' => 0
                ],
                'place_barcode' => '1011*' . $order->o_id . '*' . (101 * $order->o_id),
            ];
        }

        $payload = [
            'info' => [
                'operator_request_id' => (string)$order->o_id . '2',
                'comment' => ''
            ],
            'source' => [
                'platform_station' => [
                    'platform_id' => $this->pickUpPoint
                ]
            ],
            'destination' => [
                'type' => 'platform_station',
                'platform_station' => [
                    'platform_id' => $order->getDs->ods_ds_pp_id
                ]
            ],
            'items' => $items,
            'places' => [[
                'physical_dims' => [
                    'weight_gross' => $order->weight ?? 0,
                    'dx' => $order->dimension_x ?? 0,
                    'dy' => $order->dimension_y ?? 0,
                    'dz' => $order->dimension_z ?? 0,
                    'predefined_volume' => 0
                ],
                'barcode' => '1011*' . $order->o_id . '*' . (101 * $order->o_id),
                'description' => 'Упаковка заказа '
            ]],
            'billing_info' => [
                'payment_method' => 'already_paid',
                'delivery_cost' => 0
            ],
            'recipient_info' => [
                'first_name' => $order->getContact?->oc_first_name ?? 'Имя',
                'last_name' => $order->getContact?->oc_last_name ?? 'Фамилия',
                'partonymic' => $order->getContact?->oc_middle_name ?? '',
                'phone' => $order->getContact?->oc_phone ?? '+70000000000',
                'email' => $order->getContact?->oc_email ?? 'email@example.com'
            ],
            'last_mile_policy' => 'self_pickup'
        ];

        try {
            $response = Http::withToken($this->token)
                ->withHeaders(['Accept-Language' => 'ru'])
                ->baseUrl($this->baseUrl)
                ->acceptJson()
                ->post('/b2b/platform/request/create', $payload);

            if ($response->successful()) {
                return [
                    'status' => 'OK',
                    'id' => $response->json('request_id')
                ];
            }

            return [
                'status' => 'ERROR',
                'message' => $response->json('message') ?? CustomTranslator::get('Неизвестная ошибка'),
                'code' => $response->status(),
                'details' => $response->json()
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'ERROR',
                'message' => CustomTranslator::get('Сетевая или системная ошибка') . ': ' . $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    // *********************************************
    // *** Получаем этикетку для заказа
    public function getOrderLable($dsOrderId): ?array
    {
        $this->baseUrl = 'https://b2b-authproxy.taxi.yandex.net/api';

        $response = Http::withToken($this->token)
            ->withHeaders(['Accept-Language' => 'ru'])
            ->baseUrl($this->baseUrl)
            ->accept('application/pdf')
            ->post('/b2b/platform/request/generate-labels', [
                'request_ids' => [$dsOrderId],
                'generate_type' => 'one',
                'language' => 'ru',
            ]);

        if ($response->successful()) {
            // Сохраняем PDF в файл
            $path = storage_path("app/public/label_{$dsOrderId}.pdf");
            file_put_contents($path, $response->body());

            return [
                'status' => 'OK',
                'path' => $path,
                'filename' => "label_{$dsOrderId}.pdf",
                'url' => asset("storage/label_{$dsOrderId}.pdf"),
            ];
        }

        return [
            'status' => 'ERROR',
            'message' => 'Ошибка при получении этикетки',
            'code' => $response->status(),
        ];
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
            'latitude' => $filters['latitude'] ?? ['from' => 30.751310, 'to' => 70.886191],
            'longitude' => $filters['longitude'] ?? ['from' => 25.000000, 'to' => 26.000000],
            'page' => $filters['page'] ?? 1,
            'page_size' => $filters['page_size'] ?? 100
        ];

        return $this->request('post', '/b2b/platform/pickup-points/list', $filters);
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