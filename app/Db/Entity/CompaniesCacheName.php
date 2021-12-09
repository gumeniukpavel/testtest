<?php

namespace App\Db\Entity;

/**
 * Class CompaniesCacheName
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property string $name
 * @property string $lang
 * @property integer $companies_cache_id
 */
class CompaniesCacheName extends BaseEntity
{
    protected $table = 'companies_cache_names';
    public $timestamps = false;

    protected $visible = [
        'id',
        'name',
        'lang',
    ];

    protected $fillable = [
        'name',
        'lang',
    ];
}
