<?php

namespace App\Http\Requests\Address;

use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * SearchStreetRequest
 *
 * @property integer $cityId
 * @property string $searchString
 */
class SearchStreetRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'cityId' => [
                'required',
                Rule::exists(City::class, 'id')
            ],
            'searchString' => 'required|string|max:255',
        ];
    }
}
