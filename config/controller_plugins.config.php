<?php
return [

    'invokables' => [
        'isGranted'         => \XT\Core\Controller\Plugin\isGranted::class,
        'askBeforeDone'     => \XT\Core\Controller\Plugin\askBeforeDone::class,
        'isConfirm'         => \XT\Core\Controller\Plugin\isConfirm::class,
        'setBlockView'      => \XT\Core\Controller\Plugin\BlockView::class

    ]
];