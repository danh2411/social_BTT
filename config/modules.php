<?php
return [
    'modules' => [
        'user' => [
            'active' => true,
            'providers' => [
                \App\Modules\User\Providers\UserServiceProvider::class,
            ],
            'modules_require' => [

            ]
        ],
        'newsletter' => [
            'active' => true,
            'providers' => [
                \App\Modules\Newsletter\Providers\NewsletterServiceProvider::class,
            ],
            'modules_require' => [

            ]
        ],
        'resource' => [
            'active' => true,
            'providers' => [
                \App\Modules\Resources\Providers\ResourceServiceProvider::class,
            ],
            'modules_require' => [

            ]
        ],
        'brand' => [
            'active' => true,
            'providers' => [
                \App\Modules\Brand\Providers\BrandServiceProvider::class,
            ],
            'modules_require' => [

            ]
        ],
    ]
];
