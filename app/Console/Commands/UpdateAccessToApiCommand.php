<?php

namespace App\Console\Commands;

use App\Db\Entity\User;
use App\Db\Service\UserDao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateAccessToApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:access-to-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update access to api';

    private UserDao $userDao;

    public function __construct(
        UserDao $userDao
    ) {
        parent::__construct();
        $this->userDao = $userDao;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var User[] $users */
        $users = $this->userDao->getUserEndAccessToApi();
        foreach ($users as $user) {
            $user->is_has_access_to_api = false;
            $user->save();
        }

        return 0;
    }
}
