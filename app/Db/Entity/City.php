<?php

namespace App\Db\Entity;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class City
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property string $name
 * @property string $short_name
 * @property string $region
 *
 * @property CityCacheSearchItem $cityCacheSearchItems
 * @property Country $country
 * @property Street[] | Collection $streets
 */
class City extends BaseEntity
{
    protected $visible = [
        'id',
        'name',
        'short_name',
        'region',

        'country'
    ];

    protected $fillable = [
        'name',
        'short_name',
        'region'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function streets(): HasMany
    {
        return $this->hasMany(Street::class);
    }

    public function cityCacheSearchItems(): HasMany
    {
        return $this->hasMany(CityCacheSearchItem::class);
    }
}
