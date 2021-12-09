<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * CreateRequest
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $repeatPassword
 * @property string $uniqueIdentityNumber
 * @property string $notes
 * @property integer $endAccessToApiAt
 */
class CreateRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email:rfc',
            'password' => 'required|min:6|max:255',
            'repeatPassword' => 'required|min:6|max:255',
            'uniqueIdentityNumber' => 'required|string',
            'notes' => 'nullable|string',
            'endAccessToApiAt' => [
                'nullable',
                'integer'
            ],
        ];
    }
}
