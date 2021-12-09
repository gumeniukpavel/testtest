<?php

namespace App\Db\Entity;

/**
 * Class UserRequestHistory
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $url
 * @property string $data
 *
 * @property User $user
 */
class UserRequestHistory extends BaseEntity
{
    protected $table = 'user_request_history';

    protected $visible = [
        'id',
        'data',
        'url',

        'user'
    ];

    protected $fillable = [
        'user_id',
        'data',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
