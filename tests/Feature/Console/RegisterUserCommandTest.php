<?php

namespace Tests\Feature\Console;

use App\Console\Commands\RegisterUserCommand;
use App\Db\Entity\Role;
use App\Db\Entity\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldRegisterUser()
    {
        $email = 'test@test.test';
        $name = 'Name';

        $response = $this->artisan(RegisterUserCommand::class, [
            'email' => $email,
        ])->expectsQuestion('Enter a name for the User', $name)
            ->assertExitCode(0);

        $response->run();

        /** @var User $user */
        $user = User::query()->where('email', $email)->first();
        $this->assertEquals($name, $user->name);
        $this->assertEquals(Role::ROLE_NAME_ADMIN, $user->role->name);
    }

    public function testShouldNotRegisterUserAlreadyExists()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'password'
        ]);

        $this->artisan(RegisterUserCommand::class, [
            'email' => $user->email
        ])->assertExitCode(1);
    }

    public function testShouldNotRegisterUserWithEmptyName()
    {
        $email = 'test@test.test';

        $this->artisan(RegisterUserCommand::class, [
            'email' => $email,
        ])->expectsQuestion('Enter a name for the User', '')
            ->assertExitCode(1);
    }
}
