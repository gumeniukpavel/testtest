<?php

namespace Tests\Feature\Console;

use App\Console\Commands\CacheClearCommand;
use App\Console\Commands\CalculationCacheClearCommand;
use App\Db\Entity\CalculationCache;
use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
use App\Db\Entity\Country;
use App\Db\Entity\Role;
use App\Db\Entity\StreetCacheSearch;
use App\Db\Entity\StreetCacheSearchItem;
use App\Db\Entity\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculationCacheClearCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldCalculationClearCache()
    {
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
            'transportNumber' => '32',
            'cityFrom' => $city2->id,
            'isDerivalByCourier' => '0',
            'isArrivalByCourier' => '0',
            'paymentType' => 1,
            'cargoFromStreet' => '',
            'cargoToStreet' => '',
            'cityTo' => $city1->id,
            'weight' => 1,
            'volume' => 0.001,
            'width' => 0.1,
            'length' => 0.1,
            'height' => 0.1,
            'currency' => 'RUB',
            'language' => 'ru',
            'insurancePrice' => 0,
            'options' => [
                'derivalTerminalId' => 36,
                'arrivalTerminalId' => 1,
                'packageType' => [
                    0 => '2',
                ],
                'tariffType' => 1,
                'isPresentTerminalsFrom' => 1,
                'isPresentTerminalsTo' => 1,
            ],
            'payerType' => 1,
            'places' => [
                0 => [
                    'cargoGoodsName' => '',
                    'cargoTemperatureModeId' => '1',
                    'cargoDangerClassId' => '1',
                    'cargoWeight' => '1',
                    'cargoVol' => '0.001',
                    'cargoGoodsPrice' => '0',
                    'cargoLength' => '0.1',
                    'cargoWidth' => '0.1',
                    'cargoHeight' => '0.1',
                    'cargoDocument' => '',
                ],
            ],
        ];

        CalculationCache::factory()->count(5)->create([
            'token' => hash('sha256', json_encode($data)),
            'created_at' => Carbon::now()->subDay()->subMinutes(10)
        ]);

        CalculationCache::factory()->count(5)->create([
            'token' => hash('sha256', json_encode($data))
        ]);

        $calculationCache = CalculationCache::query()->count();
        $this->assertEquals(10, $calculationCache);

        $response = $this->artisan(CalculationCacheClearCommand::class)
            ->assertExitCode(0);

        $response->run();

        $calculationCache = CalculationCache::query()->count();
        $this->assertEquals(5, $calculationCache);
    }
}
