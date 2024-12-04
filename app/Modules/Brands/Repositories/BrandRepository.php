<?php

namespace App\Modules\Brands\Repositories;


use App\Modules\Brands\Repositories\Interfaces\BrandRepository as BrandRepositoryInterface;
use App\Repositories\Elasticsearch\CoreRepository;
use Illuminate\Support\Facades\Log;

class BrandRepository extends CoreRepository implements  BrandRepositoryInterface
{

    public function createBrand($resource): array
    {


        try {
            $save_es = $this->createResponseID($resource);
            return !empty($save_es['_id'])?['success' =>true,'id'=> $save_es['_id']]:['success' => false];
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return ['success' => false];
        }
    }
}
