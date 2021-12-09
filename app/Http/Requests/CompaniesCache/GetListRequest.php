<?php

namespace App\Http\Requests\CompaniesCache;

use App\Http\Requests\ApiFormRequest;

/**
 * SearchAddressRequest
 *
 * @property string $page
 */
class GetListRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'page' => 'nullable|integer|min:0',
        ];
    }

    public function getPage()
    {
        return $this->page ? $this->page : 1;
    }
}
