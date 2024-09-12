<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Models\Poli;
use App\Models\Log;

class SatuSehatService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $tokenUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientId = env('SATUSEHAT_CLIENT_ID');
        $this->clientSecret = env('SATUSEHAT_CLIENT_SECRET');
        $this->tokenUrl = env('SATUSEHAT_TOKEN_URL');
    }

    public function getAccessToken()
    {
        try {
            $response = $this->client->post($this->tokenUrl, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'] ?? null;
        } catch (GuzzleException $e) {
            // Handle error
            return null;
        }
    }

    public function sendPoliData(Poli $poli)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return [
                'status' => 'error',
                'error' => 'Failed to get access token',
            ];
        }

        $url = 'https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1/Location';
        $requestBody = [
            'resourceType' => 'Location',
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/location/' . env('SATUSEHAT_ORG_ID'),
                    'value' => 'INT',
                ]
            ],
            'status' => 'active',
            'name' => $poli->name,
            'description' => $poli->description,
            'mode' => 'instance',
            'telecom' => [
                [
                    'system' => 'phone',
                    'value' => $poli->phone,
                    'use' => 'work'
                ],
                [
                    'system' => 'email',
                    'value' => $poli->email,
                    'use' => 'work'
                ],
                [
                    'system' => 'url',
                    'value' => $poli->url,
                    'use' => 'work'
                ]
            ],
            'physicalType' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/location-physical-type',
                        'code' => 'ro',
                        'display' => 'Room'
                    ]
                ]
            ],
            'position' => [
                'longitude' => $poli->longitude,
                'latitude' => $poli->latitude,
                'altitude' => 0
            ],
            'managingOrganization' => [
                'reference' => 'Organization/' . env('SATUSEHAT_ORG_ID')
            ]
        ];

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestBody,
            ]);

            $statusCode = $response->getStatusCode();
            $fullResponse = $response->getBody()->getContents();

            // Simpan log
            Log::create([
                'status_code' => $statusCode,
                'full_response' => $fullResponse,
                'request_body' => json_encode($requestBody),
                'url' => $url,
            ]);

            return [
                'status' => 'success',
                'data' => json_decode($fullResponse, true),
            ];
        } catch (GuzzleException $e) {
            // Simpan log jika terjadi error
            Log::create([
                'status_code' => $e->getCode(),
                'full_response' => $e->getMessage(),
                'request_body' => json_encode($requestBody),
                'url' => $url,
            ]);

            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
}
