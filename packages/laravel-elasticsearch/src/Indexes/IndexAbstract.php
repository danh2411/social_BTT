<?php

namespace paz\Elasticsearch\Indexes;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\InvalidArgumentException;
use Elasticsearch\Common\Exceptions\RuntimeException;

abstract class IndexAbstract implements IndexInterface
{

    protected Client $currentConnection;

    private const MAX_COUNT_SEARCH = 9999;

    public function __construct(
        protected $_id = '_id',
        protected $_scroll = '1m',
        protected $_timeout = '1s'
    )
    {
        if (!config('laravel-elasticsearch.allowed_elasticsearch', false))
            throw new RuntimeException('Elasticsearch is not allowed');

        if (empty($this->server()))
            throw new InvalidArgumentException('Please provide elasticsearch SERVER config');

        if (empty($this->getIndex()))
            throw new InvalidArgumentException('Please provide elasticsearch INDEX config');

        $currentConnection = ClientBuilder::create()->setHosts([$this->server()]);
        list($username, $password) = $this->credentials();
        if (!empty($username && !empty($password)))
            $currentConnection->setBasicAuthentication($username, $password);
        $this->currentConnection = $currentConnection->build();

        if ($this->isConnected() && !$this->exists() && !empty($this->mapping()))
            $this->initMapping();
    }

    public function server(): string
    {
        return
            config('laravel-elasticsearch.connections.' . config('laravel-elasticsearch.default') . '.host')
            . ':' .
            config('laravel-elasticsearch.connections.' . config('laravel-elasticsearch.default') . '.port');
    }

    public function credentials(): array
    {
        return [
            config('laravel-elasticsearch.connections.' . config('laravel-elasticsearch.default') . '.username'),
            config('laravel-elasticsearch.connections.' . config('laravel-elasticsearch.default') . '.password')
        ];
    }

    public function prefix(): ?string
    {
        return config('laravel-elasticsearch.connections.' . config('laravel-elasticsearch.default') . '.prefix');
    }

    public function getTimeout(): string
    {
        return $this->_timeout;
    }

    public function getId(): string
    {
        return $this->_id;
    }

    public function getIndex(): string
    {
        if (!empty($this->prefix()))
            return $this->prefix() . $this->index();
        else
            return $this->index();
    }

    public function getConnection(): Client
    {
        return $this->currentConnection;
    }

    public function isConnected(): bool
    {
        if (empty($this->currentConnection)) return false;
        return empty($this->currentConnection) !== true || $this->currentConnection->ping() !== false;
    }

    public function exists(): bool
    {
        if ($this->isConnected())
            return $this->getConnection()
                ->indices()
                ->exists(['index' => $this->getIndex()]);
        else
            return false;
    }

    private function initMapping(): bool|array
    {
        if ($this->isConnected()) {
            $params = [
                'index' => $this->getIndex(),
                'body' => [
                    'mappings' => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => $this->mapping()
                    ]
                ]
            ];
            if (!empty($this->settings())) {
                $params['body']['settings'] = $this->settings();
            }
            return $this->getConnection()->indices()->create($params);
        } else {
            return false;
        }
    }

    public function search(array $params): array
    {
        if ($this->exists()) {
            if (empty($params['index']))
                $params['index'] = $this->getIndex();

            $total = $this->count($params);

            $params['body']['from'] = (int)($params['body']['from'] ?? 0);
            $params['body']['size'] = (int)($params['body']['size'] ?? $total);
            if (($params['body']['from'] + $params['body']['size']) >= self::MAX_COUNT_SEARCH) {
                $searchData = $this->scrollSearch($params);
            } else {
                $searchData = $this->getConnection()->search($params);
                $searchData = $searchData['hits']['hits'] ?? [];
            }
            $data = [];
            if (!empty($searchData)) {
                $step = 0;
                while ($step < count($searchData)) {
                    if (!empty($searchData[$step]))
                        $data[] = $searchData[$step];
                    $step++;
                }
            }
        }
        return [
            'data' => $data ?? [],
            'total' => $total ?? 0
        ];
    }

    public function searchAfter(array $params): array
    {

    }

    public function scrollSearch($params): array
    {
        if ($this->exists()) {
            if (empty($params['index']))
                $params['index'] = $this->getIndex();

            $from = (int)($params['body']['from'] ?? 0);
            $size = (int)($params['body']['size'] ?? $this->count($params));

            unset($params['body']['from'], $params['body']['size']);

            if (empty($params['timeout']))
                $params['timeout'] = $this->_timeout;

            if (empty($params['scroll']))
                $params['scroll'] = $this->_scroll;

            if ($size > self::MAX_COUNT_SEARCH) {
                $params['size'] = self::MAX_COUNT_SEARCH;
                $data = $this->scrollSearchWithSizeGreaterMaxCountSearch($params, $from, $size);
            } else {
                $params['size'] = $size;
                $data = $this->scrollSearchWithSizeLessMaxCountSearch($params, $from);
            }
        }
        return $data ?? [];
    }

    private function scrollSearchWithSizeLessMaxCountSearch($params, $from): array
    {
        $rawData = $this->getConnection()->search($params);
        $i = count($rawData['hits']['hits'] ?? []);
        if ($from == 0) {
            $data = $rawData['hits']['hits'] ?? [];
        } else {
            $scrollId = $rawData['_scroll_id'] ?? '';
            while ($i <= $from) {
                $scrollData = $this->getConnection()->scroll([
                    "scroll_id" => $scrollId,
                    "scroll" => $params['scroll']
                ]);
                $count = count($scrollData['hits']['hits'] ?? []);
                if ($count > 0)
                    $scrollId = $scrollData['_scroll_id'] ?? '';
                else
                    break;
                $i += $count;
            }
            $data = $scrollData['hits']['hits'] ?? [];
        }
        return $data;
    }

    private function scrollSearchWithSizeGreaterMaxCountSearch($params, $from, $size): array
    {
        $data = [];
        $rawData = $this->getConnection()->search($params);
        $i = count($rawData['hits']['hits'] ?? []);
        if ($from < $i)
            $data = $rawData['hits']['hits'] ?? [];
        else
            $from = $from - $i;

        $scrollId = $rawData['_scroll_id'] ?? '';
        while (true) {
            $scrollData = $this->getConnection()->scroll([
                "scroll_id" => $scrollId,
                "scroll" => $params['scroll']
            ]);
            $count = count($scrollData['hits']['hits'] ?? []);
            if ($count > 0) {
                $scrollId = $scrollData['_scroll_id'] ?? '';
                if ($from < $i)
                    $data = array_merge($data, $scrollData['hits']['hits'] ?? []);
                else
                    $from = $from - $count;
                if (($from + $size) <= count($data))
                    break;
            } else {
                break;
            }
            $i += $count;
        }

        return array_slice($data, $from, $size);
    }

    public function scrollAll($params): array
    {
        if ($this->exists()) {
            if (empty($params['index']))
                $params['index'] = $this->getIndex();

            unset($params['body']['from'], $params['body']['size']);

            if (empty($params['timeout']))
                $params['timeout'] = $this->_timeout;

            if (empty($params['scroll']))
                $params['scroll'] = $this->_scroll;

            $params['size'] = self::MAX_COUNT_SEARCH;

            $rawData = $this->getConnection()->search($params);

            $data = $rawData['hits']['hits'] ?? [];
            $total = $rawData['hits']['total']['value'] ?? 0;

            $scrollId = $rawData['_scroll_id'] ?? '';
            while (true) {
                $scrollData = $this->getConnection()->scroll([
                    "scroll_id" => $scrollId,
                    "scroll" => $params['scroll']
                ]);
                $count = count($scrollData['hits']['hits'] ?? []);
                if ($count > 0) {
                    $scrollId = $scrollData['_scroll_id'] ?? '';
                    $data = array_merge($data, $scrollData['hits']['hits'] ?? []);
                } else {
                    break;
                }
            }

        }
        return [
            'data' => $data ?? [],
            'total' => $total ?? 0
        ];
    }

    public function count(array $params): int
    {
        if ($this->exists()) {
            if (empty($params['index']))
                $params['index'] = $this->getIndex();

            if (isset($params['body']['aggs']))
                unset($params['body']['aggs']);

            if (isset($params['body']['size']))
                unset($params['body']['size']);

            if (isset($params['body']['from']))
                unset($params['body']['from']);

            if (isset($params['body']['sort']))
                unset($params['body']['sort']);

            $countData = $this->getConnection()->count($params);
            $count = $countData['count'] ?? 0;
        }
        return $count ?? 0;
    }
    public function count_unique(array $params): array
    {
        if ($this->exists()) {
            if (empty($params['index']))
                $params['index'] = $this->getIndex();

            $total = $this->count($params);

            $params['body']['from'] = (int)($params['body']['from'] ?? 0);
            $params['body']['size'] = (int)($params['body']['size'] ?? $total);

            if (($params['body']['from'] + $params['body']['size']) >= self::MAX_COUNT_SEARCH) {
                return [
                    'error' => 'Vượt quá số lượng cho phép',
                ];
            } else {

                $searchData = $this->getConnection()->search($params);
                $total= $searchData['aggregations']['unique']['value'] ?? 0;
                $searchData['hits'] = $searchData['aggregations']['hits']['buckets'] ?? [];

            }

            $size=$params['body']['aggs']['hits']['aggs']['pagination']['bucket_sort']['size']??10;
            $page=($params['body']['aggs']['hits']['aggs']['pagination']['bucket_sort']['page'])??0;

            if (!empty( $searchData['hits'])) {
                $totalItems = $total ?? 0;
                $totalPages = ceil($totalItems /$size);

                $return = [];
                $return['list'] =  $searchData['hits'];
                $return['pagination']['page'] = $page / $size + 1;
                $return['pagination']['number'] = $size;
                $return['pagination']['total_pages'] = $totalPages;
                $return['pagination']['total_items'] = $totalItems;

                return $return;
            }
        }
        return [
            'list' => [],
            'pagination' => [
                'page' => $page/ $size + 1,
                'number' =>$size,
                'total_pages' => 0,
                'total_items' => $total,
            ],
        ];
    }
    public function insert(array $data, bool $refresh = true)
    {
        if ($this->isConnected()) {
            $params = [
                'index' => $this->getIndex(),
                'timeout' => $this->getTimeout(),
                'body' => $data
            ];
            if (!empty($data[$this->getId()]))
                $params['id'] = $data[$this->getId()];
            if ($refresh === true)
                $params['refresh'] = $refresh;
            $result = $this->getConnection()->index($params);
            return !empty($result) && !empty($result['_shards']) && isset($result['_shards']['successful']) && $result['_shards']['successful'] === 1;
        } else {
            return false;
        }
    }
    public function insertResponseID(array $data, bool $refresh = true)
    {
        if ($this->isConnected()) {
            $params = [
                'index' => $this->getIndex(),
                'timeout' => $this->getTimeout(),
                'body' => $data
            ];
            if (!empty($data[$this->getId()]))
                $params['id'] = $data[$this->getId()];
            if ($refresh === true)
                $params['refresh'] = $refresh;
            $result = $this->getConnection()->index($params);
            if (!empty($result) && !empty($result['_id']) && !empty($result['_shards']) && isset($result['_shards']['successful'])
                && $result['_shards']['successful'] === 1) {
                return [
                    'success' => true,
                    '_id' => $result['_id'],
                ];
            }
            return false;
        } else {
            return false;
        }
    }


    public function insertWitOptype(array $data, bool $refresh = true)
    {
        if ($this->isConnected()) {
            $params = [
                'index' => $this->getIndex(),
                'timeout' => $this->getTimeout(),
                'op_type' => 'create',
                'body' => $data
            ];
            if (!empty($data[$this->getId()]))
                $params['id'] = $data[$this->getId()];
            if ($refresh === true)
                $params['refresh'] = $refresh;
            $result = $this->getConnection()->index($params);
            return !empty($result) && !empty($result['_shards']) && isset($result['_shards']['successful']) && $result['_shards']['successful'] === 1;
        } else {
            return false;
        }
    }

    public function update(array $data, bool $refresh = true)
    {

        if ($this->isConnected() && !empty($data[$this->getId()])) {
            $params = [
                'index' => $this->getIndex(),
                'id' => $data[$this->getId()],
            ];
            if ($this->getConnection()->exists($params)) {
                $params['body'] = [
                    'doc' => $data,
                    'doc_as_upsert' => true,
                ];
                if ($refresh === true)
                    $params['refresh'] = $refresh;

                $params['timeout'] = $this->getTimeout();

                $result = $this->getConnection()->update($params);
                return ($result['result'] === 'updated' && $result['_shards']['successful'] == 1) || $result['result'] === 'noop';
            }
        }
        return false;
    }

    public function updateByConditions(array $data, array $conditions, bool $refresh = true)
    {
        if ($this->isConnected() && !empty($conditions)) {
            $arrTerm = [];
            foreach ($conditions as $key => $value) {
                $arrTerm[] = [
                    'term' => [
                        $key => $value
                    ]
                ];
            }
            $txtData = '';
            $paramsData = [];
            foreach ($data as $keyData => $valueData) {
                $txtData .= 'ctx._source.' . $keyData . ' = params.' . $keyData . ';';
                $paramsData[$keyData] = $valueData;
            }
            $params = [
                'index' => $this->getIndex(),
                'timeout' => $this->getTimeout(),
                'body' => [
                    'script' => [
                        'source' => $txtData,
                        'lang' => 'painless',
                        'params' => $paramsData
                    ],
                    'query' => [
                        'bool' => [
                            'must' => $arrTerm
                        ]
                    ]
                ]
            ];
            if ($refresh === true)
                $params['refresh'] = $refresh;

            $result = $this->getConnection()->updateByQuery($params);
            return ($result['updated'] > 0 && empty($result['failures']));
        }
        return false;
    }

    public function updateOrInsert(array $data, array $conditions = [], bool $refresh = true)
    {
        if ($this->isConnected()) {
            if (!empty($conditions)) {
                $arrTerm = [];
                foreach ($conditions as $key => $value) {
                    $arrTerm[] = [
                        'term' => [
                            $key => $value
                        ]
                    ];
                }
                $params = [
                    'index' => $this->getIndex(),
                    'body' => [
                        'query' => [
                            'bool' => [
                                'must' => $arrTerm
                            ]
                        ]
                    ]
                ];

                if ($this->count($params) > 0)
                    return $this->updateByConditions($data, $conditions, $refresh);
            }

            if (!empty($data[$this->getId()])) {
                $params = [
                    'index' => $this->getIndex(),
                    'id' => $data[$this->getId()],
                ];
                if ($this->getConnection()->exists($params))
                    return $this->update($data, $refresh);
            }
            return $this->insert($data, $refresh);
        }
        return false;
    }

    public function delete(array $data, bool $refresh = true)
    {
        if ($this->isConnected()) {
            $params = [
                'index' => $this->getIndex(),
            ];

            if ($refresh === true)
                $params['refresh'] = $refresh;

            if (!empty($data[$this->getId()])) {
                $params['id'] = $data[$this->getId()];
                if ($this->getConnection()->exists($params)) {
                    $params['timeout'] = $this->getTimeout();
                    $result = $this->getConnection()->delete($params);
                }
            } else if (!empty($data['body'])) {
                $params['body'] = $data['body'];
                $params['conflicts'] = 'proceed';
                $result = $this->getConnection()->deleteByQuery($params);

            }
            return (!empty($result) && (($result['result'] === 'deleted' && $result['_shards']['successful'] == 1) || $result['result'] === 'noop'));
        }
        return false;
    }

}
