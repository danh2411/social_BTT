<?php

namespace paz\Elasticsearch\Repositories;

use paz\Elasticsearch\Indexes\IndexInterface;

abstract class ElasticsearchRepositoryAbstract implements ElasticsearchRepositoryInterface
{

    public function __construct(protected IndexInterface $index)
    {
    }

    public function create(array $data): bool
    {
        return $this->index->insert($data);
    }

    public function createResponseID(array $data): array
    {
        return $this->index->insertResponseID($data);
    }

    public function searchPaginationMustNot(array $query): array
    {
        $params = [

            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $query['must'],
                        'must_not' => $query['must_not'] ?? []
                    ]
                ],
            ]
        ];

        $items = $this->index->search($params);
        $i = 0;

        $hits = count($items['data']);

        while ($i < $hits) {
            $result[$i] = $items['data'][$i]['_source'];
            $i++;
        }

        if (!empty($result)) {

            $return['list'] = $result;
            return $return;
        }
        return [];
    }

    public function searchPagination(array $query): array
    {
        // Tạo params cho Elasticsearch
        $params = [
            'body' => [
                'from' => ($query['page'] - 1) * $query['number'], // Tính offset
                'size' => $query['number'], // Số lượng phần tử mỗi trang
                'query' => [
                    'bool' => [
                        'filter' => $query['filter'] ?? [],
                        'must' => $query['must'] ?? [],
                        'must_not' => $query['must_not'] ?? [],
                    ]
                ],
                'sort' => $query['sort'] ?? []
            ]
        ];

        // Gửi request tới Elasticsearch
        $items = $this->index->search($params);

        // Lấy tổng số item và kết quả
        $hits = $items['data'] ?? [];
        $totalItems = $items['total'] ?? 0;

        // Tính toán tổng số trang
        $totalPages = intval (ceil($totalItems / $query['number']));

        // Lấy danh sách kết quả
        $results = array_map(fn($item) => $item['_source'], $hits);

        if (!empty($results)) {
            // Chuẩn bị dữ liệu trả về
            return [
                'list' => $results,
                'meta' => [
                    'current_page' => $query['page'], // Trang hiện tại
                    'per_page' => $query['number'], // Số phần tử mỗi trang
                    'total_pages' => $totalPages, // Tổng số trang
                    'total_items' => $totalItems, // Tổng số phần tử
                    'from' => ($query['page'] - 1) * $query['number'] + 1, // Item bắt đầu
                    'to' => ($query['page'] - 1) * $query['number'] + count($hits), // Item kết thúc
                ]
            ];
        }
        return [];
    }


    public function createWithOptype(array $data): bool
    {
        return $this->index->insertWitOptype($data);
    }

    public function updateOrCreate(array $data, array $conditions = []): bool
    {
        return $this->index->updateOrInsert($data, $conditions);
    }

    public function update(array $data): bool
    {
        return $this->index->update($data);
    }

    public function delete(array $data): bool
    {
        return $this->index->delete($data);
    }

    public function search(array $query): array
    {
        return $this->index->search($query);
    }

    public function count(array $query): int
    {
        return $this->index->count($query);
    }

    public function count_unique(array $query): array
    {
        return $this->index->count_unique($query);
    }

    public function find(string|int $id): ?array
    {
        $params = [
            'body' => [
                'from' => 0,
                'size' => 1,
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => [
                                    $this->index->getId() => $id
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $data = $this->index->search($params);

        return $data['data'][0]['_source'] ?? null;
    }

    public function findByAttributes(array $attributes): ?array
    {
        if (!empty($attributes)) {
            $params = [
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => []
                        ]
                    ]
                ]
            ];
            foreach ($attributes as $attribute => $value) {
                if (is_array($value))
                    $param = [
                        'terms' => [
                            $attribute => $value
                        ]
                    ];
                else
                    $param = [
                        'term' => [
                            $attribute => $value
                        ]
                    ];
                $params['body']['query']['bool']['must'][] = $param;
            }
            $this->setOffset($params, 0);
            $this->setLimit($params, 1);
            $data = $this->index->search($params);
        }
        return $data['data'][0] ?? null;
    }

    public function getByAttributes(array $attributes, int $offset = 0, int $limit = 10, array $sortAttributes = []): array
    {
        if (!empty($attributes)) {
            $query = $this->queryTermAttributes($attributes, $offset, $limit, $sortAttributes);
            $data = $this->index->search($query);
        }
        return $data ?? [];
    }

    private function setOffset(array &$query, int $offset = 0)
    {
        $query['body']['from'] = $offset;
    }

    private function setLimit(array &$query, int $size = 10)
    {
        $query['body']['size'] = $size;
    }

    private function setSorts(array &$query, array $sortAttributes)
    {
        foreach ($sortAttributes as $sortAttribute => $sortValue) {
            if (!in_array($sortValue, ['asc', 'desc']))
                $sortAttribute = 'asc';
            $query['body']['sort'] += [$sortAttribute => $sortValue];
        }
    }

    private function queryTermAttributes(array $attributes, $offset = 0, $limit = 10, $sortAttributes = []): array
    {
        $query = [];
        if (!empty($attributes)) {
            foreach ($attributes as $attribute => $value) {
                if (is_array($value))
                    $param = [
                        'terms' => [
                            $attribute => $value
                        ]
                    ];
                else
                    $param = [
                        'term' => [
                            $attribute => $value
                        ]
                    ];
                $query['body']['query']['bool']['must'][] = $param;
            }
        }
        $this->setOffset($query, $offset);
        $this->setLimit($query, $limit);
        $this->setSorts($query, $sortAttributes);
        return $query;
    }
}
