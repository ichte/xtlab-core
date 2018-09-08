<?php
return [
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    

    'admin_plugins' => [
        'layout'              => XT\Core\Admin\Layout\Layout::class,
        'basiccommand'        => XT\Core\Admin\Common\BasicCommand::class
    ],
    'coresession' => [
        'remember_me_seconds' => 604800,
        'cookie_lifetime' =>604800,
        'use_cookies' => true,
        'cookie_httponly' => true,
        'name'=>'xtlab'
    ],

    'service_manager'=>[
        'factories'=>[
            \XT\Core\System\Placeholder\PlaceholderManager::class            => \XT\Core\System\Placeholder\PlaceholderManager::class,
            \XT\Core\Event\InsertHtml\InsertHtmlManager::class               => \XT\Core\Event\InsertHtml\InsertHtmlManager::class,
            \XT\Core\Event\BlockLayout\BlockLayoutManager::class             => \XT\Core\Event\BlockLayout\BlockLayoutManager::class,
            'navigation'                                                     => 'Zend\Navigation\Service\DefaultNavigationFactory',

        ],
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
    ],



    'view_helpers'       =>  include 'viewhelper.config.php',
    'view_manager'       =>  include 'viewmanager.config.php',
    'controller_plugins' =>  include 'controller_plugins.config.php',
    'log'                =>  include 'log.config.php',
    'caches'             =>  include 'cache.config.php'
];