<?php

namespace App\Db\Service;

use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
use App\Db\Entity\Country;
use App\Db\Entity\Street;
use App\Db\Entity\StreetCacheSearch;
use App\Db\Entity\StreetCacheSearchItem;
use App\Http\Requests\Address\SearchStreetRequest;
use App\Http\Service\CargoGuru\StreetModel;
use App\Http\Service\Google\LocationInfoModel;
use Illuminate\Support\Facades\Http;

class AddressDao
{
    /**
     * @var CityCacheSearchDao
     */
    private CityCacheSearchDao $cityCacheSearchDao;
    /**
     * @var StreetCacheSearchDao
     */
    private StreetCacheSearchDao $streetCacheSearchDao;

    public function __construct(
        CityCacheSearchDao $cityCacheSearchDao,
        StreetCacheSearchDao $streetCacheSearchDao
    ) {
        $this->cityCacheSearchDao = $cityCacheSearchDao;
        $this->streetCacheSearchDao = $streetCacheSearchDao;
    }

    public function addCity(LocationInfoModel $locationInfoModel, string $searchString): City
    {
        $city = $this->findOrCreateCity($locationInfoModel);
        $this->cityCacheSearchDao->addItem($searchString, $city);
        $city->load('country');
        return $city;
    }

    public function findOrCreateCity(LocationInfoModel $locationInfoModel): City
    {
        $country = Country::query()->where('name', $locationInfoModel->country)->first();
        if (!$country) {
            $country = new Country();
            $country->name = $locationInfoModel->country;
            $country->short_name = $locationInfoModel->countryShortName;
            $country->save();
        }

        $city = $country->cities()
            ->where('name', $locationInfoModel->city)
            ->first();

        if (!$city) {
            $city = new City();
            $city->name = $locationInfoModel->city;
            $city->short_name = $locationInfoModel->cityShortName;
            $city->region = $locationInfoModel->region;
            $country->cities()->save($city);
        }
        return $city;
    }

    public function addStreet(SearchStreetRequest $request, StreetModel $streetModel): Street
    {
        /** @var City $city */
        $city = City::query()
            ->where('id', $request->cityId)
            ->first();

        $street = $city->streets()
            ->where('name', $streetModel->label)
            ->first();

        if (!$street) {
            $street = new Street();
            $street->name = $streetModel->label;
            $street->short_name = $streetModel->label;
            $city->streets()->save($street);
        }
        $this->streetCacheSearchDao->addItem($request->searchString, $street);
        $street->load('city.country');
        return $street;
    }
}
