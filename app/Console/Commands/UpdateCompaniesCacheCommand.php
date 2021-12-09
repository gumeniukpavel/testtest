<?php

namespace App\Console\Commands;

use App\Db\Entity\User;
use App\Db\Service\CompaniesCacheDao;
use App\Db\Service\UserDao;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCompaniesCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:companies-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update companies cache';

    private string $url = 'https://api2.cargo.guru/3/get_complist.php';
    private CompaniesCacheDao $companiesCacheDao;

    public function __construct(
        CompaniesCacheDao $companiesCacheDao
    ) {
        parent::__construct();
        $this->companiesCacheDao = $companiesCacheDao;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get($this->url, [
            'countries' => [
                'RU'
            ],
            'inverse' => 0
        ]);

        $ruCompanies = $response->json('companies');
        foreach ($ruCompanies as $ruCompany) {
            $this->companiesCacheDao->createCompaniesCache($ruCompany);
        }

        $response = Http::get($this->url, [
            'countries' => [
                'RU'
            ],
            'inverse' => 1
        ]);

        $companies = $response->json('companies');
        foreach ($companies as $company) {
            $this->companiesCacheDao->createCompaniesCache($company);
        }
        return 0;
    }
}
