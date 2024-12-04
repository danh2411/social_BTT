<?php
return [
    'required' => ':attribute không được để trống.',
    'exists' => ':attribute không tồn tại trong hệ thống.',
    'email' => ':attribute phải là một địa chỉ email hợp lệ.',
    'unique' => ':attribute đã tồn tại.',
    'string' => ':attribute phải là chuỗi.',
    'max' => [
        'string' => ':attribute không được vượt quá :max ký tự.',
    ],
    'min' => [
        'string' => ':attribute phải có ít nhất :min ký tự.',
    ],
    'url' => ':attribute phải là một URL hợp lệ.',
    'confirmed' => ':attribute không khớp với xác nhận.',




    /*
       |--------------------------------------------------------------------------
       | Custom Validation Attributes
       |--------------------------------------------------------------------------
       |
       | The following language lines are used to swap our attribute placeholder
       | with something more reader friendly such as "E-Mail Address" instead
       | of "email". This simply helps us make our message more expressive.
       |
       */
    'attributes' => [
        'id' => 'ID người dùng',
        'name' => 'Tên',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'phone' => 'Số điện thoại',
        'avatar_url' => 'URL ảnh đại diện',
        'address' => 'Địa chỉ',
    ],

];


