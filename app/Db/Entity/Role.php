<?php

namespace App\Db\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Role
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Role extends BaseEntity
{
    use HasFactory;

    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;

    const ROLE_NAME_ADMIN = 'admin';
    const ROLE_NAME_USER = 'user';

    protected $visible = [
        'id',
        'name',
        'display_name'
    ];

    protected $guarded = [];
}
