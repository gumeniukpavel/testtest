<?php

namespace App\Console\Commands;

use App\Db\Entity\Role;
use App\Db\Entity\User;
use App\Db\Service\UserDao;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:register {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register User';

    /** @var UserDao $userDao */
    private $userDao;

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
        $validator = Validator::make([
            'email' => $this->argument('email')
        ], [
            'email' => [
                'required',
                Rule::unique(User::class, 'email')
            ],
        ]);

        if ($validator->fails()) {
            $this->info('User dont register. See error messages below:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        $name = $this->ask('Enter a name for the User', false);
        if (!$name) {
            $this->info("Name if required");
            return 1;
        }
        $password = Str::random(8);

        $data = [
            'name' => $name,
            'email' => $this->argument('email'),
            'password' => $password,
        ];

        try {
            /** @var User $user */
            $user = $this->create($data);

            $this->info("User registered");
            $this->info("JWT Token: ".$user->getJWTToken());
        } catch (\Exception $exception) {
            Log::error($exception);
            $this->info("Sending error: {$exception->getMessage()}");
            return 1;
        }
        return 0;
    }

    protected function create(array $data): Model
    {
        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_has_access_to_api' => true,
            'role_id' => Role::ROLE_ADMIN
        ]);
    }
}
