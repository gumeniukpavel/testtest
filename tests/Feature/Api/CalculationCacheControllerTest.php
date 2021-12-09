<?php

namespace Tests\Feature\Api;

use App\Db\Entity\CalculationCache;
use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\Country;
use App\Db\Entity\Street;
use App\Db\Entity\User;
use App\Db\Entity\UserRequestHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculationCacheControllerTest extends TestCase
{
    const URL = 'api/';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldReceiveCalculation()
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
        /** @var Street $street1 */
        $street1 = Street::factory()->create([
            'city_id' => $city1->id
        ]);
        /** @var Street $street2 */
        $street2 = Street::factory()->create([
            'city_id' => $city2->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'getCalculation',
            $user->getJWTToken(),
            [
                'transportNumber' => '32',
                'cityFrom' => $city1->id,
                'isDerivalByCourier' => '0',
                'isArrivalByCourier' => '0',
                'paymentType' => 1,
                'cargoFromStreet' => $street1->id,
                'cargoToStreet' => $street2->id,
                'cityTo' => $city2->id,
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
                    ]
                ],
                'payerType' => 1,
                'places' => [
                    0 => [
                        'cargoGoodsName' => '',
                        'cargoWeight' => '1',
                        'cargoVol' => '0.001',
                        'cargoGoodsPrice' => '0',
                        'cargoLength' => '0.1',
                        'cargoWidth' => '0.1',
                        'cargoHeight' => '0.1',
                        'cargoDocument' => '',
                    ],
                ],
            ]
        );

        $response->assertStatus(200);

        $userRequestHistory = UserRequestHistory::query()->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);
    }

    public function testShouldReceiveCalculationWithoutStreet()
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

        $response = $this->postJsonAuthWithToken(
            self::URL.'getCalculation',
            $user->getJWTToken(),
            [
                'transportNumber' => 32,
                'cityFrom' => $city1->id,
                'isDerivalByCourier' => '0',
                'isArrivalByCourier' => '0',
                'paymentType' => 1,
                'cityTo' => $city2->id,
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
                    ]
                ],
                'payerType' => 1,
                'places' => [
                    0 => [
                        'cargoGoodsName' => '',
                        'cargoWeight' => '1',
                        'cargoVol' => '0.001',
                        'cargoGoodsPrice' => '0',
                        'cargoLength' => '0.1',
                        'cargoWidth' => '0.1',
                        'cargoHeight' => '0.1',
                        'cargoDocument' => '',
                    ],
                ],
            ]
        );

        $response->assertStatus(200);

        $userRequestHistory = UserRequestHistory::query()->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);
    }

    public function testShouldNotReceiveCalculationValidationError()
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

        $response = $this->postJsonAuthWithToken(
            self::URL.'getCalculation',
            $user->getJWTToken(),
            [
                'transportNumber' => 99999,
                'cityFrom' => 99999,
                'isDerivalByCourier' => '0',
                'isArrivalByCourier' => '0',
                'paymentType' => 1,
                'cargoFromStreet' => '',
                'cargoToStreet' => '',
                'cityTo' => 999999,
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
                ],
                'payerType' => 1,
                'places' => [
                    0 => [
                        'cargoGoodsName' => '',
                        'cargoWeight' => '1',
                        'cargoVol' => '0.001',
                        'cargoGoodsPrice' => '0',
                        'cargoLength' => '0.1',
                        'cargoWidth' => '0.1',
                        'cargoHeight' => '0.1',
                        'cargoDocument' => '',
                    ],
                ],
            ]
        );

        $response->assertStatus(400);
    }

    public function testShouldReceiveCalculationAlreadyExists()
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
        /** @var Street $street1 */
        $street1 = Street::factory()->create([
            'city_id' => $city1->id
        ]);
        /** @var Street $street2 */
        $street2 = Street::factory()->create([
            'city_id' => $city2->id
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
                'isPresentTerminalsFrom' => 1,
                'isPresentTerminalsTo' => 1,
            ],
            'payerType' => 1,
            'places' => [
                0 => [
                    'cargoGoodsName' => '',
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

        /** @var CalculationCache $calculationCache */
        $calculationCache = CalculationCache::factory()->create([
            'token' => hash('sha256', json_encode($data))
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'getCalculation',
            $user->getJWTToken(),
            $data
        );

        $response->assertStatus(200);
    }
}
