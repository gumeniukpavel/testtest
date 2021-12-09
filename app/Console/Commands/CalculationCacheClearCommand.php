<?php

namespace App\Console\Commands;

use App\Db\Service\CalculationCacheDao;
use Illuminate\Console\Command;

class CalculationCacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-calculation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all calculation cache';

    private CalculationCacheDao $calculationCacheDao;

    public function __construct(
        CalculationCacheDao $calculationCacheDao
    ) {
        parent::__construct();
        $this->calculationCacheDao = $calculationCacheDao;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->calculationCacheDao->clearCache();
        $this->info('Calculation cache is cleared!');
        return 0;
    }
}
