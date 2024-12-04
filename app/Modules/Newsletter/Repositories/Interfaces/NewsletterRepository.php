<?php

namespace App\Modules\Newsletter\Repositories\Interfaces;

use App\Repositories\Elasticsearch\Interfaces\CoreRepository;

interface NewsletterRepository extends CoreRepository
{
    public function createNewsletter($newsletter);
}
