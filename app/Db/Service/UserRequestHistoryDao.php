<?php

namespace App\Db\Service;

use App\Db\Entity\User;
use App\Db\Entity\UserRequestHistory;

class UserRequestHistoryDao
{
    public function createUserRequest(array $data, string $url, User $user)
    {
        $userRequestHistory = new UserRequestHistory();
        $userRequestHistory->user_id = $user->id;
        $userRequestHistory->url = $url;
        $userRequestHistory->data = json_encode($data);
        $userRequestHistory->save();
    }

    public function getUserRequestHistoryQuery(User $user)
    {
        return UserRequestHistory::query()
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc');
    }
}
