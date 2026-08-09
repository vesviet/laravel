<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoshipService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.goship.base_url', 'https://api.goship.io/api/v2');
        $this->token = config('services.goship.token', 'default-token');
    }

    /**
     * Calculate shipping fee
     *
     * @param array $toAddress ['city', 'district', 'ward']
     * @param array $package ['weight', 'width', 'height', 'length']
     * @return array
     */
    public function getShippingRates(array $toAddress, array $package = []): array
    {
        // Example mock payload for Goship
        $payload = [
            'shipment' => [
                'address_from' => [
                    'city' => config('services.store.city_id', '1'),
                    'district' => config('services.store.district_id', '1'),
                ],
                'address_to' => [
                    'city' => $toAddress['city'],
                    'district' => $toAddress['district'],
                    'ward' => $toAddress['ward'] ?? null,
                ],
                'parcel' => [
                    'cod' => 0,
                    'weight' => $package['weight'] ?? 1000,
                    'width' => $package['width'] ?? 10,
                    'height' => $package['height'] ?? 10,
                    'length' => $package['length'] ?? 10,
                ],
            ]
        ];

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/rates", $payload);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
            
            Log::error('Goship calculate rates failed', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Goship calculate rates error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Create waybill
     *
     * @param \App\Models\Order $order
     * @return array|null
     */
    public function createWaybill($order): ?array
    {
        $payload = [
            'shipment' => [
                'order_id' => $order->id,
                'address_from' => [
                    'name' => config('services.store.name', 'My Store'),
                    'phone' => config('services.store.phone', '0901234567'),
                    'street' => config('services.store.address', '123 Store St'),
                    'city' => config('services.store.city_id', '1'),
                    'district' => config('services.store.district_id', '1'),
                ],
                'address_to' => [
                    'name' => $order->shipping_name ?? $order->customer_name,
                    'phone' => $order->shipping_phone ?? $order->customer_phone,
                    'street' => $order->shipping_address ?? 'Customer Address',
                    'city' => $order->shipping_city_id ?? '1',
                    'district' => $order->shipping_district_id ?? '1',
                    'ward' => $order->shipping_ward_id ?? '1',
                ],
                'parcel' => [
                    'cod' => $order->payment_method === 'cod' ? $order->total_amount : 0,
                    'weight' => 1000, // example static weight
                ],
            ]
        ];

        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/shipments", $payload);

            if ($response->successful()) {
                $data = $response->json('data');
                // You can update the order with waybill ID here if needed
                // $order->update(['tracking_number' => $data['id']]);
                return $data;
            }
            
            Log::error('Goship create waybill failed', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Goship create waybill error: ' . $e->getMessage());
        }

        return null;
    }
}
