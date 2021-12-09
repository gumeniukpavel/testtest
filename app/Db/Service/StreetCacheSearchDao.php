<?php

namespace App\Db\Service;

use App\Db\Entity\City;
use App\Db\Entity\Street;
use App\Db\Entity\StreetCacheSearch;
use App\Db\Entity\StreetCacheSearchItem;
use App\Http\Requests\Address\SearchStreetRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StreetCacheSearchDao
{
    public function getStreetsFromCache(SearchStreetRequest $request): Collection
    {
        /** @var StreetCacheSearch $cacheSearch */
        $cacheSearch = StreetCacheSearch::query()
            ->whereRaw('LOWER(`search_string`) LIKE ?', ['%'.$request->searchString.'%'])
            ->where('city_id', $request->cityId)
            ->first();

        if (!$cacheSearch) {
            return new Collection();
        }
        $searchResult = $cacheSearch->streetCacheSearchItems()
            ->with('street.city.country')
            ->get();
        /** @var City[] | Collection $cities */
        $cities = $searchResult->pluck('street');
        return $cities;
    }

    public function addItem(string $searchString, Street $street)
    {
        /** @var StreetCacheSearch $cacheSearch */
        $cacheSearch = StreetCacheSearch::query()
            ->where('search_string', $searchString)
            ->where('city_id', $street->city->id)
            ->first();

        if (!$cacheSearch) {
            $cacheSearch = new StreetCacheSearch();
            $cacheSearch->search_string = $searchString;
            $cacheSearch->city_id = $street->city->id;
            $cacheSearch->save();
        }
        $existsItem = $cacheSearch->streetCacheSearchItems()
            ->where('street_id', $street->id)
            ->exists();
        if (!$existsItem) {
            $cityCacheSearchItem = new StreetCacheSearchItem();
            $cityCacheSearchItem->street_id = $street->id;
            $cacheSearch->streetCacheSearchItems()->save($cityCacheSearchItem);
        }
    }

    public function clear()
    {
        StreetCacheSearchItem::query()->delete();
        StreetCacheSearch::query()->delete();
    }

    public function clearCache()
    {
        /** @var StreetCacheSearch[] $streetCacheSearchResults */
        $streetCacheSearchResults = StreetCacheSearch::query()
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->get();

        foreach ($streetCacheSearchResults as $cacheSearchResult) {
            $cacheSearchResult->streetCacheSearchItems()->delete();
            $cacheSearchResult->delete();
        }
    }
}
