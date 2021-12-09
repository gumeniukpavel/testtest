<?php

namespace App\Db\Entity;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class CityCacheSearch
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property string $search_string
 *
 * @property CityCacheSearchItem $cityCacheSearchItems
 */
class CityCacheSearch extends BaseEntity
{
    protected $table = 'city_cache_search';

    protected $visible = [
        'id',
        'search_string',
    ];

    protected $fillable = [
        'search_string',
    ];

    public function cityCacheSearchItems(): HasMany
    {
        return $this->hasMany(CityCacheSearchItem::class);
    }
}
