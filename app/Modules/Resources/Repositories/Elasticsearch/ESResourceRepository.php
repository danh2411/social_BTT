<?php

namespace App\Modules\Resources\Repositories\Elasticsearch;


use App\Modules\Resources\Repositories\Elasticsearch\Interfaces\ESResourceRepository as ResourceRepositoryInterface;
use App\Repositories\Elasticsearch\CoreRepository;
use Illuminate\Support\Facades\Log;

class ESResourceRepository extends CoreRepository implements  ResourceRepositoryInterface
{

    public function createResource($resource): array
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
