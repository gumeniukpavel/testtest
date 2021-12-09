<?php

namespace App\Http\Requests\User;

use App\Db\Entity\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdatePasswordRequest
 *
 * @property integer $id
 * @property string $newPassword
 * @property string $repeatPassword
 */
class UpdatePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => [
                'required',
                Rule::exists(User::class, 'id')
            ],
            'newPassword' => 'required|min:6|max:255',
            'repeatPassword' => 'required|min:6|max:255',
        ];
    }
}
