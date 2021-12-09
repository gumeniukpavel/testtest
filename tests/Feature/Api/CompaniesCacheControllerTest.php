<?php

namespace Tests\Feature\Api;

use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\Street;
use App\Db\Entity\StreetCacheSearch;
use App\Db\Entity\StreetCacheSearchItem;
use App\Db\Entity\User;
use App\Db\Entity\UserRequestHistory;
use App\Http\Service\Google\FakeGeocodingService;
use App\Http\Service\Google\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompaniesCacheControllerTest extends TestCase
{
    const URL = 'api/companies/';

    protected function setUp(): void
    {
        parent::setUp();

        $fakeService = $this->app->make(FakeGeocodingService::class);
        $this->instance(GeocodingService::class, $fakeService);
    }

    public function testShouldReceiveSearchAndAddCity()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var CompaniesCache[] $companiesCache */
        $companiesCache = CompaniesCache::factory()->count(5)->create();

        foreach ($companiesCache as $companyCache) {
            CompaniesCacheName::factory()->count(10)->create([
                'companies_cache_id' => $companyCache->id
            ]);
        }

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $user->getJWTToken(),
            [
                'page' => 1
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 5);

        $userRequestHistory = UserRequestHistory::query()->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);
    }
}
