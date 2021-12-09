<?php

namespace App\Db\Entity;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 * @package App\Db\Country
 *
 * @property integer $id
 * @property string $name
 * @property string $short_name
 *
 * @property City[] | Collection $cities
 */
class Country extends BaseEntity
{
    protected $visible = [
        'id',
        'name',
        'short_name'
    ];

    protected $fillable = [
        'name',
        'short_name',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
