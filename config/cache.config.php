<?php
return  [
    'CacheCommon' => [
        'adapter' => [
            'name'    => XT\Core\Cache\Storage\Adapter\Filesystem\Filesystem::class,
            'options' => [
                'dirLevel' => 0,
                'dirPermission' => 0755,
                'filePermission' => 0666,
                'namespaceSeparator' => '-',
                'cache_dir' => './data/cache',
                'namespace' => 'xtcache-102592000',
                'ttl' => 102592000
            ],
        ],
        'plugins' => [
            [
                'name' => 'serializer',
                'options' => [
                ],
            ],
        ],
    ],

];


/*
  'CacheCommon' => [
        'adapter' => [
            'name'    => XT\Core\Cache\Storage\Adapter\Memcached\Memcached::class,

            'options' =>  [
                'ttl' => 86400,
                'servers' => 'localhost',
                'namespaceSeparator' => '.',
                'lib_options' => [
                    \Memcached::OPT_PREFIX_KEY => 'xtlabsitename.'
                ]

            ],
        ],
    ],
 */