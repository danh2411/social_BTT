<?php

namespace App\Modules\Resources\Repositories\Elasticsearch\Interfaces;

use App\Repositories\Elasticsearch\Interfaces\CoreRepository;

interface ESResourceRepository extends CoreRepository
{
    public function createResource($resource);
}
