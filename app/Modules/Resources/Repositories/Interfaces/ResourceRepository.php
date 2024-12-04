<?php

namespace App\Modules\Resources\Repositories\Interfaces;

use App\Repositories\Elasticsearch\Interfaces\CoreRepository;

interface ResourceRepository extends CoreRepository
{
    public function createResource($resource);
}
