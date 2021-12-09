<?php

namespace Tests\Feature\Console;

use App\Console\Commands\CacheClearCommand;
use App\Console\Commands\CompaniesCacheClearCommand;
use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\CompaniesCacheOption;
use App\Db\Entity\CompaniesCachePayment;
use App\Db\Entity\CompaniesCacheTerminal;
use App\Db\Entity\Country;
use App\Db\Entity\Role;
use App\Db\Entity\StreetCacheSearch;
use App\Db\Entity\StreetCacheSearchItem;
use App\Db\Entity\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompaniesCacheClearCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldClearCache()
    {
        /** @var CompaniesCache[] $companiesCache */
        $companiesCache = CompaniesCache::factory()->count(10)->create([
            'transport_number' => 32
        ]);

        foreach ($companiesCache as $companyCache) {
            CompaniesCacheName::factory()->count(10)->create([
                'companies_cache_id' => $companyCache->id
            ]);
        }

        $companiesCacheCount = CompaniesCache::query()->count();
        $this->assertEquals(10, $companiesCacheCount);
        $companiesCacheNameItemsCount = CompaniesCacheName::query()->count();
        $this->assertEquals(100, $companiesCacheNameItemsCount);

        $response = $this->artisan(CompaniesCacheClearCommand::class)
            ->assertExitCode(0);

        $response->run();

        $companiesCacheCount = CompaniesCache::query()->count();
        $this->assertEquals(0, $companiesCacheCount);
        $companiesCacheNameItemsCount = CompaniesCacheName::query()->count();
        $this->assertEquals(0, $companiesCacheNameItemsCount);
    }
}
