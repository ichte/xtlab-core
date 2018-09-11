<?php
return [
    'display_not_found_reason' => true,
    'display_exceptions'       => true,
    'doctype'                  => 'HTML5',
    'not_found_template'       => 'layout/error/404.phtml',
    'exception_template'       => 'layout/error/index.phtml',
    'layout'                   => 'layout/layout.phtml',


    'template_path_stack' => [
        __DIR__. '/../src/template'

    ],
    'strategies' => [
        'ViewJsonStrategy',
    ],

];