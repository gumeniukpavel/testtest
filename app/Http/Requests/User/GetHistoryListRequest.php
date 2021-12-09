<?php

namespace App\Http\Requests\User;

use App\Db\Entity\User;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * GetHistoryListRequest
 *
 * @property integer $userId
 * @property integer $page
 */
class GetHistoryListRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'userId' => [
                'required',
                Rule::exists(User::class, 'id')
            ],
            'page' => 'nullable|integer|min:0',
        ];
    }

    public function getPage()
    {
        return $this->page ? $this->page : 1;
    }
}
