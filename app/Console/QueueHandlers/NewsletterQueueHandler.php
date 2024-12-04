<?php

namespace App\Console\QueueHandlers;

use App\Modules\Newsletter\Models\Newsletter;

class NewsletterQueueHandler
{
    protected $actionMap = [
        'create' => 'handleCreate',
        'update' => 'handleUpdate',
        'delete' => 'handleDelete',
    ];
    public function handle( $data)
    {

        if (!isset($data['action']) || !isset($this->actionMap[$data['action']])) {
            Log::warning('Unknown or missing action: ' . json_encode($data));
            return;
        }

        $method = $this->actionMap[$data['action']];
        $this->$method($data['data']);
    }

    protected function handleCreate(array $data) {

        $newsletter = new Newsletter();
        $newsletter->fill($data);
        $newsletter->save();
    }
    protected function handleUpdate(array $data) {

    }
    protected function handleDelete(array $data) { /* ... */ }

}
