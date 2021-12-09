<?php

namespace App\Db\Entity;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * Class CompaniesCacheTerminal
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
class CompaniesCacheTerminal extends BaseEntity
{
    protected $table = 'companies_cache_terminal';

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
        $arrivalTerminalBlock = isset($response->arrivalTerminalBlock->aoptions[0]->variants) ? $response->arrivalTerminalBlock->aoptions[0]->variants : [];
        $derivalTerminalBlock = isset($response->derivalTerminalBlock->aoptions[0]->variants) ? $response->derivalTerminalBlock->aoptions[0]->variants : [];

        return (object) [
            'arrivalTerminalBlocks' => $arrivalTerminalBlock,
            'derivalTerminalBlocks' => $derivalTerminalBlock
        ];
    }
}
