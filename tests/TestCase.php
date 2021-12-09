<?php

namespace Tests;

use App\Db\Entity\City;
use App\Db\Entity\Country;
use App\Db\Entity\Role;
use App\Db\Entity\Street;
use App\Db\Entity\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Core\CustomTestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $seed = true;

    public function createAdmin()
    {
        return User::factory()->create([
            'role_id' => Role::ROLE_ADMIN
        ]);
    }

    /**
     * Visit the given URI with a POST request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  string  $token
     * @param  array  $data
     * @param  array  $headers
     * @return CustomTestResponse
     */
    public function postJsonAuthWithToken(
        $uri,
        string $token,
        array $data = [],
        array $headers = []
    ): CustomTestResponse {
        $headers = array_merge($headers, [
            'Authorization' => 'Bearer '.$token
        ]);
        $response = $this->postJson($uri, $data, $headers);
        return CustomTestResponse::fromBaseResponse($response->baseResponse);
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $headers
     * @return CustomTestResponse
     */
    public function getJson($uri, array $headers = []): CustomTestResponse
    {
        $response = parent::getJson($uri, $headers);
        return CustomTestResponse::fromBaseResponse($response->baseResponse);
    }

    /**
     * Visit the given URI with a POST request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @return CustomTestResponse
     */
    public function postJson($uri, array $data = [], array $headers = []): CustomTestResponse
    {
        $response = parent::postJson($uri, $data, $headers);
        return CustomTestResponse::fromBaseResponse($response->baseResponse);
    }

    /**
     * Visit the given URI with a GET request, expecting a JSON response.
     *
     * @param  string  $uri
     * @param  string  $token
     * @param  array  $headers
     * @return CustomTestResponse
     */
    public function getJsonAuthWithToken($uri, string $token, array $headers = []): CustomTestResponse
    {
        $headers = array_merge($headers, [
            'Authorization' => 'Bearer '.$token
        ]);
        $response = $this->getJson($uri, $headers);
        return CustomTestResponse::fromBaseResponse($response->baseResponse);
    }

    protected function createStreet(): Street
    {
        /** @var Country $country */
        $country = Country::factory()->create();
        /** @var City $city */
        $city = City::factory()->create([
            'country_id' => $country->id
        ]);
        /** @var Street $street */
        $street = Street::factory()->create([
            'city_id' => $city->id
        ]);
        return $street;
    }
}
