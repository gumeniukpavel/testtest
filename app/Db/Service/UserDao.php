<?php

namespace App\Db\Service;

use App\Constant\OrderType;
use App\Constant\SortUserProfile;
use App\Db\Entity\Role;
use App\Db\Entity\User;
use App\Db\Entity\UserProfile;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\GetListRequest;
use App\Http\Requests\User\SetAccessToApiRequest;
use App\Http\Requests\User\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserDao
{
    public function firstWithData(int $id): ?User
    {
        /** @var User $user */
        $user = User::query()
            ->with('userProfile')
            ->where('id', $id)
            ->first();
        return $user;
    }

    public function getUserByEmail(string $email): ?User
    {
        /** @var User $user */
        $user = User::query()
            ->where('email', $email)
            ->first();

        return $user;
    }

    public function createNew(CreateRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->is_has_access_to_api = true;
        if ($request->endAccessToApiAt) {
            $user->end_access_to_api_at = Carbon::createFromTimestamp($request->endAccessToApiAt)->toDateString();
        } else {
            $user->end_access_to_api_at = Carbon::now()->addMonths(6)->toDateString();
        }
        $user->role_id = Role::ROLE_USER;
        $user->save();

        $userProfile = new UserProfile();
        $userProfile->user_id = $user->id;
        $userProfile->client_name = $request->name;
        $userProfile->client_email = $request->email;
        $userProfile->unique_identity_number = $request->uniqueIdentityNumber;
        $userProfile->notes = $request->notes;
        $userProfile->save();

        return $user;
    }

    public function update(UpdateRequest $request, User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->endAccessToApiAt) {
            $user->end_access_to_api_at = Carbon::createFromTimestamp($request->endAccessToApiAt)->toDateString();
        } else {
            $user->end_access_to_api_at = Carbon::now()->addMonths(6)->toDateString();
        }
        $user->save();

        $userProfile = $user->userProfile;
        if (!$userProfile) {
            $userProfile = new UserProfile();
            $userProfile->user_id = $user->id;
        }
        $userProfile->client_name = $request->name;
        $userProfile->client_email = $request->email;
        $userProfile->unique_identity_number = $request->uniqueIdentityNumber;
        $userProfile->notes = $request->notes;
        $userProfile->save();

        return $user;
    }

    public function setAccessToApi(SetAccessToApiRequest $request, User $user)
    {
        $user->is_has_access_to_api = $request->isHasAccessToApi;

        $user->save();
        return $user;
    }

    public function getListUserProfiles(GetListRequest $request)
    {
        $searchString = $request->searchString;
        $builder = UserProfile::query()
            ->with('user')
            ->when(!empty($searchString), function (Builder $builder) use ($searchString)
            {
                $builder->whereIn('user_profile.id', function ($query) use ($searchString)
                {
                    $query->select('user_profile.id')
                        ->from('user_profile')
                        ->whereRaw('UPPER(`client_name`) LIKE ?', [mb_strtoupper('%'.$searchString.'%', 'UTF-8')])
                        ->orWhereRaw('UPPER(`client_email`) LIKE ?', [mb_strtoupper('%'.$searchString.'%', 'UTF-8')])
                        ->orWhereRaw('UPPER(`notes`) LIKE ?', [mb_strtoupper('%'.$searchString.'%', 'UTF-8')])
                        ->orWhereRaw('UPPER(`unique_identity_number`) LIKE ?',
                            [mb_strtoupper('%'.$searchString.'%', 'UTF-8')]);
                });
            });

        $orderBy = strtolower($request->orderBy);
        switch ($request->sortColumn) {
            case SortUserProfile::$ClientEmail->getValue():
                $builder->orderBy('client_email', $orderBy);
                break;

            case SortUserProfile::$ClientName->getValue():
                $builder->orderBy('client_name', $orderBy);
                break;

            case SortUserProfile::$Notes->getValue():
                $builder->orderBy('notes', $orderBy);
                break;

            case SortUserProfile::$UniqueIdentityNumber->getValue():
                $builder->orderBy('unique_identity_number', $orderBy);
                break;

            default:
                $builder->orderBy('id', $orderBy);
                break;
        }

        return $builder;
    }

    public function getUserEndAccessToApi()
    {
        return User::query()
            ->whereNotNull('end_access_to_api_at')
            ->where('end_access_to_api_at', '<=', Carbon::today()->toDateString())
            ->get();
    }
}
