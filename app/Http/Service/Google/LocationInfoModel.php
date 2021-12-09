<?php

namespace App\Http\Service\Google;

class LocationInfoModel
{
    public ?string $country;
    public ?string $countryShortName;

    public ?string $city;
    public ?string $cityShortName;

    public ?string $region;
    public ?string $regionShortName;

    public ?string $street;
    public ?string $streetShortName;

    public ?string $streetNumber;

    public function __construct(array $resultSearchComponent)
    {
        $addressComponents = $resultSearchComponent['address_components'];

        foreach ($addressComponents as $addressComponent) {
            $types = $addressComponent['types'];
            if (in_array('street_number', $types)) {
                $this->streetNumber = $addressComponent['long_name'];
                continue;
            }
            if (in_array('route', $types)) {
                $this->street = $addressComponent['long_name'];
                $this->streetShortName = $addressComponent['short_name'];
                continue;
            }
            if (in_array('locality', $types)) {
                $this->city = $addressComponent['long_name'];
                $this->cityShortName = $addressComponent['short_name'];
                continue;
            }
            if (in_array('administrative_area_level_1', $types)) {
                $this->region = $addressComponent['long_name'];
                continue;
            } elseif (in_array('administrative_area_level_2', $types)) {
                $this->region = $addressComponent['long_name'];
                continue;
            }
            if (in_array('country', $types)) {
                $this->country = $addressComponent['long_name'];
                $this->countryShortName = $addressComponent['short_name'];
                continue;
            }
        }
    }

    /**
     * @return self[]
     */
    public static function parseFromJson(array $requestData): array
    {
        $response = [];
        if (!isset($requestData['results'])) {
            return $response;
        }
        foreach ($requestData['results'] as $searchResult) {
            $response[] = new self($searchResult);
        }
        return $response;
    }

    public function isValidAddress(): bool
    {
        return !empty($this->street) && !empty($this->city) && !empty($this->country);
    }

    public function isValidCity(): bool
    {
        return !empty($this->country) && !empty($this->city);
    }
}
