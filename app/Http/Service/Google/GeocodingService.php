<?php

namespace App\Http\Service\Google;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    const GET_ADDRESS_INFO_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    private $api_key;

    public function __construct()
    {
        $this->api_key = config('services.google.api_key');
    }

    /**
     * @param  string  $searchString
     * @return LocationInfoModel[]
     */
    public function getAddressBySearchString(string $searchString): array
    {
        $requestAddress = str_replace(" ", "+", trim($searchString));

        $response = Http::get(self::GET_ADDRESS_INFO_URL, [
            'address' => $requestAddress,
            'key' => $this->api_key,
            'language' => 'ru'
        ]);

        $responseJson = $response->json();
        if ($response->failed()) {
            Log::error('Google search returned error: '.json_encode($responseJson));
            return [];
        }
        $responseJson = $response->json();
        if ($responseJson['status'] != 'OK') {
            Log::error('Google search returned error: '.json_encode($responseJson));
            return [];
        }

        return LocationInfoModel::parseFromJson($response->json());
    }
}
