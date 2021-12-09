<?php

namespace Tests\Feature\Api;

use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\CompaniesCachePayment;
use App\Db\Entity\Country;
use App\Db\Entity\User;
use App\Db\Entity\UserRequestHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyPaymentControllerTest extends TestCase
{
    const URL = 'api/company/';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldReceiveCompanyPayment()
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
        $companiesCachePayment = CompaniesCachePayment::query()
            ->where([
                'token' => $hash
            ])
            ->exists();
        $this->assertFalse($companiesCachePayment);

        $response = $this->postJsonAuthWithToken(
            self::URL.'paymentMethods',
            $user->getJWTToken(),
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'paymentTypes',
            'payerTypes'
        ]);
        $companiesCachePayment = CompaniesCachePayment::query()
            ->where([
                'token' => $hash
            ])
            ->exists();
        $this->assertTrue($companiesCachePayment);

        $userRequestHistory = UserRequestHistory::query()->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);
    }

    public function testShouldReceiveCompanyPaymentAlreadyExists()
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

        /** @var CompaniesCachePayment $companiesCachePayment */
        $companiesCachePayment = CompaniesCachePayment::factory()->create([
            'token' => $hash
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'paymentMethods',
            $user->getJWTToken(),
            $data
        );

        $response->assertStatus(200);
    }

    public function testShouldNotReceiveCompanyPaymentValidationError()
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
            self::URL.'paymentMethods',
            $user->getJWTToken(),
            [
                'data' =>
                    [
                        'transportNumber' => 999,
                        'cargoFrom' => 999,
                        'cargoTo' => 999,
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
            ]
        );

        $response->assertStatus(400);
    }
}
