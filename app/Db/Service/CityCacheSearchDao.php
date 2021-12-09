<?php

namespace App\Db\Service;

use App\Db\Entity\City;
use App\Db\Entity\CityCacheSearch;
use App\Db\Entity\CityCacheSearchItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CityCacheSearchDao
{
    /**
     * @param  string  $searchString
     * @return Collection | City[]
     */
    public function getCitiesFromCache(string $searchString): Collection
    {
        /** @var CityCacheSearch $cityCacheSearch */
        $cityCacheSearch = CityCacheSearch::query()
            ->whereRaw('LOWER(`search_string`) LIKE ?', ['%'.$searchString.'%'])
            ->first();
        if (!$cityCacheSearch) {
            return new Collection();
        }
        $searchResult = $cityCacheSearch->cityCacheSearchItems()
            ->with('city.country')
            ->get();
        /** @var City[] | Collection $cities */
        $cities = $searchResult->pluck('city');
        return $cities;
    }

    public function addItem(string $searchString, City $city)
    {
        /** @var CityCacheSearch $cityCacheSearch */
        $cityCacheSearch = CityCacheSearch::query()
            ->where('search_string', $searchString)
            ->first();

        if (!$cityCacheSearch) {
            $cityCacheSearch = new CityCacheSearch();
            $cityCacheSearch->search_string = $searchString;
            $cityCacheSearch->save();
        }
        $existsItem = $cityCacheSearch->cityCacheSearchItems()->where('city_id', $city->id)->exists();
        if (!$existsItem) {
            $cityCacheSearchItem = new CityCacheSearchItem();
            $cityCacheSearchItem->city_id = $city->id;
            $cityCacheSearch->cityCacheSearchItems()->save($cityCacheSearchItem);
        }
    }

    public function clear()
    {
        CityCacheSearchItem::query()->delete();
        CityCacheSearch::query()->delete();
    }

    public function clearCache()
    {
        /** @var CityCacheSearch[] $cityCacheSearchResults */
        $cityCacheSearchResults = CityCacheSearch::query()
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->get();

        foreach ($cityCacheSearchResults as $cacheSearchResult) {
            $cacheSearchResult->cityCacheSearchItems()->delete();
            $cacheSearchResult->delete();
        }
    }
}
