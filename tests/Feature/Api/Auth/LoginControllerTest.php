<?php

namespace Tests\Feature\Api\Auth;

use App\Db\Entity\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    const URL_LOGIN = '/api/login';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldLogin()
    {
        $password = 'BthKAas9a0';

        /** @var User $user */
        $user = User::factory()->create([
            'password' => $password
        ]);

        $response = $this->postJson(self::URL_LOGIN, [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function testShouldNotLoginWithoutPassword()
    {
        $response = $this->postJson(self::URL_LOGIN, [
            'email' => 'test@test.test',
            'password' => ''
        ]);

        $response->assertStatus(400);
    }

    public function testShouldNotLoginWithIncorrectPassword()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'blabla'
        ]);

        $response = $this->postJson(self::URL_LOGIN, [
            'email' => $user->email,
            'password' => 'pukpuk'
        ]);

        $response->assertStatus(400);
    }
}
