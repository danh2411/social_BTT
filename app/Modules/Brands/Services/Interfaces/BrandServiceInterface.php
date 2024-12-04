<?php

namespace App\Modules\Brands\Services\Interfaces;

interface BrandServiceInterface
{
public function createBrand($data);
public function listBrand($data): array;
}
