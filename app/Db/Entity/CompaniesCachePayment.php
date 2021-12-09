<?php

namespace App\Db\Entity;

use Carbon\Carbon;

/**
 * Class CompaniesCachePayment
 * @package CompaniesCacheOption\Db\Entity
 *
 * @property integer $id
 * @property string $token
 * @property string $data
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * @property string $response
 */
class CompaniesCachePayment extends BaseEntity
{
    protected $table = 'companies_cache_payment';

    protected $visible = [
        'id',

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
        $response = json_decode($this->data);
        $paymentTypes = isset($response->aoptions->paymentType->variants) ? $response->aoptions->paymentType->variants : [];
        $payerTypes = isset($response->aoptions->payerType->variants) ? $response->aoptions->payerType->variants : [];

        return (object) [
            'paymentTypes' => $paymentTypes,
            'payerTypes' => $payerTypes
        ];
    }
}
