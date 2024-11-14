<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class UserController extends Controller
{
    protected $userService;
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function listUsers(Request $request): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        return $this->responseJsonSuccess($users,'Fetched users successfully');

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getUserByID($id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            return $this->responseJsonSuccess($user,'Fetched users successfully');
        } catch (\Exception $e) {
            return $this->responseJsonError('User not found', 404);
        }
    }

    public function createUser(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $user = $this->userService->createUser($data);
            return $this->responseJsonSuccess($user,'User created successfully', 201);

        } catch (\Exception $e) {
            return $this->responseJsonError('Failed to create user', 500);

        }
    }
}
