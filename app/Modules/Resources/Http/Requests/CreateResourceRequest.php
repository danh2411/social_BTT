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
            'type' => 'required|string|max:255', // Loại tài nguyên
            'resourceable_id' => 'required|integer', // ID của bản ghi liên quan
            'resourceable_type' => 'required|string|max:255', // Loại đối tượng liên kết
            'path' => 'required|string|max:255', // Đường dẫn tài nguyên
            'description' => 'sometime|string', // Mô tả tài nguyên
            'meta_data' => 'sometime|array', // Dữ liệu meta
            'option' => 'sometime|string|max:255', // Dữ liệu meta khác
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
            'resourceable_id.required' => 'ID của bản ghi liên quan là bắt buộc.',
            'resourceable_type.required' => 'Loại đối tượng liên kết là bắt buộc(brands, switches, keycaps, etc.).',
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
