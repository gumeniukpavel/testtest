<?php

namespace App\Http\Requests\User;

use App\Constant\OrderType;
use App\Constant\SortUserProfile;
use App\Db\Entity\City;
use App\Db\Entity\UserProfile;
use App\Http\Requests\ApiFormRequest;
use App\Rules\IsEnumValueRule;
use Illuminate\Validation\Rule;

/**
 * GetListRequest
 *
 * @property string $orderBy
 * @property string $searchString
 * @property string $sortColumn
 * @property integer $page
 */
class GetListRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'orderBy' => [
                'required',
                new IsEnumValueRule(OrderType::class)
            ],
            'searchString' => [
                'string'
            ],
            'sortColumn' => [
                'string',
                new IsEnumValueRule(SortUserProfile::class)
            ],
            'page' => 'nullable|integer|min:0',
        ];
    }

    public function getPage()
    {
        return $this->page ? $this->page : 1;
    }
}
