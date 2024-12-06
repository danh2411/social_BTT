<?php

namespace App\Modules\Brand\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
class CreateBrandRequest extends FormRequest
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
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',
            'name' => 'required|string|max:255',
            'description' => 'sometime|string',
            'date_released' => 'nullable|date_format:d-m-Y',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Nếu tải lên ảnh mới
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
            'thumbnail.exists' => 'Thumbnail không hợp lệ hoặc không tồn tại.',
            'thumbnail_file.image' => 'File tải lên phải là hình ảnh.',
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
