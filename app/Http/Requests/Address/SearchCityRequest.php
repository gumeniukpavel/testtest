<?php

namespace App\Http\Requests\Address;

use App\Http\Requests\ApiFormRequest;

/**
 * SearchCityRequest
 *
 * @property string $searchString
 */
class SearchCityRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'searchString' => 'required|string|max:255',
        ];
    }
}
