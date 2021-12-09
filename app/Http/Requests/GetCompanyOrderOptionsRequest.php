<?php

namespace App\Http\Requests;

use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use Illuminate\Validation\Rule;

/**
 * GetCompanyOrderOptionsRequest
 *
 * @property array $data
 * @property array $modifies
 */
class GetCompanyOrderOptionsRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'data' => [
                'required',
                'array'
            ],
            'data.cargoFrom' => [
                'required',
                Rule::exists(City::class, 'id')
            ],
            'data.cargoTo' => [
                'required',
                Rule::exists(City::class, 'id')
            ],
            'data.transportNumber' => [
                'required',
                Rule::exists(CompaniesCache::class, 'transport_number')
            ],
            'data.isArrivalByCourier' => 'nullable|boolean',
            'data.isDerivalByCourier' => 'nullable|boolean',
            'data.lang' => 'required|string',
            'modifies' => [
                'required',
                'array'
            ],
            'modifies.height' => 'required|numeric',
            'modifies.length' => 'required|numeric',
            'modifies.volume' => 'required|numeric',
            'modifies.weight' => 'required|numeric',
            'modifies.width' => 'required|numeric',
        ];
    }
}
