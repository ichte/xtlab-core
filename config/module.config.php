<?php
return [
    'view_manager'  =>  [
        'template_path_stack' => [
            __DIR__. '/../src/Template'

        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    'coresession' => [
        'remember_me_seconds' => 604800,
        'cookie_lifetime' =>604800,
        'use_cookies' => true,
        'cookie_httponly' => true,
        'name'=>'xtlab'
    ]
];