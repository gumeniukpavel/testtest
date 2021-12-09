<?php

namespace App\Db\Entity;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class CityCacheSearchItem
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property integer $city_cache_search_id
 * @property string $city_id
 *
 * @property CityCacheSearch $cityCacheSearch
 * @property City $city
 */
class CityCacheSearchItem extends BaseEntity
{
    protected $visible = [
        'id',

        'cityCacheSearch',
        'city',
    ];

    protected $fillable = [
        'city_cache_search_id',
        'city_id',
    ];

    public $timestamps = false;

    public function cityCacheSearch(): BelongsTo
    {
        return $this->belongsTo(CityCacheSearch::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
