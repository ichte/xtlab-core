<?php
return [
    'aliases' => [
        'translate'                                             => \Zend\I18n\View\Helper\Translate::class,


    ],

    'invokables' => [
        'pageBreadcrumbs'                                       => XT\Core\ViewHelper\Breadcrumbs\Breadcrumbs::class,
        'blockHtml'                                             => XT\Core\ViewHelper\Html\blockHtml::class,
        'enableCodeEdit'                                        => XT\Core\ViewHelper\Html\enableCodeEdit::class,
        'dropdownMenu'                                          => \XT\Core\ViewHelper\Menu\DropdownMenu::class,
        \Zend\Form\View\Helper\FormElementErrors::class         => \XT\Core\Form\Helper\FormElementErrors::class,
        \Zend\Form\View\Helper\FormElement::class               => \XT\Core\Form\Helper\FormElement::class,
        'formRowBootstrap'                                      => \XT\Core\Form\Helper\FormRowBootstrap::class,



    ],
    'factories' => [
          \Zend\I18n\View\Helper\Translate::class             => \Zend\ServiceManager\Factory\InvokableFactory::class,

//        'isGranted'                                         => \Ichte\Core\View\Helper\Factory\RbacFactory::class,
//        'pagingxt'                                          => \Ichte\Core\View\Helper\Factory\PagingFactory::class

    ]
];

