<?php

namespace App\Http\Controllers;

use App\Db\Entity\User;
use App\Service\AuthService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var AuthService $authService */
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /** @return User|null */
    protected function user(): ?User
    {
        return $this->authService->getUser();
    }

    /**
     * Is logged in?
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return boolval($this->user());
    }

    protected function responseErrorRecordIsExists()
    {
        return $this->json(['message' => "Запись такого типа уже добавлена"], 403);
    }

    protected function responsePermissionsDenied($message = 'В доступе отказано')
    {
        return $this->json(['message' => $message], 403);
    }

    protected function jsonError(string $message = 'Системная ошибка', int $status = 500)
    {
        return $this->json(['message' => $message], $status);
    }

    protected function responseAccessDenied(string $message = 'В доступе отказано', int $status = 403)
    {
        return $this->json(['message' => $message], $status);
    }

    protected function responseNotFound(string $message = 'Запись не найдена', int $status = 404)
    {
        return $this->json(['message' => $message], $status);
    }

    protected function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return response()->json($data, $status, $headers, $options);
    }
}
