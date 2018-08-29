<?php
namespace XT\Core;

use Zend\View\HelperPluginManager;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\ServiceManager\ServiceManager;

class Common
{
    /**
     * @var ServiceManager
     */
    public static $sm;




    /**
     * @return HelperPluginManager
     */
    public static function getViewHelper()
    {
        return self::$sm->get('ViewHelperManager');
    }

    /**
     * @return TemplateMapResolver
     */
    public static function getTemplateMapResolver()
    {
        return self::$sm->get(TemplateMapResolver::class);
    }

    /***
     * @return TemplatePathStack
     */
    public static function getTemplatePathStack()
    {
       return self::$sm->get(TemplatePathStack::class);
    }

    public static function builResolver()
    {
        $resolver = new AggregateResolver();
        return $resolver->attach(self::getTemplateMapResolver())
            ->attach(self::getTemplatePathStack());

    }


}