<?php

namespace App\Modules\Resources\Services\Interfaces;

interface ResourceServiceInterface
{
public function createResource($data);
public function listResource($data);
public function updateResource($data);
}
