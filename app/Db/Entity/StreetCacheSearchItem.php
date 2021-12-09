<?php

namespace App\Db\Entity;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class StreetCacheSearchItem
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property integer $street_cache_search_id
 * @property string $street_id
 *
 * @property StreetCacheSearch $streetCacheSearch
 * @property Street $street
 */
class StreetCacheSearchItem extends BaseEntity
{
    protected $visible = [
        'id',

        'streetCacheSearch',
        'street',
    ];

    protected $fillable = [
        'street_cache_search_id',
        'street_id',
    ];

    public $timestamps = false;

    public function streetCacheSearch()
    {
        return $this->belongsTo(CityCacheSearch::class);
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }
}
