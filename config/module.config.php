<?php
return [

    'coresession' => [
        'remember_me_seconds' => 604800,
        'cookie_lifetime' =>604800,
        'use_cookies' => true,
        'cookie_httponly' => true,
        'name'=>'xtlab'
    ],
    'view_helpers'  =>  include 'viewhelper.config.php',
    'view_manager'  =>  include 'viewmanager.config.php',
];