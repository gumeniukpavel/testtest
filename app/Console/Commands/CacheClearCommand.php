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

class CacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-temporary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all temporary cache';

    private CityCacheSearchDao $cityCacheSearchDao;
    private StreetCacheSearchDao $streetCacheSearchDao;
    private CompaniesCacheDao $companiesCacheDao;

    public function __construct(
        CityCacheSearchDao $cityCacheSearchDao,
        StreetCacheSearchDao $streetCacheSearchDao,
        CompaniesCacheDao $companiesCacheDao
    ) {
        parent::__construct();
        $this->cityCacheSearchDao = $cityCacheSearchDao;
        $this->streetCacheSearchDao = $streetCacheSearchDao;
        $this->companiesCacheDao = $companiesCacheDao;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->cityCacheSearchDao->clearCache();
        $this->streetCacheSearchDao->clearCache();
        $this->companiesCacheDao->clearOptionsCache();
        $this->info('Cache is cleared!');
        return 0;
    }
}
