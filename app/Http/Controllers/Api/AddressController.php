<?php

namespace App\Http\Controllers\Api;

use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\StreetCacheSearch;
use App\Db\Service\AddressDao;
use App\Db\Service\CityCacheSearchDao;
use App\Db\Service\StreetCacheSearchDao;
use App\Db\Service\UserRequestHistoryDao;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Address\SearchCityRequest;
use App\Http\Requests\Address\SearchStreetRequest;
use App\Http\Service\CargoGuru\ApiService;
use App\Http\Service\Google\GeocodingService;
use App\Service\AuthService;
use Illuminate\Http\JsonResponse;

class AddressController extends BaseController
{
    private AddressDao $addressDao;
    private UserRequestHistoryDao $userRequestHistoryDao;
    private GeocodingService $geocodingService;
    private ApiService $apiService;
    /**
     * @var CityCacheSearchDao
     */
    private CityCacheSearchDao $cityCacheSearchDao;
    /**
     * @var StreetCacheSearchDao
     */
    private StreetCacheSearchDao $streetCacheSearchDao;

    public function __construct(
        AuthService $authService,
        AddressDao $AddressDao,
        UserRequestHistoryDao $userRequestHistoryDao,
        GeocodingService $geocodingService,
        ApiService $apiService,
        CityCacheSearchDao $cityCacheSearchDao,
        StreetCacheSearchDao $streetCacheSearchDao
    ) {
        parent::__construct($authService);
        $this->addressDao = $AddressDao;
        $this->userRequestHistoryDao = $userRequestHistoryDao;
        $this->geocodingService = $geocodingService;
        $this->apiService = $apiService;
        $this->cityCacheSearchDao = $cityCacheSearchDao;
        $this->streetCacheSearchDao = $streetCacheSearchDao;
    }

    public function actionSearchCity(SearchCityRequest $request): JsonResponse
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );
        $citiesFromCache = $this->cityCacheSearchDao->getCitiesFromCache($request->searchString);
        if (count($citiesFromCache)) {
            return $this->json($citiesFromCache);
        } else {
            /** @var City[] $cities */
            $cities = [];
            $addresses = $this->geocodingService->getAddressBySearchString($request->searchString);
            foreach ($addresses as $address) {
                if ($address->isValidCity()) {
                    $cities[] = $this->addressDao->addCity($address, $request->searchString);
                }
            }
            return $this->json($cities);
        }
    }

    public function actionSearchStreet(SearchStreetRequest $request): JsonResponse
    {
        $this->userRequestHistoryDao->createUserRequest(
            $request->all(),
            $request->getRequestUri(),
            $this->authService->getUser()
        );
        $streetsFromCache = $this->streetCacheSearchDao->getStreetsFromCache($request);
        if (count($streetsFromCache)) {
            return $this->json($streetsFromCache);
        } else {
            $streets = [];
            $streetResults = $this->apiService->getStreets($request);
            foreach ($streetResults as $streetResult) {
                $streets[] = $this->addressDao->addStreet($request, $streetResult);
            }
            return $this->json($streets);
        }
    }
}
