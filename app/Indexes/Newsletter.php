<?php

namespace App\Indexes;

class Newsletter extends CoreIndex
{

    public function __construct()
    {
        return parent::__construct('id');
    }

    public function index(): string
    {
        return 'newsletter';
    }

    public function mapping(): array
    {
        return [
            'id' => [
                'type' => 'keyword',
            ],
            'title' => [
                'type' => 'text',
                'analyzer' => 'vietnamese_analyzer',
            ],
            'content' => [
                'type' => 'text',
                'analyzer' => 'vietnamese_analyzer',
            ],
            'tags' => [
                'type' => 'keyword',
            ],
            'thumbnail' => [
                'type' => 'keyword',
            ],
            'location' => [
                'type' => 'text',
                'analyzer' => 'vietnamese_analyzer',
            ],
            'creator' => [
                'type' => 'keyword',
            ],
        ];
    }
}
