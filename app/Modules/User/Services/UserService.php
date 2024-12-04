<?php

namespace App\Modules\User\Services;

use App\Mail\UserVerificationMail;
use App\Modules\User\Models\User;
use App\Modules\User\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        try {
            // Bắt đầu transaction
            DB::beginTransaction();

            // Tạo user
            $user = User::create($data);

            // Commit transaction
            DB::commit();

            // Gửi email xác thực sau khi commit thành công
            Mail::to($user->email)->send(new UserVerificationMail($user));

            return $user;
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            // Ghi log hoặc xử lý lỗi nếu cần
            throw new \Exception('Failed to create user: ' . $e->getMessage());
        }
    }
    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }
    public function verifyUser( array $data)
    {
        // Tìm User với ID đã cho
        $user = User::find($data['id']);

        // Nếu không tìm thấy user, trả về thông báo lỗi hoặc null
        if (!$user) {
            return[
                'success' => false,
                'message' => 'User not found'
            ];
        }
        if (empty( $user->status)&& $user->status==1){
            return [
                'success' => false,
                'message' => 'User Activated',
            ];
        }
        // Cập nhật trạng thái nếu tìm thấy user
        $data['status'] = 1; // Cập nhật status thành 1
        $user->update($data);

        return [
            'success' => true,
            'message' => 'User verified successfully',
            'data' => $user
        ];
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
        $user = Auth::guard('api')->user();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Info User Not Found',
            ];
        }
        if (empty( $user->status)|| $user->status!==1){
            return [
                'success' => false,
                'message' => 'User Not Activated',
            ];
        }
        $response=[
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
            'address' => $user->address,
            'date_of_birth' => $user->date_of_birth,
            'role' => $user->role,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
        // Trả về thông tin người dùng và token
        return[
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $response,
            'token_type' => 'bearer',
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
        $response=[
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
            'address' => $user->address,
            'date_of_birth' => $user->date_of_birth,
            'role' => $user->role,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
        return [
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $response,
        ];
    }
}
