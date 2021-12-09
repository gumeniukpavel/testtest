<?php

namespace Tests\Feature\Api;

use App\Constant\OrderType;
use App\Constant\SortUserProfile;
use App\Db\Entity\Role;
use App\Db\Entity\User;
use App\Db\Entity\UserProfile;
use App\Db\Entity\UserRequestHistory;
use Tests\TestCase;

class UserControllerRequestHistoryListTest extends TestCase
{
    const URL = 'api/user/';

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testShouldReceiveRequestHistoryList()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
        ]);

        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Test',
        ]);

        UserRequestHistory::factory()->count(10)->create([
            'user_id' => $user1->id,
        ]);

        UserRequestHistory::factory()->count(5)->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'requestHistory',
            $admin->getJWTToken(),
            [
                'userId' => $user1->id
            ]
        );

        $response->assertStatus(200);
        $response->assertPaginationResponse(1, 10);
    }

    public function testShouldReceiveRequestHistoryListUserNotFound()
    {
        /** @var User $admin */
        $admin = $this->createAdmin();

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Test',
        ]);

        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Test',
        ]);

        UserRequestHistory::factory()->count(10)->create([
            'user_id' => $user1->id,
        ]);

        UserRequestHistory::factory()->count(5)->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->postJsonAuthWithToken(
            self::URL.'requestHistory',
            $admin->getJWTToken(),
            [
                'userId' => 99999
            ]
        );

        $response->assertStatus(400);
    }
}
