<?php

namespace Tests\Feature\Api;

use App\Db\Entity\Role;
use App\Db\Entity\User;
use App\Db\Entity\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    const URL = 'api/user/';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldCreateNewUser()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        $response = $this->postJsonAuthWithToken(
            self::URL.'create',
            $admin->getJWTToken(),
            [
                'name' => 'Test',
                'email' => 'test@test.test',
                'password' => 'password',
                'repeatPassword' => 'password',
                'uniqueIdentityNumber' => '1231231231'
            ]
        );

        $response->assertStatus(200);
        $endedAt = Carbon::now()->addMonths(6)->toDateString();
        $this->assertEquals($endedAt, $response->json('endAccessToApiAt'));
    }

    public function testShouldCreateNewUserWithEndAccessToApiAt()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        $response = $this->postJsonAuthWithToken(
            self::URL.'create',
            $admin->getJWTToken(),
            [
                'name' => 'Test',
                'email' => 'test@test.test',
                'password' => 'password',
                'repeatPassword' => 'password',
                'uniqueIdentityNumber' => '1231231231',
                'endAccessToApiAt' => Carbon::now()->addMonth()->timestamp
            ]
        );

        $response->assertStatus(200);
        $endedAt = Carbon::now()->addMonth()->toDateString();
        $this->assertEquals($endedAt, $response->json('endAccessToApiAt'));
    }

    public function testShouldNotCreateNewUserPasswordConfirmError()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        $response = $this->postJsonAuthWithToken(
            self::URL.'create',
            $admin->getJWTToken(),
            [
                'name' => 'Test',
                'email' => 'test@test.test',
                'password' => 'password',
                'repeatPassword' => 'password123',
                'uniqueIdentityNumber' => '1231231231',
                'notes' => 'Notes',
            ]
        );

        $response->assertStatus(500);
    }

    public function testShouldNotCreateNewUserValidationError()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        $response = $this->postJsonAuthWithToken(
            self::URL.'create',
            $admin->getJWTToken(),
            [
                'name' => 123,
                'email' => 'test',
                'password' => 'password',
                'repeatPassword' => 'password123',
                'uniqueIdentityNumber' => '1231231231',
                'notes' => 'Notes',
            ]
        );

        $response->assertStatus(400);
    }

    public function testShouldNotCreateNewUserRoleError()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->postJsonAuthWithToken(
            self::URL.'create',
            $user->getJWTToken(),
            [
                'name' => 'Test',
                'email' => 'test@test.test',
                'password' => 'password',
                'repeatPassword' => 'password',
                'uniqueIdentityNumber' => '1231231231',
                'notes' => 'Notes',
            ]
        );

        $response->assertStatus(403);
    }

    public function testShouldUpdateUser()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'update',
            $admin->getJWTToken(),
            [
                'id' => $user->id,
                'name' => 'Test',
                'email' => 'test@test.test',
                'uniqueIdentityNumber' => '1231231231',
                'notes' => 'Notes'
            ]
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals($user->id, $response->json('id'));
        $this->assertEquals($user->name, $response->json('name'));
        $this->assertEquals($user->email, $response->json('email'));
    }

    public function testShouldNotUpdateUserRoleError()
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();

        /** @var User $user2 */
        $user2 = User::factory()->create([
            'password' => 'test123'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'update',
            $user1->getJWTToken(),
            [
                'id' => $user2->id,
                'name' => 'Test',
                'email' => 'test@test.test',
                'uniqueIdentityNumber' => '1231231231',
                'notes' => 'Notes',
            ]
        );

        $response->assertStatus(403);
    }

    public function testShouldNotUpdateUserValidationError()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'update',
            $admin->getJWTToken(),
            [
                'id' => 99999,
                'name' => 123,
                'email' => 'Test',
                'uniqueIdentityNumber' => '1231231231',
                'notes' => 'Notes',
            ]
        );

        $response->assertStatus(400);
    }

    public function testShouldDeleteUser()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJsonAuthWithToken(
            self::URL.'delete/'.$user->id,
            $admin->getJWTToken()
        );

        $response->assertStatus(204);
    }

    public function testShouldNotDeleteUserNotFound()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJsonAuthWithToken(
            self::URL.'delete/'. 99999,
            $admin->getJWTToken()
        );

        $response->assertStatus(404);
    }

    public function testShouldNotDeleteUserRoleError()
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'password' => 'test123'
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJsonAuthWithToken(
            self::URL.'delete/'.$user->id,
            $admin->getJWTToken()
        );

        $response->assertStatus(403);
    }

    public function testShouldReceiveUser()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJsonAuthWithToken(
            self::URL.$user->id,
            $admin->getJWTToken()
        );

        $response->assertStatus(200);
    }

    public function testShouldNotReceiveUserNotFound()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJsonAuthWithToken(
            self::URL. 999999,
            $admin->getJWTToken()
        );

        $response->assertStatus(404);
    }

    public function testShouldReceiveNewToken()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJsonAuthWithToken(
            self::URL.'generateToken/'.$user->id,
            $admin->getJWTToken()
        );


        $response->assertStatus(200);
    }

    public function testShouldNotReceiveNewTokenUserNotFound()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test123'
        ]);

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->getJsonAuthWithToken(
            self::URL.'generateToken/'. 99999,
            $admin->getJWTToken()
        );

        $response->assertStatus(404);
    }

    public function testShouldUpdateUserPassword()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'updatePassword',
            $admin->getJWTToken(),
            [
                'id' => $user->id,
                'newPassword' => 'test123',
                'repeatPassword' => 'test123',
            ]
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertTrue(Hash::check('test123', $user->password));
    }

    public function testShouldNotUpdateUserPasswordValidationError()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'updatePassword',
            $admin->getJWTToken(),
            [
                'id' => 99999,
                'newPassword' => 'test123',
                'repeatPassword' => 'test123',
            ]
        );

        $response->assertStatus(400);
    }

    public function testShouldNotUpdateUserPasswordPermissionDenied()
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();

        /** @var User $user2 */
        $user2 = User::factory()->create([
            'password' => 'test111',
            'is_has_access_to_api' => false
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'updatePassword',
            $user2->getJWTToken(),
            [
                'id' => 99999,
                'newPassword' => 'test123',
                'repeatPassword' => 'test123',
            ]
        );

        $response->assertStatus(403);
    }

    public function testShouldSetAccessToApiTrue()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111',
            'is_has_access_to_api' => false
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'setAccess',
            $admin->getJWTToken(),
            [
                'id' => $user->id,
                'isHasAccessToApi' => true,
            ]
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(1, $user->is_has_access_to_api);
    }

    public function testShouldSetAccessToApiFalse()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111',
            'is_has_access_to_api' => true
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'setAccess',
            $admin->getJWTToken(),
            [
                'id' => $user->id,
                'isHasAccessToApi' => false,
            ]
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(0, $user->is_has_access_to_api);
    }

    public function testShouldNotSetAccessToApiValidationError()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111',
            'is_has_access_to_api' => true
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'setAccess',
            $admin->getJWTToken(),
            [
                'id' => 99999,
                'isHasAccessToApi' => 999999,
            ]
        );

        $response->assertStatus(400);
    }

    public function testShouldNotSetAccessToApiPermissionDenied()
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();

        /** @var User $user2 */
        $user2 = User::factory()->create([
            'password' => 'test111',
            'is_has_access_to_api' => true
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'setAccess',
            $user1->getJWTToken(),
            [
                'id' => $user2->id,
                'isHasAccessToApi' => true,
            ]
        );

        $response->assertStatus(403);
    }

    public function testShouldUpdatePasswordByUser()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'update/password',
            $user->getJWTToken(),
            [
                'oldPassword' => 'test111',
                'newPassword' => 'test123',
                'repeatPassword' => 'test123',
            ]
        );

        $response->assertStatus(200);
        $user->refresh();
        $this->assertTrue(Hash::check('test123', $user->password));
    }

    public function testShouldNotUpdatePasswordByUserOldPasswordError()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'update/password',
            $user->getJWTToken(),
            [
                'oldPassword' => 'test222',
                'newPassword' => 'test123',
                'repeatPassword' => 'test123',
            ]
        );

        $response->assertStatus(500);
    }

    public function testShouldNotUpdatePasswordByUserRepeatPasswordError()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => 'test111'
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'update/password',
            $user->getJWTToken(),
            [
                'oldPassword' => 'test111',
                'newPassword' => 'test123',
                'repeatPassword' => 'test321',
            ]
        );

        $response->assertStatus(500);
    }
}
