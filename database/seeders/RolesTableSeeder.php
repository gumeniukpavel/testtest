<?php

namespace Database\Seeders;

use App\Db\Entity\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        Role::query()->updateOrInsert(
            [
                'id' => Role::ROLE_ADMIN
            ],
            [
                'id' => Role::ROLE_ADMIN,
                'name' => Role::ROLE_NAME_ADMIN,
                'display_name' => 'Administrator',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
        Role::query()->updateOrInsert(
            [
                'id' => Role::ROLE_USER
            ],
            [
                'id' => Role::ROLE_USER,
                'name' => Role::ROLE_NAME_USER,
                'display_name' => 'User',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
