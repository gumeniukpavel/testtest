<?php

namespace App\Http\Requests\CompanyTerminal;

use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * GetCompanyOrderOptionsRequest
 *
 * @property array $data
 * @property array $modifies
 */
class GetCompanyTerminalsRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'data' => [
                'required',
                'array'
            ],
            'data.cityId' => [
                'required',
                Rule::exists(City::class, 'id')
            ],
            'data.transportNumber' => [
                'required',
                Rule::exists(CompaniesCache::class, 'transport_number')
            ],
            'data.isArrival' => 'required|boolean',
            'data.isDerival' => 'required|boolean',
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
