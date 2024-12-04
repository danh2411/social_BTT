<?php
namespace App\Modules\Newsletter\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateNewsletterRequest extends FormRequest
{
    /**
     * Xác định quyền người dùng cho request này.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Cho phép tất cả người dùng sử dụng request này
    }

    /**
     * Quy tắc xác thực cho request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'   => 'required|integer|exists:users,id', // Phải là integer và tồn tại trong bảng users
            'title'     => 'required|string|max:255', // Bắt buộc, chuỗi, tối đa 255 ký tự
            'content'   => 'required|string', // Bắt buộc, chuỗi
            'tags'      => 'nullable|string', // Tùy chọn, chuỗi
            'thumbnail' => 'nullable|string|url', // Tùy chọn, chuỗi, phải là URL hợp lệ
            'location'  => 'nullable|string|max:255', // Tùy chọn, chuỗi, tối đa 255 ký tự
            'creator'   => 'nullable|string|max:255', // Tùy chọn, chuỗi, tối đa 255 ký tự
            'option'    => 'nullable|json', // Tùy chọn, định dạng JSON
            'like'      => 'nullable|integer|min:0', // Tùy chọn, phải là số nguyên, tối thiểu là 0
            'interact'  => 'nullable|integer|min:0', // Tùy chọn, phải là số nguyên, tối thiểu là 0
            'note'      => 'nullable|string', // Tùy chọn, chuỗi
            'flag'      => 'nullable|boolean', // Tùy chọn, chỉ nhận true/false
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
