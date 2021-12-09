<?php

namespace App\Http\Requests\User;

use App\Db\Entity\User;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateRequest
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $uniqueIdentityNumber
 * @property string $notes
 * @property integer $endAccessToApiAt
 */
class UpdateRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'id' => [
                'required',
                Rule::exists(User::class, 'id')
            ],
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255|email:rfc',
            'uniqueIdentityNumber' => 'nullable|string',
            'notes' => 'nullable|string',
            'endAccessToApiAt' => [
                'nullable',
                'integer'
            ],
        ];
    }
}
