<?php
namespace App\Modules\User\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Hoặc logic xác thực tùy ý
    }

    public function rules()
    {

        $rules = [
            'id' => 'required|exists:users,id', // Kiểm tra user tồn tại
            'name' => 'sometimes|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'avatar_url' => 'nullable|url',
            'address' => 'nullable|string',
        ];

        if (isset($this->user()->role)&&$this->user()->role !== 1) {
            $rules['email'] = 'sometimes|email|unique:users,email,' . $this->id;
        }

        return $rules;
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $errors
        ], 422, ['Content-Type' => 'application/json; charset=UTF-8'], JSON_UNESCAPED_UNICODE));
    }
}
