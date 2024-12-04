<?php

namespace App\Repositories\Elasticsearch;

use App\Indexes\Interfaces\CoreIndex;
use App\Repositories\Elasticsearch\Interfaces\CoreRepository as CoreRepositoryInterface;
use paz\Elasticsearch\Repositories\ElasticsearchRepositoryAbstract;

class CoreRepository extends ElasticsearchRepositoryAbstract implements CoreRepositoryInterface
{
    public function __construct(CoreIndex $index)
    {
        parent::__construct($index);
    }
}
