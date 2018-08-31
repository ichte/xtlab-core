<?php
namespace XT\Core\Common;

use Zend\View\HelperPluginManager;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\ServiceManager\ServiceManager;

class Common
{

    use ViewHelperHeader;
    use ViewHelperPhpRender;
    /**
     * @var ServiceManager
     */
    public static $sm;
    /**
     * @var \Zend\EventManager\EventManager
     */
    public static $em;

    /**
     * @var Zend\Mvc\Application
     */
    public static $app;
    
    /**
     * @return \Zend\Http\PhpEnvironment\Request
     */
    public static function getRequest()
    {
        return self::$sm->get('request');
    } 
}