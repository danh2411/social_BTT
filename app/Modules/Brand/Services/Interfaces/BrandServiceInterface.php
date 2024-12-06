<?php

namespace App\Modules\Brand\Services\Interfaces;

interface BrandServiceInterface
{
    public function createBrand($data);

    public function updateBrand($id, $data);

    public function listBrand($data): array;
}
