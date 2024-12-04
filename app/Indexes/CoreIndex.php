<?php

namespace App\Indexes;

use paz\Elasticsearch\Indexes\IndexAbstract;
use App\Indexes\Interfaces\CoreIndex as CoreIndexInterface;

abstract class CoreIndex extends IndexAbstract implements CoreIndexInterface
{
    /**
     * Từ khóa tùy chỉnh cho từng đối tượng tìm kiếm.
     */
    protected array $customKeywords = [];

    /**
     * Đặt từ khóa tùy chỉnh.
     */
    public function setCustomKeywords(array $keywords): self
    {
        $this->customKeywords = $keywords;
        return $this;
    }

    /**
     * Cấu hình cho index.
     */
    public function settings(): array
    {
        return [
            'number_of_shards' => 5,
            'number_of_replicas' => 3,
            'analysis' => [
                'filter' => [
                    'vietnamese_stop' => [
                        'type' => 'stop',
                        'stopwords' => '_vietnamese_', // Bộ từ dừng cho tiếng Việt
                    ],
                    'vietnamese_keywords' => [
                        'type' => 'keyword_marker',
                        'keywords' => $this->customKeywords, // Sử dụng từ khóa tùy chỉnh
                    ],
                    'icu_folding' => [
                        'type' => 'icu_folding', // Dùng để chuẩn hóa Unicode, thay thế stemmer
                    ]
                ],
                'analyzer' => [
                    'vietnamese_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard', // Tokenizer tiêu chuẩn
                        'filter' => [
                            'lowercase', // Chuyển tất cả chữ về dạng thường
                            'icu_folding', // Chuẩn hóa Unicode (dấu, ký tự đặc biệt)
                            'vietnamese_stop', // Loại bỏ các từ dừng
                        ]
                    ]
                ]
            ]
        ];
    }
}
