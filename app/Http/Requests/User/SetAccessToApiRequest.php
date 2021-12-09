<?php

namespace App\Http\Requests\User;

use App\Db\Entity\User;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * SetAccessToApiRequest
 *
 * @property integer $id
 * @property boolean $isHasAccessToApi
 */
class SetAccessToApiRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'id' => [
                'required',
                Rule::exists(User::class, 'id')
            ],
            'isHasAccessToApi' => 'required|boolean',
        ];
    }
}
