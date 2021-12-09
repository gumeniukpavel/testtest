<?php

namespace App\Console\Commands;

use App\Db\Entity\Role;
use App\Db\Entity\User;
use App\Db\Service\CityCacheSearchDao;
use App\Db\Service\StreetCacheSearchDao;
use App\Db\Service\UserDao;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CacheClearAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all locations cache';
    /**
     * @var CityCacheSearchDao
     */
    private CityCacheSearchDao $cityCacheSearchDao;
    /**
     * @var StreetCacheSearchDao
     */
    private StreetCacheSearchDao $streetCacheSearchDao;

    public function __construct(
        CityCacheSearchDao $cityCacheSearchDao,
        StreetCacheSearchDao $streetCacheSearchDao
    ) {
        parent::__construct();
        $this->cityCacheSearchDao = $cityCacheSearchDao;
        $this->streetCacheSearchDao = $streetCacheSearchDao;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->cityCacheSearchDao->clear();
        $this->streetCacheSearchDao->clear();
        $this->info('Cache is cleared!');
        return 0;
    }
}
