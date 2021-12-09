<?php

namespace Tests\Feature\Api;

use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\CompaniesCachePayment;
use App\Db\Entity\CompaniesCacheTerminal;
use App\Db\Entity\Country;
use App\Db\Entity\User;
use App\Db\Entity\UserRequestHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTerminalControllerTest extends TestCase
{
    const URL = 'api/company/';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldReceiveCompanyTerminal()
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
        /** @var City $city */
        $city = City::factory()->create([
            'name' => 'Москва',
            'country_id' => $country->id
        ]);

        $data = [
            'data' =>
                [
                    'transportNumber' => 32,
                    'cityId' => $city->id,
                    'lang' => 'ru',
                    'isArrival' => 0,
                    'isDerival' => 1,
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
        $companiesCacheTerminal = CompaniesCacheTerminal::query()
            ->where([
                'token' => $hash
            ])
            ->exists();
        $this->assertFalse($companiesCacheTerminal);

        $response = $this->postJsonAuthWithToken(
            self::URL.'terminals',
            $user->getJWTToken(),
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'arrivalTerminalBlocks',
            'derivalTerminalBlocks'
        ]);
        $companiesCacheTerminal = CompaniesCacheTerminal::query()
            ->where([
                'token' => $hash
            ])
            ->exists();
        $this->assertTrue($companiesCacheTerminal);

        $userRequestHistory = UserRequestHistory::query()->where('user_id', $user->id)->exists();
        $this->assertTrue($userRequestHistory);
    }

    public function testShouldReceiveCompanyTerminalAlreadyExists()
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
        /** @var City $city */
        $city = City::factory()->create([
            'name' => 'Москва',
            'country_id' => $country->id
        ]);

        $data = [
            'data' =>
                [
                    'transportNumber' => 32,
                    'cityId' => $city->id,
                    'lang' => 'ru',
                    'isArrival' => 0,
                    'isDerival' => 1,
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

        /** @var CompaniesCacheTerminal $companiesCacheTerminal */
        $companiesCacheTerminal = CompaniesCacheTerminal::factory()->create([
            'token' => $hash
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'terminals',
            $user->getJWTToken(),
            $data
        );

        $response->assertStatus(200);
    }

    public function testShouldNotReceiveCompanyTerminalValidationError()
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

        $response = $this->postJsonAuthWithToken(
            self::URL.'options',
            $user->getJWTToken(),
            [
                'data' =>
                    [
                        'transportNumber' => 999,
                        'cargoFrom' => 999,
                        'cargoTo' => 999,
                        'lang' => 'ru',
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
