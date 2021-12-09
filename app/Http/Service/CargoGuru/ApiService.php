<?php

namespace App\Http\Service\CargoGuru;

use App\Constant\ScheduleCalculationCompanyStatus;
use App\Db\Entity\CalculationCache;
use App\Db\Entity\City;
use App\Db\Entity\CompaniesCache;
use App\Db\Entity\CompaniesCacheOption;
use App\Db\Entity\CompaniesCachePayment;
use App\Db\Entity\CompaniesCacheTerminal;
use App\Db\Entity\ScheduleCalculation;
use App\Db\Entity\ScheduleCalculationCompany;
use App\Db\Entity\Street;
use App\Http\Requests\Address\SearchStreetRequest;
use App\Http\Requests\CalculationCache\GetCalculationRequest;
use App\Http\Requests\CompanyTerminal\GetCompanyTerminalsRequest;
use App\Http\Requests\GetCompanyOrderOptionsRequest;
use App\Http\Requests\ScheduleCalculation\CreateScheduleCalculationRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    private string $apiKey;
    private string $apiUrl;
    private string $mainApiUrl;

    public function __construct()
    {
        $this->apiKey = config('app.api_key');
        $this->apiUrl = config('app.api_url');
        $this->mainApiUrl = config('app.main_api_url');
    }

    public function getStreets(SearchStreetRequest $request)
    {
        /** @var City $city */
        $city = City::query()->where('id', $request->cityId)->first();

        $data = [
            'city' => $city->name,
            'country' => $city->country->short_name,
            'region' => $city->region,
            'type' => 'suggestions',
            'street' => $request->searchString
        ];

        $response = Http::withHeaders([
            'api-key' => config('app.api_key')
        ])->post(config('app.main_api_url').'/suggestions.php', $data);

        if ($response->status() == 200) {
            if ($response->json()) {
                return StreetModel::parseFromJson($response->json());
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public function getActualCompanies(ScheduleCalculation $scheduleCalculation)
    {
        /** @var CreateScheduleCalculationRequest $data */
        $data = json_decode($scheduleCalculation->data);

        /** @var City $cityTo */
        $cityTo = City::query()->where('id', $data->cityTo)->first();
        /** @var City $cityFrom */
        $cityFrom = City::query()->where('id', $data->cityFrom)->first();

        $data = [
            'data' => [],
            'modifies' => [
                'countryFrom' => $cityFrom->country->short_name,
                'countryTo' => $cityTo->country->short_name
            ]
        ];

        $response = Http::withHeaders([
            'api-key' => config('app.api_key')
        ])->post(config('app.api_url').'/3/get_actual_companies.php', $data);

        if ($response->status() == 200) {
            return $response->json()['companies'];
        } else {
            return null;
        }
    }

    public function getScheduleCalculation(
        ScheduleCalculation $scheduleCalculation,
        ScheduleCalculationCompany $scheduleCalculationCompany
    ) {
        /** @var array $data */
        $data = json_decode($scheduleCalculation->data, true);

        /** @var City $cityTo */
        $cityTo = City::query()->where('id', $data['cityTo'])->first();
        /** @var City $cityFrom */
        $cityFrom = City::query()->where('id', $data['cityFrom'])->first();

        unset($data['callbackUrl']);
        $data['transportNumber'] = 32;
        $data['cityTo'] = $cityTo->name;
        $data['cityFrom'] = $cityFrom->name;
        $data['cargoToStreet'] = '';
        $data['cargoFromStreet'] = '';
        $data['cargoFromCountry'] = $cityFrom->country->short_name;
        $data['cargoToCountry'] = $cityTo->country->short_name;
        $data['cargoFromState'] = '';
        $data['cargoToState'] = '';
        foreach ($data['places'] as &$place) {
            $place['cargoWeightTypeID'] = 1;
            $place['cargoVolTypeID'] = 1;
            $place['cargoTemperatureModeId'] = 1;
            $place['cargoDangerClassId'] = 1;
        }
        $data['cargoComposition'] = 1;
        $data['userDiscount'] = 2;
        $data['options']['tariffType'] = 1;

        $response = Http::withHeaders([
            'api-key' => config('app.api_key')
        ])->post(config('app.api_url').'/3/get_delivery_calculation.php', $data);

        if ($response->status() == 200) {
            if ($response->json('failReason')) {
                $scheduleCalculationCompany->status = ScheduleCalculationCompanyStatus::$Failed;
                $scheduleCalculationCompany->response = json_encode($response->json());
                $scheduleCalculationCompany->save();
            } else {

                $scheduleCalculationCompany->status = ScheduleCalculationCompanyStatus::$Completed;
                $scheduleCalculationCompany->response = json_encode($response->json());
                $scheduleCalculationCompany->save();
            }
        } else {
            $scheduleCalculationCompany->status = ScheduleCalculationCompanyStatus::$Failed;
            $scheduleCalculationCompany->response = json_encode($response->json());
            $scheduleCalculationCompany->save();
        }
    }

    public function getCalculation(GetCalculationRequest $request)
    {
        $body = $request->all();
        $hash = hash('sha256', json_encode($body));

        /** @var CalculationCache $calculationCache */
        $calculationCache = CalculationCache::query()
            ->where('token', $hash)
            ->first();

        if (!$calculationCache) {
            /** @var City $cityTo */
            $cityTo = City::query()->where('id', $request->cityTo)->first();
            /** @var City $cityFrom */
            $cityFrom = City::query()->where('id', $request->cityFrom)->first();

            if ($request->cargoToStreet) {
                /** @var Street $cargoToStreet */
                $cargoToStreet = Street::query()->where('id', $request->cargoToStreet)->first();
            }
            if ($request->cargoFromStreet) {
                /** @var Street $cargoFromStreet */
                $cargoFromStreet = Street::query()->where('id', $request->cargoFromStreet)->first();
            }
            $data = $request->toArray();

            $data['cityTo'] = $cityTo->name;
            $data['cityFrom'] = $cityFrom->name;
            $data['cargoToStreet'] = isset($cargoToStreet) ? $cargoToStreet->name : '';
            $data['cargoFromStreet'] = isset($cargoFromStreet) ? $cargoFromStreet->name : '';
            $data['cargoFromCountry'] = $cityFrom->country->short_name;
            $data['cargoFromState'] = isset($cargoFromStreet) ? $cargoFromStreet->city->region : '';
            $data['cargoToState'] = isset($cargoToStreet) ? $cargoToStreet->city->region : '';
            $data['cargoToCountry'] = $cityTo->country->short_name;
            $data['cargoComposition'] = 1;
            $data['userDiscount'] = 2;
            $data['options']['tariffType'] = 1;
            foreach ($data['places'] as &$place) {
                $place['cargoWeightTypeID'] = 1;
                $place['cargoVolTypeID'] = 1;
                $place['cargoTemperatureModeId'] = 1;
                $place['cargoDangerClassId'] = 1;
            }

            $response = Http::withHeaders([
                'api-key' => config('app.api_key')
            ])->post(config('app.api_url').'/3/get_delivery_calculation.php', $data);

            if ($response->status() == 200) {
                $calculationCache = new CalculationCache();
                $calculationCache->token = $hash;
                $calculationCache->data = json_encode($response->json());
                $calculationCache->save();
            } else {
                return null;
            }
        }
        return $calculationCache;
    }

    public function getOptions(GetCompanyOrderOptionsRequest $request): ?CompaniesCacheOption
    {
        $body = $request->all();
        $hash = hash('sha256', json_encode($body));

        /** @var CompaniesCacheOption $companyCacheOption */
        $companyCacheOption = CompaniesCacheOption::query()
            ->where('token', $hash)
            ->first();

        if (!$companyCacheOption) {
            $response = $this->getCompanyOrderOptions($request);
            if ($response->status() == 200) {
                $options = $response->json('groups.addOptions');
                $companyCacheOption = new CompaniesCacheOption();
                $companyCacheOption->token = $hash;
                $companyCacheOption->data = json_encode($options);
                $companyCacheOption->save();
            } else {
                return null;
            }
        }
        return $companyCacheOption;
    }

    public function getPayment(GetCompanyOrderOptionsRequest $request): ?CompaniesCachePayment
    {
        $body = $request->all();
        $hash = hash('sha256', json_encode($body));

        /** @var CompaniesCachePayment $companiesCachePayment */
        $companiesCachePayment = CompaniesCachePayment::query()
            ->where('token', $hash)
            ->first();

        if (!$companiesCachePayment) {
            $response = $this->getCompanyOrderOptions($request);

            if ($response->status() == 200) {
                $payment = $response->json('groups.payment');
                $companiesCachePayment = new CompaniesCachePayment();
                $companiesCachePayment->token = $hash;
                $companiesCachePayment->data = json_encode($payment);
                $companiesCachePayment->save();
            } else {
                return null;
            }
        }
        return $companiesCachePayment;
    }

    public function getTerminal(GetCompanyTerminalsRequest $request): ?CompaniesCacheTerminal
    {
        $body = $request->all();
        $hash = hash('sha256', json_encode($body));

        /** @var CompaniesCacheTerminal $companiesCacheTerminal */
        $companiesCacheTerminal = CompaniesCacheTerminal::query()
            ->where('token', $hash)
            ->first();

        if (!$companiesCacheTerminal) {
            $data = [
                'modifies' => $request->modifies
            ];

            $data['data']['companyID'] = $request->data['transportNumber'];
            $data['data']['lang'] = $request->data['lang'];

            /** @var City $city */
            $city = City::query()->where('id', $request->data['cityId'])->first();

            if ($request->data['isArrival'] && !$request->data['isDerival']) {
                $data['data']['cargoTo'] = $city->name;
                $data['data']['cargoToState'] = $city->region ? $city->region : '';
                $data['data']['isArrivalByCourier'] = false;
                $data['data']['isDerivalByCourier'] = true;
            } elseif (!$request->data['isArrival'] && $request->data['isDerival']) {
                $data['data']['cargoFrom'] = $city->name;
                $data['data']['cargoFromState'] = $city->region ? $city->region : '';
                $data['data']['isArrivalByCourier'] = true;
                $data['data']['isDerivalByCourier'] = false;
            } else {
                $data['data']['cargoTo'] = $city->name;
                $data['data']['cargoFrom'] = $city->name;
                $data['data']['cargoFromState'] = $city->region ? $city->region : '';
                $data['data']['cargoToState'] = $city->region ? $city->region : '';
                $data['data']['isArrivalByCourier'] = false;
                $data['data']['isDerivalByCourier'] = false;
            }

            $response = Http::withHeaders([
                'api-key' => config('app.api_key')
            ])->post(config('app.api_url').'/3/list_company_order_options.php', $data);

            if ($response->status() == 200) {
                if ($request->data['isArrival'] && !$request->data['isDerival']) {
                    $arrivalTerminals = $response->json('groups.where.aoptions.arrivalTerminalBlock');
                    $terminals = [
                        'arrivalTerminalBlock' => $arrivalTerminals,
                    ];
                } elseif (!$request->data['isArrival'] && $request->data['isDerival']) {
                    $derivalTerminals = $response->json('groups.from.aoptions.derivalTerminalBlock');
                    $terminals = [
                        'derivalTerminalBlock' => $derivalTerminals
                    ];
                } else {
                    $arrivalTerminals = $response->json('groups.where.aoptions.arrivalTerminalBlock');
                    $derivalTerminals = $response->json('groups.from.aoptions.derivalTerminalBlock');
                    $terminals = [
                        'arrivalTerminalBlock' => $arrivalTerminals,
                        'derivalTerminalBlock' => $derivalTerminals
                    ];
                }

                $companiesCacheTerminal = new CompaniesCacheTerminal();
                $companiesCacheTerminal->token = $hash;
                $companiesCacheTerminal->data = json_encode($terminals);
                $companiesCacheTerminal->save();
            } else {
                return null;
            }
        }
        return $companiesCacheTerminal;
    }

    private function getCompanyOrderOptions(GetCompanyOrderOptionsRequest $request)
    {
        $data = $request->toArray();

        /** @var City $cityTo */
        $cityTo = City::query()->where('id', $request->data['cargoTo'])->first();
        /** @var City $cityFrom */
        $cityFrom = City::query()->where('id', $request->data['cargoFrom'])->first();

        $data['data']['cargoTo'] = $cityTo->name;
        $data['data']['cargoFrom'] = $cityFrom->name;
        $data['data']['cargoFromState'] = $cityFrom->region ? $cityFrom->region : '';
        $data['data']['cargoToState'] = $cityTo->region ? $cityTo->region : '';
        $data['data']['companyID'] = $request->data['transportNumber'];
        $data['data']['isArrivalByCourier'] = isset($request->data['isArrivalByCourier']) ? $request->data['isArrivalByCourier'] : 0;
        $data['data']['isDerivalByCourier'] = isset($request->data['isDerivalByCourier']) ? $request->data['isDerivalByCourier'] : 0;

        return Http::withHeaders([
            'api-key' => config('app.api_key')
        ])->post(config('app.api_url').'/3/list_company_order_options.php', $data);
    }
}
