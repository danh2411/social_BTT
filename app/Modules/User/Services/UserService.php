<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Auth;

class UserService implements UserServiceInterface
{
    public function getAllUsers(array $data)
    {
        // Phân trang người dùng
        $users = User::paginate($data['per_page'], ['*'], 'page', $data['page']);

        //cấu trúc dữ liệu khi trả ra
         $response['meta']=[
            'total' => $users->total()??0,
            'per_page' => $users->perPage()??0,
            'current_page' => $users->currentPage()??0,
            'last_page' => $users->lastPage()??0,
            'from' => $users->firstItem()??0,
            'to' => $users->lastItem(),
        ];
        $response['data']=$users->items()??[];
       return $response;
    }

    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function deleteUser($id): true
    {
        $user = User::findOrFail($id);
        $user->delete();
        return true;
    }
    public function login(array $credentials): array
    {
        // Kiểm tra thông tin đăng nhập
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }



        // Lấy thông tin người dùng đã xác thực
        $user = Auth::user();

        // Trả về thông tin người dùng và token
        return[
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'bearer',
            'user' => $user??[],
        ];
    }
    public function getUserByToken()
    {
        // Lấy thông tin người dùng từ token
        $user = Auth::guard('api')->user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        return [
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $user,
        ];
    }
}
