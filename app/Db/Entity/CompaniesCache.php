<?php

namespace App\Db\Entity;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class CompaniesCache
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property boolean $can_order_now
 * @property string $transport_lang
 * @property string $transport_logo
 * @property string $transport_name
 * @property int $transport_number
 * @property string $transport_site
 *
 * @property CompaniesCacheName $companiesCacheNames
 */
class CompaniesCache extends BaseEntity
{
    protected $table = 'companies_cache';
    public $timestamps = false;

    protected $visible = [
        'id',
        'can_order_now',
        'transport_lang',
        'transport_logo',
        'transport_name',
        'transport_number',
        'transport_site',

        'companiesCacheNames'
    ];

    protected $fillable = [
        'can_order_now',
        'transport_lang',
        'transport_logo',
        'transport_name',
        'transport_number',
        'transport_site',
    ];

    public function companiesCacheNames()
    {
        return $this->hasMany(CompaniesCacheName::class);
    }
}
