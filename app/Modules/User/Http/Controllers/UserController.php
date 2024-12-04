<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Http\Requests\CreateUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    protected $userService;
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function listUsers(Request $request): JsonResponse
    {
        $data['per_page']=$request->input('per_page',10);
        $data['page']=$request->input('page',1);
        $users = $this->userService->getAllUsers($data);

        return $this->responseJsonSuccess($users['data'],'Fetched users successfully',$users['meta']);

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

    public function createUser(CreateUserRequest $request): JsonResponse
    {

        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);

            $user = $this->userService->createUser($data);
            return $this->responseJsonSuccess($user, 'User created successfully',null ,201);

        } catch (\Illuminate\Database\QueryException $e) {
            // Bắt lỗi liên quan đến query SQL, ví dụ: trùng email
            return $this->responseJsonError('Database error: ' . $e->getMessage(), 500);

        } catch (\Exception $e) {
            // Bắt lỗi khác
            return $this->responseJsonError('Failed to create user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update user information.
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function updateUser(UpdateUserRequest $request): JsonResponse
    {

        try {
            // Lấy tất cả dữ liệu đã validated
            $data = $request->validated();

            // Gọi service để xử lý cập nhật
            $user = $this->userService->updateUser($data['id'],$data);

            // Trả về phản hồi thành công
            return $this->responseJsonSuccess($user, 'User updated successfully', null, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Xử lý lỗi truy vấn (ví dụ: trùng email)
            return $this->responseJsonError('Database error: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            // Xử lý các lỗi khác
            return $this->responseJsonError('Failed to update user: ' . $e->getMessage(), 500);
        }
    }
    public function verifyUser(UpdateUserRequest $request): JsonResponse
    {
            $data = $request->all();
            $user = $this->userService->verifyUser($data);
            if ($user['success']) {
                return $this->responseJsonSuccess($user, 'User verified successfully', null, 200);
            }
        return $this->responseJsonSuccess($user, 'User verified failed', null, 200);

    }

    public function login(Request $request): JsonResponse
    {
        // Xác thực thông tin đăng nhập
        $credentials = $request->only('email', 'password');

        // Lấy thông tin người dùng đã xác thực
        $result= $this->userService->login($credentials);
        if (!empty($result['success']) && $result['success'] === true) {
            $response = array_merge(
                [
                    'token' => $result['token'] ?? [],
                    'token_type' => 'bearer',
                ],
                $result['data'] ?? []
            );
            return $this->responseJsonSuccess($response,'Login successful');
        }
        return $this->responseJsonError($result['message']??'Login failed', 404,$result['message']??'Login failed');
    }
    public function getUserByToken(): JsonResponse
    {
        // Kiểm tra xem header Authorization có được gửi qua không
        $authHeader = request()->header('Authorization');

        if (!$authHeader) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization header missing',
            ], 401);
        }
        $result= $this->userService->getUserByToken();

        if (!empty($result['success']) && $result['success'] === true) {
            $response= $result['data']??[];
            return $this->responseJsonSuccess($response,'Login successful');
        }
        return $this->responseJsonError($result['message']??'Login failed',  401,$result['message']??'Login failed');
    }
}
