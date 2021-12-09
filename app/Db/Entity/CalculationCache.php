<?php

namespace App\Db\Entity;

/**
 * Class CalculationCache
 * @package App\Db\Entity
 *
 * @property integer $id
 * @property string $token
 * @property string $data
 *
 * @property string $response
 */
class CalculationCache extends BaseEntity
{
    protected $visible = [
        'id',
        'token',

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
