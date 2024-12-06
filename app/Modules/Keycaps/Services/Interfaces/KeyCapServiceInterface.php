<?php

namespace App\Modules\Brands\Services\Interfaces;

interface KeyCapServiceInterface
{
public function createBrand($data);
public function listBrand($data): array;
}
