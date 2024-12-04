<?php

namespace App\Modules\Newsletter\Services;

use App\Modules\Newsletter\Repositories\Interfaces\NewsletterRepository;
use App\Modules\Newsletter\Services\Interfaces\NewsletterServiceInterface;
use App\Services\RabbitMQService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterService implements NewsletterServiceInterface
{
    public function __construct(
        protected NewsletterRepository $newsletterRepository,
        protected RabbitMQService $rabbitMQ,

    )
    {

    }
    public function createNewsletter($data)
    {

        $saveES=$this->newsletterRepository->create($data);
        if (!$saveES) {
            return [
                'success' => false,
                'message' => 'Newsletter not created ES',
            ];
        }
        $this->rabbitMQ->publish('newsletter_queue', [
            'action' => 'create',
            'data' => $data
        ]);
    }
}
