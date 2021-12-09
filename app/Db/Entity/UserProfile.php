<?php

namespace App\Db\Entity;

/**
 * Class UserProfile
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $client_name
 * @property string $client_email
 * @property string $unique_identity_number
 * @property string $notes
 *
 * @property User $user
 */
class UserProfile extends BaseEntity
{
    protected $table = 'user_profile';

    protected $visible = [
        'id',
        'client_name',
        'client_email',
        'unique_identity_number',
        'notes',

        'user'
    ];

    protected $fillable = [
        'user_id',
        'client_name',
        'client_email',
        'unique_identity_number',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
