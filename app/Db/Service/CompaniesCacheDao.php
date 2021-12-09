<?php

namespace App\Db\Service;

use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheName;
use App\Db\Entity\CompaniesCacheOption;
use App\Db\Entity\CompaniesCachePayment;
use App\Db\Entity\CompaniesCacheTerminal;
use Carbon\Carbon;

class CompaniesCacheDao
{
    public function createCompaniesCache(array $company)
    {
        $companiesCache = CompaniesCache::query()->where([
            'transport_number' => $company['transportNumber']
        ])->first();
        if (!$companiesCache) {
            $companiesCache = new CompaniesCache();
        }
        $companiesCache->can_order_now = boolval($company['canOrderNow']);
        $companiesCache->transport_lang = $company['transportLang'];
        $companiesCache->transport_logo = $company['transportLogo'];
        $companiesCache->transport_name = $company['transportName'];
        $companiesCache->transport_number = $company['transportNumber'];
        $companiesCache->transport_site = $company['transportSite'];
        $companiesCache->save();

        $companyNames = $company['transportNames'];
        foreach ($companyNames as $lang => $companyName) {
            $companiesCacheName = CompaniesCacheName::query()->where([
                'lang' => $lang
            ])->first();
            if (!$companiesCacheName) {
                $companiesCacheName = new CompaniesCacheName();
            }
            $companiesCacheName->lang = $lang;
            $companiesCacheName->name = $companyName;
            $companiesCacheName->companies_cache_id = $companiesCache->id;
            $companiesCacheName->save();
        }

        return $companiesCache;
    }

    public function listQuery()
    {
        return CompaniesCache::query()->with('companiesCacheNames');
    }

    public function clearOptionsCache()
    {
        CompaniesCacheOption::query()
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->delete();
        CompaniesCachePayment::query()
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->delete();
        CompaniesCacheTerminal::query()
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->delete();
    }

    public function clearCompaniesCache()
    {
        /** @var CompaniesCache[] $companyCacheSearchResults */
        $companyCacheSearchResults = CompaniesCache::query()
            ->get();

        foreach ($companyCacheSearchResults as $cacheSearchResult) {
            $cacheSearchResult->companiesCacheNames()->delete();
            $cacheSearchResult->delete();
        }
    }
}
