<?php

namespace App\Indexes;

class Brands extends CoreIndex
{

    public function __construct()
    {
        return parent::__construct('id'); // ID của mỗi document
    }

    public function index(): string
    {
        return 'brands'; // Tên index trong Elasticsearch
    }

    public function mapping(): array
    {
        return [
            'brand_id' => [
                'type' => 'keyword', // ID duy nhất
            ],
            'name' => [
                'type' => 'keyword', // Loại đối tượng liên kết, không cần phân tích
            ],
            'thumbnail' => [
                'type' => 'text', // Đường dẫn tài nguyên, có thể cần phân tích nếu tìm kiếm
                'analyzer' => 'standard',
            ],
            'description' => [
                'type' => 'text', // Mô tả tài nguyên, cần phân tích
                'analyzer' => 'standard', // Bộ phân tích mặc định
            ],
            'date_released' => [
                'type' => 'keyword', // Loại đối tượng liên kết, không cần phân tích
            ],
//
            'created_at' => [
                'type' => 'keyword', // Loại đối tượng liên kết, không cần phân tích
            ],
            'updated_at' => [
                'type' => 'keyword', // Loại đối tượng liên kết, không cần phân tích
            ],
        ];
    }


}
