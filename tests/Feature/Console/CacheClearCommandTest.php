<?php

namespace Tests\Feature\Console;

use App\Console\Commands\CacheClearCommand;
use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
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

class CacheClearCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldClearCache()
    {
        $street = $this->createStreet();

        /** @var CityCacheSearch[] $citiesCacheSearch1 */
        $citiesCacheSearch1 = CityCacheSearch::factory()->count(5)->create([
            'search_string' => 'Test',
            'created_at' => Carbon::now()->subDays(4)
        ]);

        foreach ($citiesCacheSearch1 as $cityCacheSearch) {
            CityCacheSearchItem::factory()->create([
                'city_id' => $street->city->id,
                'city_cache_search_id' => $cityCacheSearch->id
            ]);
        }

        /** @var CityCacheSearch[] $citiesCacheSearch2 */
        $citiesCacheSearch2 = CityCacheSearch::factory()->count(5)->create([
            'search_string' => 'Test'
        ]);

        foreach ($citiesCacheSearch2 as $cityCacheSearch) {
            CityCacheSearchItem::factory()->create([
                'city_id' => $street->city->id,
                'city_cache_search_id' => $cityCacheSearch->id
            ]);
        }

        /** @var StreetCacheSearch[] $streetsCacheSearch1 */
        $streetsCacheSearch1 = StreetCacheSearch::factory()->count(5)->create([
            'search_string' => 'Test',
            'city_id' => $street->city->id,
            'created_at' => Carbon::now()->subDays(4)
        ]);

        foreach ($streetsCacheSearch1 as $streetCacheSearch) {
            StreetCacheSearchItem::factory()->create([
                'street_id' => $street->id,
                'street_cache_search_id' => $streetCacheSearch->id
            ]);
        }

        /** @var StreetCacheSearch[] $streetsCacheSearch2 */
        $streetsCacheSearch2 = StreetCacheSearch::factory()->count(5)->create([
            'search_string' => 'Test',
            'city_id' => $street->city->id,
        ]);

        foreach ($streetsCacheSearch2 as $streetCacheSearch) {
            StreetCacheSearchItem::factory()->create([
                'street_id' => $street->id,
                'street_cache_search_id' => $streetCacheSearch->id
            ]);
        }

        /** @var Country $country */
        $country = Country::factory()->create();
        /** @var City $city1 */
        $city1 = City::factory()->create([
            'name' => 'Москва',
            'country_id' => $country->id
        ]);
        /** @var City $city2 */
        $city2 = City::factory()->create([
            'name' => 'Санкт-Петербург',
            'country_id' => $country->id
        ]);

        $data = [
            'data' =>
                [
                    'transportNumber' => 32,
                    'cargoFrom' => $city1->id,
                    'cargoTo' => $city2->id,
                    'lang' => 'ru',
                    'isArrivalByCourier' => 0,
                    'isDerivalByCourier' => 0,
                ],
            'modifies' =>
                [
                    'weight' => 1,
                    'length' => 0.1,
                    'width' => 0.1,
                    'height' => 0.1,
                    'volume' => 0.001,
                ],
        ];
        $hash = hash('sha256', json_encode($data));

        CompaniesCacheOption::factory()->count(5)->create([
            'token' => $hash,
            'created_at' => Carbon::now()->subDays(4)
        ]);
        CompaniesCacheOption::factory()->count(5)->create([
            'token' => $hash
        ]);

        CompaniesCachePayment::factory()->count(5)->create([
            'token' => $hash,
            'created_at' => Carbon::now()->subDays(4)
        ]);
        CompaniesCachePayment::factory()->count(5)->create([
            'token' => $hash
        ]);

        CompaniesCacheTerminal::factory()->count(5)->create([
            'token' => $hash,
            'created_at' => Carbon::now()->subDays(4)
        ]);
        CompaniesCacheTerminal::factory()->count(5)->create([
            'token' => $hash
        ]);

        $streetsCacheCount = StreetCacheSearch::query()->count();
        $this->assertEquals(10, $streetsCacheCount);
        $streetsCacheItemsCount = StreetCacheSearchItem::query()->count();
        $this->assertEquals(10, $streetsCacheItemsCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(10, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(10, $citiesCacheItemsCount);
        $companiesCacheOptionItemsCount = CompaniesCacheOption::query()->count();
        $this->assertEquals(10, $companiesCacheOptionItemsCount);
        $companiesCachePaymentItemsCount = CompaniesCachePayment::query()->count();
        $this->assertEquals(10, $companiesCachePaymentItemsCount);
        $companiesCacheTerminalItemsCount = CompaniesCacheTerminal::query()->count();
        $this->assertEquals(10, $companiesCacheTerminalItemsCount);

        $response = $this->artisan(CacheClearCommand::class)
            ->assertExitCode(0);

        $response->run();

        $streetsCacheCount = StreetCacheSearch::query()->count();
        $this->assertEquals(5, $streetsCacheCount);
        $streetsCacheItemsCount = StreetCacheSearchItem::query()->count();
        $this->assertEquals(5, $streetsCacheItemsCount);
        $citiesCacheCount = CityCacheSearch::query()->count();
        $this->assertEquals(5, $citiesCacheCount);
        $citiesCacheItemsCount = CityCacheSearchItem::query()->count();
        $this->assertEquals(5, $citiesCacheItemsCount);
        $companiesCacheOptionItemsCount = CompaniesCacheOption::query()->count();
        $this->assertEquals(5, $companiesCacheOptionItemsCount);
        $companiesCachePaymentItemsCount = CompaniesCachePayment::query()->count();
        $this->assertEquals(5, $companiesCachePaymentItemsCount);
        $companiesCacheTerminalItemsCount = CompaniesCacheTerminal::query()->count();
        $this->assertEquals(5, $companiesCacheTerminalItemsCount);
    }
}
