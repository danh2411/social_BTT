<?php

namespace App\Modules\Resources\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'requiredrequired|string|max:255', // Loại tài nguyên
            'resourceable_id' => 'nullable|integer', // ID của bản ghi liên quan
            'resourceable_type' => 'nullable|string|max:255', // Loại đối tượng liên kết
            'name' => 'nullable|string|max:255',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048', // Đường dẫn tài nguyên
            'description' => 'nullable|string', // Mô tả tài nguyên
            'meta_data' => 'nullable|array', // Dữ liệu meta
            'option' => 'nullable|string|max:255', // Dữ liệu meta khác
        ];
    }


    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type.required' => 'Loại tài nguyên là bắt buộc (thumbnail, avatar, gallery, video, etc.).',
            'path.required' => 'Đường dẫn tài nguyên là bắt buộc.',
        ];
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
