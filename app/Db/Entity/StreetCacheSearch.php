<?php

namespace App\Db\Entity;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class StreetCacheSearch
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property integer $city_id
 * @property string $search_string
 *
 * @property StreetCacheSearchItem $streetCacheSearchItems
 * @property City $city
 */
class StreetCacheSearch extends BaseEntity
{
    protected $table = 'street_cache_search';

    protected $visible = [
        'id',
        'city_id',
        'search_string',

        'streetCacheSearchItems'
    ];

    protected $fillable = [
        'search_string',
    ];

    public function streetCacheSearchItems(): HasMany
    {
        return $this->hasMany(StreetCacheSearchItem::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
