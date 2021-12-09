<?php

namespace Tests\Feature\Api;

use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\Country;
use App\Db\Entity\Role;
use App\Db\Entity\Street;
use App\Db\Entity\StreetCacheSearch;
use App\Db\Entity\StreetCacheSearchItem;
use App\Db\Entity\User;
use App\Db\Entity\UserRequestHistory;
use App\Http\Service\Google\FakeGeocodingService;
use App\Http\Service\Google\GeocodingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    const URL = 'api/search/';

    protected function setUp(): void
    {
        parent::setUp();

        $fakeService = $this->app->make(FakeGeocodingService::class);
        $this->instance(GeocodingService::class, $fakeService);
    }

    public function testShouldReceiveSearchAndAddCity()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();
        /** @var User $user */
        $user = User::factory()->create();

        $citiesCount = City::query()->count();
        $this->assertEquals(0, $citiesCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(0, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(0, $citiesCacheItemsCount);
        $response = $this->postJsonAuthWithToken(
            self::URL.'city',
            $user->getJWTToken(),
            [
                'searchString' => 'Test'
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'shortName',
                'country' => [],
            ]
        ]);

        $userRequestHistory = UserRequestHistory::query()
            ->where('user_id', $user->id)
            ->exists();
        $this->assertTrue($userRequestHistory);

        $citiesCount = City::query()->count();
        $this->assertEquals(2, $citiesCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(1, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(2, $citiesCacheItemsCount);
    }

    public function testShouldNotReceiveSearchAndAddCityHasNotAccessToApi()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'is_has_access_to_api' => false
        ]);

        $citiesCount = City::query()->count();
        $this->assertEquals(0, $citiesCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(0, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(0, $citiesCacheItemsCount);
        $response = $this->postJsonAuthWithToken(
            self::URL.'city',
            $user->getJWTToken(),
            [
                'searchString' => 'Test'
            ]
        );

        $response->assertStatus(403);
    }

    public function testShouldNotReceiveSearchAndAddCityEndAccessToApi()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'is_has_access_to_api' => true,
            'end_access_to_api_at' => Carbon::now()->subDay()->toDateString()
        ]);

        $citiesCount = City::query()->count();
        $this->assertEquals(0, $citiesCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(0, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(0, $citiesCacheItemsCount);
        $response = $this->postJsonAuthWithToken(
            self::URL.'city',
            $user->getJWTToken(),
            [
                'searchString' => 'Test'
            ]
        );

        $response->assertStatus(403);
    }

    public function testShouldReceiveSearchAlreadyExistCity()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $street = $this->createStreet();

        /** @var CityCacheSearch $cityCacheSearch */
        $cityCacheSearch = CityCacheSearch::factory()->create([
            'search_string' => 'Test'
        ]);

        /** @var CityCacheSearchItem $cityCacheSearchItem */
        $cityCacheSearchItem = CityCacheSearchItem::factory()->create([
            'city_id' => $street->city->id,
            'city_cache_search_id' => $cityCacheSearch->id
        ]);

        $citiesCount = City::query()->count();
        $this->assertEquals(1, $citiesCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(1, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(1, $citiesCacheItemsCount);
        $response = $this->postJsonAuthWithToken(
            self::URL.'city',
            $user->getJWTToken(),
            [
                'searchString' => 'Test'
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            [
                'id' => $cityCacheSearchItem->city->id,
                'name' => $cityCacheSearchItem->city->name,
                'shortName' => $cityCacheSearchItem->city->short_name,
                'country' => [
                    'id' => $cityCacheSearchItem->city->country->id,
                    'name' => $cityCacheSearchItem->city->country->name,
                    'shortName' => $cityCacheSearchItem->city->country->short_name,
                ]
            ]
        ]);

        $citiesCount = City::query()->count();
        $this->assertEquals(1, $citiesCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(1, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(1, $citiesCacheItemsCount);
    }

    public function testShouldNotReceiveSearchCityValidationError()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->postJsonAuthWithToken(
            self::URL.'city',
            $user->getJWTToken(),
            [
                'searchString' => 123
            ]
        );

        $response->assertStatus(400);
    }

    public function testShouldReceiveSearchAndAddStreet()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var CompaniesCache $companyCache */
        $companyCache = CompaniesCache::factory()->create([
            'transport_number' => 32
        ]);

        CompaniesCacheName::factory()->count(10)->create([
            'companies_cache_id' => $companyCache->id
        ]);

        /** @var Country $country */
        $country = Country::factory()->create([
            'name' => 'Russia',
            'short_name' => 'RU'
        ]);
        /** @var City $city */
        $city = City::factory()->create([
            'name' => 'Москва',
            'short_name' => 'Москва',
            'region' => 'Москва',
            'country_id' => $country->id
        ]);

        $streetsCount = Street::query()->count();
        $this->assertEquals(0, $streetsCount);
        $streetsCacheCount = StreetCacheSearch::query()->count();
        $this->assertEquals(0, $streetsCacheCount);
        $streetsCacheItemsCount = StreetCacheSearchItem::query()->count();
        $this->assertEquals(0, $streetsCacheItemsCount);
        $response = $this->postJsonAuthWithToken(
            self::URL.'street',
            $user->getJWTToken(),
            [
                'cityId' => $city->id,
                'searchString' => 'лен',
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                'id',
                'name',
                'shortName',
                'city' => [
                    'id',
                    'name',
                    'shortName',
                    'country' => []
                ]
            ]
        ]);

        $userRequestHistory = UserRequestHistory::query()
            ->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);

        $streetsCacheCount = StreetCacheSearch::query()->count();
        $this->assertEquals(1, $streetsCacheCount);
    }

    public function testShouldReceiveSearchAlreadyExistStreet()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $street = $this->createStreet();

        /** @var CompaniesCache $companyCache */
        $companyCache = CompaniesCache::factory()->create([
            'transport_number' => 32
        ]);

        CompaniesCacheName::factory()->count(10)->create([
            'companies_cache_id' => $companyCache->id
        ]);

        /** @var StreetCacheSearch $streetCacheSearch */
        $streetCacheSearch = StreetCacheSearch::factory()->create([
            'search_string' => 'Test',
            'city_id' => $street->city->id
        ]);

        /** @var StreetCacheSearchItem $streetCacheSearchItem */
        $streetCacheSearchItem = StreetCacheSearchItem::factory()->create([
            'street_id' => $street->id,
            'street_cache_search_id' => $streetCacheSearch->id
        ]);

        $citiesCount = City::query()->count();
        $this->assertEquals(1, $citiesCount);
        $streetsCount = Street::query()->count();
        $this->assertEquals(1, $streetsCount);
        $streetsCacheCount = StreetCacheSearch::query()->count();
        $this->assertEquals(1, $streetsCacheCount);
        $streetsCacheItemsCount = StreetCacheSearchItem::query()->count();
        $this->assertEquals(1, $streetsCacheItemsCount);
        $response = $this->postJsonAuthWithToken(
            self::URL.'street',
            $user->getJWTToken(),
            [
                'cityId' => $street->city->id,
                'searchString' => 'Test',
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            [
                'id' => $street->id,
                'name' => $street->name,
                'shortName' => $street->short_name,
                'city' => [
                    'id' => $street->city->id,
                    'name' => $street->city->name,
                    'shortName' => $street->city->short_name,
                    'country' => [
                        'id' => $street->city->country->id,
                        'name' => $street->city->country->name,
                        'shortName' => $street->city->country->short_name,
                    ]
                ]
            ]
        ]);

        $citiesCount = City::query()->count();
        $this->assertEquals(1, $citiesCount);
        $streetsCount = Street::query()->count();
        $this->assertEquals(1, $streetsCount);
        $streetsCacheCount = StreetCacheSearch::query()->count();
        $this->assertEquals(1, $streetsCacheCount);
    }

    public function testShouldReceiveSearchAlreadyExistStreetInAnotherCity()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $street = $this->createStreet();
        $street2 = $this->createStreet();

        /** @var CompaniesCache $companyCache */
        $companyCache = CompaniesCache::factory()->create([
            'transport_number' => 32
        ]);

        CompaniesCacheName::factory()->count(10)->create([
            'companies_cache_id' => $companyCache->id
        ]);

        /** @var StreetCacheSearch $streetCacheSearch */
        $streetCacheSearch = StreetCacheSearch::factory()->create([
            'search_string' => 'Test',
            'city_id' => $street->city->id
        ]);

        /** @var StreetCacheSearchItem $streetCacheSearchItem */
        $streetCacheSearchItem = StreetCacheSearchItem::factory()->create([
            'street_id' => $street->id,
            'street_cache_search_id' => $streetCacheSearch->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'street',
            $user->getJWTToken(),
            [
                'cityId' => $street2->city->id,
                'searchString' => 'Test',
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([]);

        $citiesCount = City::query()->count();
        $this->assertEquals(2, $citiesCount);
        $streetsCount = Street::query()->count();
        $this->assertEquals(2, $streetsCount);
        $streetsCacheCount = StreetCacheSearch::query()->count();
        $this->assertEquals(1, $streetsCacheCount);
    }

    public function testShouldNotReceiveSearchStreetValidationError()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->postJsonAuthWithToken(
            self::URL.'street',
            $user->getJWTToken(),
            [
                'searchString' => 123
            ]
        );

        $response->assertStatus(400);
    }
}
