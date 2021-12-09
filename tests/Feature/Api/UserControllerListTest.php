<?php

namespace Tests\Feature\Api;

use App\Constant\OrderType;
use App\Constant\SortUserProfile;
use App\Db\Entity\Role;
use App\Db\Entity\User;
use App\Db\Entity\UserProfile;
use Tests\TestCase;

class UserControllerListTest extends TestCase
{
    const URL = 'api/user/';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldReceiveUsersList()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Qwerty',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'name' => 'Zxcvbn',
        ]);

        UserProfile::factory()->create([
            'client_name' => $user1->name,
            'client_email' => $user1->email,
            'user_id' => $user1->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user2->name,
            'client_email' => $user2->email,
            'user_id' => $user2->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user3->name,
            'client_email' => $user3->email,
            'user_id' => $user3->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $admin->getJWTToken(),
            [
                'orderBy' => OrderType::$Asc->getValue(),
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 3);
    }

    public function testShouldReceiveUsersListFilterBySearchStringName()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Qwerty',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'name' => 'Zxcvbn',
        ]);

        UserProfile::factory()->create([
            'client_name' => $user1->name,
            'client_email' => $user1->email,
            'unique_identity_number' => 1111111111,
            'notes' => 'Description',
            'user_id' => $user1->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user2->name,
            'client_email' => $user2->email,
            'unique_identity_number' => 2222222222,
            'notes' => 'Description',
            'user_id' => $user2->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user3->name,
            'client_email' => $user3->email,
            'unique_identity_number' => 3333333333,
            'notes' => 'Description',
            'user_id' => $user3->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $admin->getJWTToken(),
            [
                'orderBy' => OrderType::$Asc->getValue(),
                'searchString' => 'qwerty'
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 1);
        $this->assertEquals($user2->id, $response->json('items')[0]['user']['id']);
    }

    public function testShouldReceiveUsersListFilterBySearchStringEmail()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.test',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Qwerty',
            'email' => 'qwerty@qwety.qwerty',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'name' => 'Zxcvbn',
            'email' => 'zxcvbn@zxcvbn.zxcvbn',
        ]);

        UserProfile::factory()->create([
            'client_name' => $user1->name,
            'client_email' => $user1->email,
            'unique_identity_number' => 111111111,
            'notes' => 'Description',
            'user_id' => $user1->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user2->name,
            'client_email' => $user2->email,
            'unique_identity_number' => 222222222,
            'notes' => 'Description',
            'user_id' => $user2->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user3->name,
            'client_email' => $user3->email,
            'unique_identity_number' => 333333333,
            'notes' => 'Description',
            'user_id' => $user3->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $admin->getJWTToken(),
            [
                'orderBy' => OrderType::$Asc->getValue(),
                'searchString' => 'test@'
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 1);
        $this->assertEquals($user1->id, $response->json('items')[0]['user']['id']);
    }

    public function testShouldReceiveUsersListFilterBySearchStringIdentityNumber()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.test',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Qwerty',
            'email' => 'qwerty@qwety.qwerty',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'name' => 'Zxcvbn',
            'email' => 'zxcvbn@zxcvbn.zxcvbn',
        ]);

        UserProfile::factory()->create([
            'client_name' => $user1->name,
            'client_email' => $user1->email,
            'unique_identity_number' => 111111111,
            'notes' => 'Description',
            'user_id' => $user1->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user2->name,
            'client_email' => $user2->email,
            'unique_identity_number' => 222222222,
            'notes' => 'Description',
            'user_id' => $user2->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user3->name,
            'client_email' => $user3->email,
            'unique_identity_number' => 333333333,
            'notes' => 'Description',
            'user_id' => $user3->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $admin->getJWTToken(),
            [
                'orderBy' => OrderType::$Asc->getValue(),
                'searchString' => '33333'
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 1);
        $this->assertEquals($user3->id, $response->json('items')[0]['user']['id']);
    }

    public function testShouldReceiveUsersListSortByName()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.test',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Qwerty',
            'email' => 'qwerty@qwety.qwerty',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'name' => 'Zxcvbn',
            'email' => 'zxcvbn@zxcvbn.zxcvbn',
        ]);

        UserProfile::factory()->create([
            'client_name' => $user1->name,
            'client_email' => $user1->email,
            'unique_identity_number' => 111111111,
            'notes' => 'Description',
            'user_id' => $user1->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user2->name,
            'client_email' => $user2->email,
            'unique_identity_number' => 222222222,
            'notes' => 'Description',
            'user_id' => $user2->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user3->name,
            'client_email' => $user3->email,
            'unique_identity_number' => 333333333,
            'notes' => 'Description',
            'user_id' => $user3->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $admin->getJWTToken(),
            [
                'orderBy' => OrderType::$Asc->getValue(),
                'sortColumn' => SortUserProfile::$ClientName->getValue()
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 3);
        $this->assertEquals($user2->id, $response->json('items')[0]['user']['id']);
    }

    public function testShouldReceiveUsersListSortByEmail()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.test',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Qwerty',
            'email' => 'qwerty@qwety.qwerty',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'name' => 'Zxcvbn',
            'email' => 'zxcvbn@zxcvbn.zxcvbn',
        ]);

        UserProfile::factory()->create([
            'client_name' => $user1->name,
            'client_email' => $user1->email,
            'unique_identity_number' => 111111111,
            'notes' => 'Description',
            'user_id' => $user1->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user2->name,
            'client_email' => $user2->email,
            'unique_identity_number' => 222222222,
            'notes' => 'Description',
            'user_id' => $user2->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user3->name,
            'client_email' => $user3->email,
            'unique_identity_number' => 333333333,
            'notes' => 'Description',
            'user_id' => $user3->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $admin->getJWTToken(),
            [
                'orderBy' => OrderType::$Asc->getValue(),
                'sortColumn' => SortUserProfile::$ClientEmail->getValue()
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 3);
        $this->assertEquals($user2->id, $response->json('items')[0]['user']['id']);
    }

    public function testShouldReceiveUsersListSortByIdentityNumberDesc()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@test.test',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Qwerty',
            'email' => 'qwerty@qwety.qwerty',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'name' => 'Zxcvbn',
            'email' => 'zxcvbn@zxcvbn.zxcvbn',
        ]);

        UserProfile::factory()->create([
            'client_name' => $user1->name,
            'client_email' => $user1->email,
            'unique_identity_number' => 111111111,
            'notes' => 'Description',
            'user_id' => $user1->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user2->name,
            'client_email' => $user2->email,
            'unique_identity_number' => 222222222,
            'notes' => 'Description',
            'user_id' => $user2->id
        ]);
        UserProfile::factory()->create([
            'client_name' => $user3->name,
            'client_email' => $user3->email,
            'unique_identity_number' => 333333333,
            'notes' => 'Description',
            'user_id' => $user3->id
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'list',
            $admin->getJWTToken(),
            [
                'orderBy' => OrderType::$Desc->getValue(),
                'sortColumn' => SortUserProfile::$ClientEmail->getValue()
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 3);
        $this->assertEquals($user3->id, $response->json('items')[0]['user']['id']);
    }
}
