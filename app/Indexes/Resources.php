<?php

namespace App\Indexes;

class Resources extends CoreIndex
{

    public function __construct()
    {
        return parent::__construct('id'); // ID của mỗi document
    }

    public function index(): string
    {
        return 'resources'; // Tên index trong Elasticsearch
    }

    public function mapping(): array
    {
        return [
            'resource_id' => [
                'type' => 'keyword', // Loại tài nguyên (thumbnail, avatar, etc.), không cần phân tích
            ],
            'type' => [
                'type' => 'keyword', // Loại tài nguyên (thumbnail, avatar, etc.), không cần phân tích
            ],
            'resourceable_id' => [
                'type' => 'keyword', // Đường dẫn tài nguyên, không cần phân tích
            ],
            'resourceable_type' => [
                'type' => 'keyword', // Loại đối tượng liên kết, không cần phân tích
            ],
            'name' => [
                'type' => 'keyword', // Đường dẫn tài nguyên, không cần phân tích
            ],
            'path' => [
                'type' => 'keyword', // Đường dẫn tài nguyên, không cần phân tích
            ],
            'status' => [
                'type' => 'keyword', // Dữ liệu bổ sung, có thể là một JSON object
            ],
            'description' => [
                'type' => 'text', // Mô tả tài nguyên, cần phân tích
                'analyzer' => 'vietnamese_analyzer', // Sử dụng bộ phân tích tiếng Việt
            ],
            'meta_data' => [
                'type' => 'object', // Dữ liệu bổ sung, có thể là một JSON object
            ],
            'option' => [
                'type' => 'keyword', // Dữ liệu bổ sung, có thể là một JSON object
            ],
            'created_at' => [
                'type' => 'keyword', // Đường dẫn tài nguyên, không cần phân tích
            ],
            'updated_at' => [
                'type' => 'keyword', // Đường dẫn tài nguyên, không cần phân tích
            ],
        ];
    }

}
