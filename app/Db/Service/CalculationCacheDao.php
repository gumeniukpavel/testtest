<?php

namespace App\Db\Service;

use App\Db\Entity\CalculationCache;
use Carbon\Carbon;

class CalculationCacheDao
{
    public function clearCache()
    {
        CalculationCache::query()
            ->where('created_at', '<', Carbon::now()->subDay())
            ->delete();
    }
}
