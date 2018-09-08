<?php
return [
    'display_not_found_reason' => true,
    'display_exceptions'       => true,
    'doctype'                  => 'HTML5',
    'not_found_template'       => 'error/404',
    'exception_template'       => 'error/index',

    'template_map' => [
        'layout/layout'                 => __DIR__ . '/../src/template/layout/layout.phtml',
        'error/404'                     => __DIR__ . '/../src/template/layout/error/404.phtml',
        'error/index'                   => __DIR__ . '/../src/template/layout/error/index.phtml',

        'textarea_autogrow'             => __DIR__ . '/../src/template/form/element/textarea-autogrow.phtml',
        'text-autocomplete'             => __DIR__ . '/../src/template/form/element/text-autocomplete.phtml',
    ],

    'template_path_stack' => [
        __DIR__. '/../src/template'

    ],
    'strategies' => [
        'ViewJsonStrategy',
    ],

];