<?php

namespace App\Http\Requests\ScheduleCalculation;

use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\Street;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * SearchAddressRequest
 *
 * @property integer $transportNumber
 * @property integer $cityFrom
 * @property string $cargoFromCountry
 * @property string $cargoFromState
 * @property string $cargoToState
 * @property string $cargoToCountry
 * @property boolean $isDerivalByCourier
 * @property boolean $isArrivalByCourier
 * @property integer $paymentType
 * @property integer $cityTo
 * @property float $weight
 * @property float $volume
 * @property float $width
 * @property float $length
 * @property float $height
 * @property string $currency
 * @property string $language
 * @property integer $insurancePrice
 * @property array $options
 * @property integer $payerType
 * @property array $places
 * @property string $callbackUrl
 */
class CreateScheduleCalculationRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'cityFrom' => [
                'required',
                Rule::exists(City::class, 'id')
            ],
            'cityTo' => [
                'required',
                Rule::exists(City::class, 'id')
            ],
            'isDerivalByCourier' => 'required|boolean',
            'isArrivalByCourier' => 'required|boolean',
            'places' => 'required|array',
            'currency' => 'required|string',
            'language' => 'required|string',
            'insurancePrice' => 'required|integer',
            'paymentType' => 'required|integer',
            'payerType' => 'required|integer',
            'callbackUrl' => 'required|string',
            'weight' => 'required|numeric',
            'volume' => 'required|numeric',
            'width' => 'required|numeric',
            'length' => 'required|numeric',
            'height' => 'required|numeric',
        ];
    }
}
