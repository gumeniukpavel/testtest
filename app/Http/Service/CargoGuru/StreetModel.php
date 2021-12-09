<?php

namespace App\Http\Service\CargoGuru;

class StreetModel
{
    public ?string $attribute;
    public ?string $street;
    public ?string $fullName;
    public ?string $code;
    public ?string $label;
    public ?string $cityName;

    public function __construct(array $resultStreetComponent)
    {
        $this->attribute = $resultStreetComponent['attribute'];
        $this->street = $resultStreetComponent['street'];
        $this->fullName = $resultStreetComponent['fullName'];
        $this->code = $resultStreetComponent['code'];
        $this->label = $resultStreetComponent['label'];
        $this->cityName = $resultStreetComponent['cityName'];
    }

    /**
     * @return self[]
     */
    public static function parseFromJson(array $requestData): array
    {
        $response = [];
        foreach ($requestData as $searchResult) {
            $response[] = new self($searchResult);
        }
        return $response;
    }
}
