<?php

namespace Tests\Feature\Console;

use App\Console\Commands\RegisterUserCommand;
use App\Console\Commands\UpdateAccessToApiCommand;
use App\Db\Entity\Role;
use App\Db\Entity\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateAccessToApiCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldUpdateAccessToApi()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'is_has_access_to_api' => true,
            'end_access_to_api_at' => Carbon::now()->subDays(15)->toDateString()
        ]);

        $response = $this->artisan(UpdateAccessToApiCommand::class)->assertExitCode(0);

        $response->run();

        $user->refresh();
        $this->assertEquals(0, $user->is_has_access_to_api);
    }
}
