<?php

namespace App\Http\Requests\User;

use App\Db\Entity\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdatePasswordByUserRequest
 *
 * @property string $oldPassword
 * @property string $newPassword
 * @property string $repeatPassword
 */
class UpdatePasswordByUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'oldPassword' => 'required|max:255',
            'newPassword' => 'required|min:6|max:255',
            'repeatPassword' => 'required|min:6|max:255',
        ];
    }
}
