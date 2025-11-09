<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShipEngineService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl = 'https://api.shipengine.com/v1/';

    public function __construct()
    {
        $this->apiKey = config('services.shipengine.api_key');
        // $this->apiKey = "3DdOVSJt05Dd5QJmNvPqYvfmsHnWTee66DNGFp9E0yU";

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('ShipEngine API key is not configured. Please set SHIPENGINE_API_KEY in your .env file.');
        }

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Get shipping rates for a shipment
     */
    public function getRates(array $shipmentData)
    {
        info("====================== ShipEngine Get Rates Request ==================");
        info(print_r($shipmentData, true));
        info("========================================");
        try {
            $response = $this->client->post('rates', [
                'json' => $shipmentData
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            info('ShipEngine Rates Response: ' . print_r($data, true));
            return $data;
        } catch (ClientException $e) {
            // Handle 4xx client errors (400, 401, 404, etc.)
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($responseBody, true);

            // If we successfully decoded the API error response, return it
            if ($errorData && isset($errorData['errors'])) {
                return [
                    'rate_response' => [
                        'status' => 'error',
                        'errors' => $errorData['errors'],
                        'request_id' => $errorData['request_id'] ?? null
                    ]
                ];
            }

            // Fall back to logging and throwing the original error
            Log::error('ShipEngine API client error getting rates', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (ServerException $e) {
            // Handle 5xx server errors
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($responseBody, true);

            if ($errorData && isset($errorData['errors'])) {
                return [
                    'rate_response' => [
                        'status' => 'error',
                        'errors' => $errorData['errors'],
                        'request_id' => $errorData['request_id'] ?? null
                    ]
                ];
            }

            Log::error('ShipEngine API server error getting rates', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (GuzzleException $e) {
            Log::error('ShipEngine API error getting rates', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Get list of carrier packaging types
     */
    public function getCarrierPackages(string $carrierId): array
    {
        $cacheKey = "shipengine_carrier_packages_{$carrierId}";

        return Cache::remember($cacheKey, 3600, function () use ($carrierId) {
            try {
                $response = $this->client->get("carriers/{$carrierId}/packages");
                $data = json_decode($response->getBody()->getContents(), true);

                return $data;
            } catch (GuzzleException $e) {
                Log::error('ShipEngine API error getting carrier packages', [
                    'carrier_id' => $carrierId,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Create a shipping label
     */
    public function createLabel(string $rateId): array
    {
        try {
            $response = $this->client->post('labels/rates/' . $rateId);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data;
        } catch (ClientException $e) {
            // Handle 4xx client errors (400, 401, 404, etc.)
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($responseBody, true);

            // If we successfully decoded the API error response, return it
            if ($errorData && isset($errorData['errors'])) {
                return [
                    'status' => 'error',
                    'api_errors' => $errorData['errors'],
                    'request_id' => $errorData['request_id'] ?? null
                ];
            }
            throw $e;
        } catch (ServerException $e) {
            // Handle 5xx server errors
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($responseBody, true);

            if ($errorData && isset($errorData['errors'])) {
                return [
                    'status' => 'error',
                    'api_errors' => $errorData['errors'],
                    'request_id' => $errorData['request_id'] ?? null
                ];
            }
            throw $e;
        } catch (GuzzleException $e) {
            throw $e;
        }
    }

    /**
     * Void a shipping label
     */
    public function voidLabel(string $labelId): array
    {
        try {
            $response = $this->client->put("labels/{$labelId}/void");
            $data = json_decode($response->getBody()->getContents(), true);
            return $data;
        } catch (ClientException $e) {
            // Handle 4xx client errors (400, 401, 404, etc.)
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($responseBody, true);

            // If we successfully decoded the API error response, return it
            if ($errorData && isset($errorData['errors'])) {
                return [
                    'status' => 'error',
                    'api_errors' => $errorData['errors'],
                    'request_id' => $errorData['request_id'] ?? null
                ];
            }
            throw $e;
        } catch (ServerException $e) {
            // Handle 5xx server errors
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($responseBody, true);

            if ($errorData && isset($errorData['errors'])) {
                return [
                    'status' => 'error',
                    'api_errors' => $errorData['errors'],
                    'request_id' => $errorData['request_id'] ?? null
                ];
            }
            throw $e;
        } catch (GuzzleException $e) {
            throw $e;
        }
    }

    /**
     * Track a package by tracking number
     */
    public function trackPackage(string $trackingNumber, string|null $carrierCode = null): array
    {
        try {
            $url = "tracking?tracking_number={$trackingNumber}";
            if ($carrierCode) {
                $url .= "&carrier_code={$carrierCode}";
            }

            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            return $data;
        } catch (GuzzleException $e) {
            Log::error('ShipEngine API error tracking package', [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Get list of carriers
     */
    public function getCarriers(): array
    {
        $cacheKey = 'shipengine_carriers';

        return Cache::remember($cacheKey, 3600, function () {
            try {
                $response = $this->client->get('carriers');
                $data = json_decode($response->getBody()->getContents(), true);


                return $data;
            } catch (GuzzleException $e) {
                Log::error('ShipEngine API error getting carriers', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Get services for a specific carrier
     */
    public function getCarrierServices(string $carrierId): array
    {
        $cacheKey = "shipengine_carrier_services_{$carrierId}";

        return Cache::remember($cacheKey, 3600, function () use ($carrierId) {
            try {
                $response = $this->client->get("carriers/{$carrierId}/services");
                $data = json_decode($response->getBody()->getContents(), true);

                return $data;
            } catch (GuzzleException $e) {
                Log::error('ShipEngine API error getting carrier services', [
                    'carrier_id' => $carrierId,
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Validate an address
     */
    public function validateAddress(array $address): array
    {
        try {
            $response = $this->client->post('addresses/validate', [
                'json' => $address
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data;
        } catch (GuzzleException $e) {
            Log::error('ShipEngine API error validating address', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Create a shipment
     */
    public function createShipment(array $shipmentData): array
    {
        try {
            $response = $this->client->post('shipments', [
                'json' => $shipmentData
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $data;
        } catch (GuzzleException $e) {
            Log::error('ShipEngine API error creating shipment', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Get shipment by ID
     */
    public function getShipment(string $shipmentId): array
    {
        try {
            $response = $this->client->get("shipments/{$shipmentId}");
            $data = json_decode($response->getBody()->getContents(), true);

            return $data;
        } catch (GuzzleException $e) {
            Log::error('ShipEngine API error getting shipment', [
                'shipment_id' => $shipmentId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel a label
     */
    public function cancelLabel(string $labelId): array
    {
        try {
            $response = $this->client->put("labels/{$labelId}/void");
            $data = json_decode($response->getBody()->getContents(), true);

            return $data;
        } catch (GuzzleException $e) {
            Log::error('ShipEngine API error cancelling label', [
                'label_id' => $labelId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        }
    }

    /**
     * Get account information
     */
    public function getAccount(): array
    {
        $cacheKey = 'shipengine_account';

        return Cache::remember($cacheKey, 1800, function () {
            try {
                $response = $this->client->get('account');
                $data = json_decode($response->getBody()->getContents(), true);

                return $data;
            } catch (GuzzleException $e) {
                Log::error('ShipEngine API error getting account', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Helper method to format address for ShipEngine API
     */
    public function formatAddress(array $address): array
    {
        return [
            'name' => $address['name'] ?? '',
            'phone' => $address['phone'] ?? '',
            'company_name' => $address['company_name'] ?? '',
            'address_line1' => $address['address_line1'] ?? '',
            'address_line2' => $address['address_line2'] ?? '',
            'address_line3' => $address['address_line3'] ?? '',
            'city_locality' => $address['city'] ?? $address['city_locality'] ?? '',
            'state_province' => $address['state'] ?? $address['state_province'] ?? '',
            'postal_code' => $address['postal_code'] ?? $address['zip'] ?? '',
            'country_code' => $address['country_code'] ?? 'US',
            'address_residential_indicator' => $address['address_residential_indicator'] ?? 'unknown'
        ];
    }

    /**
     * Helper method to format package for ShipEngine API
     */
    public function formatPackage(array $package): array
    {
        return [
            'weight' => [
                'value' => $package['weight'] ?? 1,
                'unit' => $package['weight_unit'] ?? 'pound'
            ],
            'dimensions' => [
                'length' => $package['length'] ?? 12,
                'width' => $package['width'] ?? 12,
                'height' => $package['height'] ?? 12,
                'unit' => $package['dimension_unit'] ?? 'inch'
            ],
            'package_code' => $package['package_code'] ?? 'package',
            'insured_value' => [
                'amount' => $package['insured_value'] ?? 0,
                'currency' => $package['currency'] ?? 'USD'
            ]
        ];
    }
}
