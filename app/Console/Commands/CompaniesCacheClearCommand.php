<?php

namespace App\Console\Commands;

use App\Db\Entity\Role;
use App\Db\Entity\User;
use App\Db\Service\CityCacheSearchDao;
use App\Db\Service\CompaniesCacheDao;
use App\Db\Service\StreetCacheSearchDao;
use App\Db\Service\UserDao;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompaniesCacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all temporary cache';

    private CompaniesCacheDao $companiesCacheDao;

    public function __construct(
        CompaniesCacheDao $companiesCacheDao
    ) {
        parent::__construct();
        $this->companiesCacheDao = $companiesCacheDao;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->companiesCacheDao->clearCompaniesCache();
        $this->info('Cache is cleared!');
        return 0;
    }
}
