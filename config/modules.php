<?php
return [
    'modules' => [
        'user' => [
            'active' => true,
            'providers' => [
               \App\Modules\User\Providers\UserServiceProvider::class,
            ],
            'modules_require'=> [

            ]
        ],
        ]
    ];
