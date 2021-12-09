<?php

namespace App\Db\Entity;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class City
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property string $name
 * @property string $short_name
 * @property integer $city_id
 * @property integer $country_id
 *
 * @property CityCacheSearchItem $cityCacheSearchItems
 * @property City $city
 */
class Street extends BaseEntity
{
    protected $visible = [
        'id',
        'name',
        'short_name',

        'city',
    ];

    protected $fillable = [
        'name',
        'short_name',
        'city_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function cityCacheSearchItems()
    {
        return $this->hasMany(CityCacheSearchItem::class);
    }
}
