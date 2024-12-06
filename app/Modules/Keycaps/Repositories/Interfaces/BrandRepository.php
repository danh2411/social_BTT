<?php

namespace App\Modules\Brands\Repositories\Interfaces;

use App\Repositories\Elasticsearch\Interfaces\CoreRepository;

interface BrandRepository extends CoreRepository
{
    public function createBrand($resource);
}
