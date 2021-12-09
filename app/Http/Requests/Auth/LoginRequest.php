<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

/**
 * LoginRequest
 *
 * @property string $email
 * @property string $password
 */
class LoginRequest extends ApiFormRequest
{
    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => 'required|min:6|max:255',
        ];
    }
}
