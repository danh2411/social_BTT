<?php

namespace App\Modules\Brand\Repositories\Elasticsearch\Interfaces;

use App\Repositories\Elasticsearch\Interfaces\CoreRepository;

interface BrandRepositoryInterface extends CoreRepository
{
    public function createBrand($resource);
    public function updateBrand($resource);
}
