<?php
return [
    'xtlab_err_log' => [
        'writers' => [
            [
                'name' => 'db',
                'priority' => Zend\Log\Logger::DEBUG,
                'options' => [
                    'db' =>  Zend\Db\Adapter\Adapter::class,
                    'table' => 'log',
                    'column'=> [
                        'timestamp' => 'date',
                        'priority'  => 'type',
                        'message'   => 'event',

                    ],
                    'formatter' => [
                        'name' => Zend\Log\Formatter\Db::class,
                        'options' => [
                            'dateTimeFormat'=>'Y-m-d H:i:s'
                        ]
                    ]

                ],
            ],
        ],
    ],
];

//
//
//
//return [
//    'xtlab_err_log' => [
//        'writers' => [
//            [
//                'name' => 'stream',
//                'priority' => Zend\Log\Logger::DEBUG,
//                'options' => [
//                    'stream' =>'data/xtlab_err.log',
//                    /*
//                    'formatter' => [
//                        'name' => 'MyFormatter',
//                    ],
//                    */
//                    /*
//                    'filters' => [
//                        [
//                            'name' => 'MyFilter',
//                        ],
//                    ],
//                    */
//                ],
//            ],
//        ],
//    ],
//];