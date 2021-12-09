<?php

namespace Tests\Feature\Api;

use App\Constant\ScheduleCalculationStatus;
use App\Db\Entity\CalculationCache;
use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\Country;
use App\Db\Entity\ScheduleCalculation;
use App\Db\Entity\Street;
use App\Db\Entity\User;
use App\Db\Entity\UserRequestHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ScheduleCalculationControllerTest extends TestCase
{
    const URL = 'api/calculationQueue/';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldCreateScheduleCalculation()
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
            self::URL.'scheduleCalculation',
            $user->getJWTToken(),
            [
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
                'callbackUrl' => 'http://test.url'
            ]
        );

        $response->assertStatus(200);

        $scheduleCalculation = ScheduleCalculation::byId($response->json('id'));
        $this->assertEquals(
            ScheduleCalculationStatus::$Failed->getValue(),
            $scheduleCalculation->status
        );
        $userRequestHistory = UserRequestHistory::query()->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);
    }

    public function testShouldCreateScheduleCalculationWithEmptyResponse()
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
            'short_name' => 'QWE'
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
            self::URL.'scheduleCalculation',
            $user->getJWTToken(),
            [
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
                'callbackUrl' => 'qwerty'
            ]
        );

        $response->assertStatus(200);

        $scheduleCalculation = ScheduleCalculation::byId($response->json('id'));
        $this->assertEquals(
            ScheduleCalculationStatus::$EmptyResponse->getValue(),
            $scheduleCalculation->status
        );
        $userRequestHistory = UserRequestHistory::query()->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);
    }
}
