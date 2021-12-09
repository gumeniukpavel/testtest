<?php

namespace App\Db\Entity;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class CompaniesCache
 * @package CompaniesCacheOption\Db\Entity
 *
 * @property integer $id
 * @property string $token
 * @property string $data
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property string $response
 */
class CompaniesCacheOption extends BaseEntity
{
    protected $table = 'companies_cache_options';

    protected $visible = [
        'id',

        'response',
    ];

    protected $fillable = [
        'token',
        'data',
    ];

    protected $appends = [
        'response'
    ];

    public function getResponseAttribute()
    {
        return json_decode($this->data);
    }
}
