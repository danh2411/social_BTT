<?php

namespace App\Modules\Newsletter\Repositories;


use App\Modules\Newsletter\Repositories\Interfaces\NewsletterRepository as NewsletterRepositoryInterface;

use App\Repositories\Elasticsearch\CoreRepository;
use Illuminate\Support\Facades\Log;

class NewsletterRepository extends CoreRepository implements  NewsletterRepositoryInterface
{

    public function createNewsletter($newsletter): bool
    {


        try {
            $save_es = $this->create($newsletter);
            return $save_es;
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }
}
